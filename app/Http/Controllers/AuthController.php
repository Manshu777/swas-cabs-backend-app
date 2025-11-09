<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RiderDocuments;
use App\Models\VehicleDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

use Twilio\Rest\Client as TwilioClient;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    private $apiKey;
    private $apiSecret;

    public function __construct()
    {
        $this->apiKey = env('AADHAAR_SANDBOX_KEY');
        $this->apiSecret = env('AADHAAR_SANDBOX_SECRET');
    }

     public function sendTwilioOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|regex:/^\+?[1-9]\d{9,14}$/',
        ]);

        $phone = $request->phone;
        $otp   = rand(100000, 999999);               // 6-digit OTP
        $expires = now()->addMinutes(5);

        Cache::put("otp:{$phone}", ['otp' => $otp, 'expires' => $expires], $expires);

        $twilio = new TwilioClient(config('services.twilio.sid'), config('services.twilio.auth_token'));

        try {
            $twilio->messages->create(
                $phone,
                [
                    'from' => config('services.twilio.from'),
                    'body' => "Your verification code is {$otp}. It expires in 5 minutes.",
                ]
            );

            return response()->json([
                'message' => 'OTP sent successfully',
                'phone'   => $phone,
            ]);
        } catch (Exception $e) {
            Log::error('Twilio OTP error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to send OTP'], 500);
        }
    }

    public function verifyTwilioOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp'   => 'required|digits:6',
        ]);

        $cacheKey = "otp:{$request->phone}";
        $data     = Cache::get($cacheKey);

        if (!$data || now()->gt($data['expires'])) {
            return response()->json(['message' => 'OTP expired or not found'], 400);
        }

        if ($data['otp'] != $request->otp) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        // OTP valid → mark phone as verified for the next step
        Cache::forget($cacheKey);
        Cache::put("verified_phone:{$request->phone}", true, now()->addHours(2));

        return response()->json(['message' => 'Phone verified successfully']);
    }

    /** ---------- REGISTER WITH TWILIO OTP ---------- **/

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'   => 'required|string|regex:/^\+?[1-9]\d{9,14}$/',
            'email'   => 'required|email|unique:users,email',
            'password'=> 'required|string|min:8|confirmed',
            'profile_image' => 'nullable|image',
            'emergency_contacts' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Verify phone via cached flag
        if (!Cache::has("verified_phone:{$request->phone}")) {
            return response()->json(['message' => 'Phone number not verified with OTP'], 403);
        }

        $profilePath = $request->hasFile('profile_image')
            ? $request->file('profile_image')->store('profile_images', 'public')
            : null;

        $user = User::create([
            'phone'              => $request->phone,
            'email'              => $request->email,
            'password'           => Hash::make($request->password),
            'profile_image'      => $profilePath,
            'emergency_contacts' => $request->emergency_contacts,
            'kyc_status'         => 'none',   // no Aadhaar now
            'role'               => 'user',
            'is_verified'        => true,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        // Clean up verification flag
        Cache::forget("verified_phone:{$request->phone}");

        return response()->json([
            'message' => 'User registered successfully',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    /** ---------- LOGIN WITH EMAIL/PASSWORD ---------- **/

    public function userLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => $user,
        ]);
    }

    /** ---------- GOOGLE OAuth ---------- **/

    // 1. Redirect to Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    // 2. Handle callback
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (Exception $e) {
            return response()->json(['message' => 'Google auth failed'], 400);
        }

        $user = User::where('email', $googleUser->email)->first();

        if (!$user) {
            // Auto-register
            $user = User::create([
                'name'         => $googleUser->name,
                'email'        => $googleUser->email,
                'google_id'    => $googleUser->id,
                'password'     => Hash::make(\Str::random(16)), // dummy
                'is_verified'  => true,
                'role'         => 'user',
            ]);
        } else {
            // Update google_id if missing
            $user->google_id = $googleUser->id;
            $user->save();
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // You can redirect to your frontend with token as query param
        $redirectUrl = config('app.frontend_url') . '?token=' . $token;
        return redirect($redirectUrl);
    }

   

    public function becomeDriver(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'driver') {
            return response()->json(['message' => 'User is already a driver'], 400);
        }
        Log::info('Becoming Driver Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'license_number' => 'required|string',
            'license_image' => 'nullable',
            // 'aadhaar_front_image' => 'nullable',
            // 'aadhaar_back_image' => 'nullable',
            'vehicle_rc_number' => 'required|string',
            'vehicle_rc_image' => 'required',
            'insurance_number' => 'required|string',
            'insurance_image' => 'required',
            'police_verification_image' => 'nullable',
            'brand' => 'required|string',
            'model' => 'required|string',
            'vehicle_type' => 'required|string',
            'license_plate' => 'required|string',
            'year' => 'required|string',
            'color' => 'required|string',
        ]);
        Log::info('Becoming Driver Request:', $validator->errors()->all());


        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update user role to driver
        $user->role = 'driver';
        $user->is_verified = false;
        $user->save();

        // Save driver documents
        $documentData = [
            'user_id' => $user->id,
            'license_number' => $request->license_number,
            'license_image' => $request->file('license_image')->store('driver_documents', 'public'),
            // 'aadhaar_number' => $user->aadhaar_number,
            // 'aadhaar_front_image' => $request->file('aadhaar_front_image')->store('driver_documents', 'public'),
            // 'aadhaar_back_image' => $request->hasFile('aadhaar_back_image')
            //     ? $request->file('aadhaar_back_image')->store('driver_documents', 'public')
            //     : null,
            'vehicle_rc_number' => $request->vehicle_rc_number,
            'vehicle_rc_image' => $request->file('vehicle_rc_image')->store('driver_documents', 'public'),
            'insurance_number' => $request->insurance_number,
            'insurance_image' => $request->file('insurance_image')->store('driver_documents', 'public'),
            'police_verification_image' => $request->hasFile('police_verification_image')
                ? $request->file('police_verification_image')->store('driver_documents', 'public')
                : null,
            'status' => 'pending',
        ];

        $documents = DriverDocument::create($documentData);

        // Save vehicle details
        $vehicleData = [
            'driver_id' => $user->id,
            'brand' => $request->brand,
            'model' => $request->model,
            'license_plate' => $request->license_plate,
            'vehicle_type' => $request->vehicle_type,
            'year' => $request->year,
            'color' => $request->color,
        ];

        VehicleDetails::create($vehicleData);

        return response()->json([
            'message' => 'Driver profile created successfully. Awaiting verification.',
            'user' => $user,
            'documents' => $documents,
        ], 201);
    }

    public function updateDriverStatus(Request $request, $id)
    {
        $request->validate([
            'is_verified' => 'required|boolean',
        ]);

        $user = User::where('role', 'driver')->find($id);

        if (!$user) {
            return response()->json(['message' => 'Driver not found'], 404);
        }

        $user->is_verified = $request->is_verified;
        $user->save();

        return response()->json([
            'message' => 'Driver verification status updated successfully',
            'user' => $user,
        ], 200);
    }

    public function getAllUsers()
    {
        $users = User::where('role', 'user')->get();
        return response()->json([
            'status' => true,
            'users' => $users,
        ], 200);
    }

    public function getAllDrivers()
    {
        $drivers = User::where('role', 'driver')->with('documents', 'vehicleDetails')->get();
        return response()->json([
            'status' => true,
            'drivers' => $drivers,
        ], 200);
    }

    public function updateContacts(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'contacts' => 'required|array',
            'contacts.*.name' => 'required|string|max:255',
            'contacts.*.phone' => 'required|string|max:20',
            'contacts.*.relation' => 'nullable|string|max:100',
        ]);

        $user->emergency_contacts = $request->contacts;
        $user->save();

        return response()->json([
            'message' => 'Emergency contacts updated successfully',
            'emergency_contacts' => $user->emergency_contacts,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Successfully logged out',
        ], 200);
    }

    public function profile(Request $request)
    {
      $user = $request->user();               // Authenticated user
    $data = [
        'message' => 'User data fetched successfully',
        'user'    => $user->only([
            'id', 'name', 'email', 'phone', 'gender',
            'profile_image', 'emergency_contacts', 'language',
            'is_active', 'full_address', 'role',
            'aadhaar_number', 'is_verified', 'is_available',
            'latitude', 'longitude', 'kyc_status', 'kyc_verified_at',
        ]),
    ];

    // -------------------------------------------------
    // 1. If the user is a DRIVER → attach extra data
    // -------------------------------------------------
    if ($user->role === 'driver') {
        // a) Driver's vehicles
        $vehicles = VehicleDetails::where('driver_id', $user->id)
            ->get();

        // b) Driver's documents (KYC)
        $documents = Riderdocuments::where('user_id', $user->id)
            ->get();

        $data['driver'] = [
            'vehicles'   => $vehicles,
            'documents'  => $documents,
            // you can add more driver-specific fields here
        ];
    }

    // -------------------------------------------------
    // 2. (Optional) If the user is a PASSENGER → attach passenger data
    // -------------------------------------------------
    // elseif ($user->role === 'passenger') { … }

    return response()->json($data, 200);

    }
}