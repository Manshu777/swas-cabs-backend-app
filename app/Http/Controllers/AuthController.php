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


use Twilio\Rest\Client ;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    private $apiKey;
    private $apiSecret;
    protected Client $twilio;
   

    public function __construct()
    {
        $this->twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.auth_token')
        );
    }
      
     public function sendTwilioOtp(Request $request)
    {
        $request->validate([
            'phone'   => 'required|string|regex:/^\+?[1-9]\d{9,14}$/',
            'channel' => 'sometimes|in:sms,call',
        ]);

        $phone   = $request->input('phone');
        $channel = $request->input('channel', 'sms');

        try {
            $verification = $this->twilio->verify->v2
                ->services(config('services.twilio.verify_sid'))
                ->verifications
                ->create($phone, $channel);

            return response()->json([
                'message' => 'OTP sent successfully',
                'phone'   => $phone,
                'channel' => $channel,
                'sid'     => $verification->sid,
                'status'  => $verification->status,
            ]);
        } catch (RestException $e) {
            Log::error('Twilio Verify send failed', [
                'phone'   => $phone,
                'channel' => $channel,
                'code'    => $e->getCode(),
                'error'   => $e->getMessage(),
            ]);

            $message = $e->getCode() == 20404
                ? 'Invalid phone number or service not active.'
                : 'Failed to send OTP';

            return response()->json(['message' => $message], 500);
        } catch (\Exception $e) {
            Log::error('Unexpected error in send OTP', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }

    /**
     * Verify OTP
     */
    public function verifyTwilioOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|regex:/^\+?[1-9]\d{9,14}$/',
            'otp'   => 'required|digits:6',
        ]);

        $phone = $request->input('phone');
        $otp   = $request->input('otp');

        try {
            $check = $this->twilio->verify->v2
                ->services(config('services.twilio.verify_sid'))
                ->verificationChecks
                ->create([
                    'to'   => $phone,
                    'code' => $otp,
                ]);

            if ($check->status === 'approved') {
                // Mark phone as verified for 2 hours
                Cache::put("verified_phone:{$phone}", true, now()->addHours(2));

                return response()->json([
                    'message' => 'Phone verified successfully',
                    'phone'   => $phone,
                ]);
            }

            return response()->json(['message' => 'Invalid OTP'], 400);
        } catch (RestException $e) {
            Log::error('Twilio Verify check failed', [
                'phone' => $phone,
                'otp'   => $otp,
                'code'  => $e->getCode(),
                'error' => $e->getMessage(),
            ]);

            if ($e->getCode() == 20404) {
                return response()->json([
                    'message' => 'No active verification found. Please request a new OTP.',
                ], 400);
            }

            return response()->json([
                'message' => 'Verification failed',
                'error'   => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('Unexpected error in OTP verify', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }

    /**
     * Register user after OTP verification
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'               => 'nullable|string|max:255',
            'phone'              => 'required|string',
            'email'              => 'required|email',
            'password'           => 'required|string|min:8|confirmed',
            'profile_image'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'emergency_contacts' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        Log::info('Registering user with data: ' . json_encode($request->all()));


        $phone = $request->input('phone');

        // Check if phone was verified via OTP
        // if (!Cache::has("verified_phone:{$phone}")) {
        //     return response()->json([
        //         'message' => 'Phone number not verified with OTP. Please complete OTP verification.',
        //     ], 403);
        // }

        // Handle profile image
        $profilePath = null;
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            if ($file->isValid()) {
                $profilePath = $file->store('profile_images', 'public');
            }
        }

        // Create user
        $user = User::create([
            'name'               => $request->name,
            'phone'              => $phone,
            'email'              => $request->email,
            'password'           => Hash::make($request->password),
            'profile_image'      => $profilePath,
            'emergency_contacts' => $request->emergency_contacts,
            'kyc_status'         => 'none',
            'role'               => 'user',
            'is_verified'        => true,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        // Clean up verification flag
        Cache::forget("verified_phone:{$phone}");

        return response()->json([
            'message' => 'User registered successfully',
            'user'    => $user->makeHidden(['email_verified_at', 'created_at', 'updated_at']),
            'token'   => $token,
        ], 201);
    }

    /**
     * Login with email & password
     */
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
            'user'    => $user->makeHidden(['email_verified_at', 'created_at', 'updated_at']),
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

   

     // app/Http/Controllers/AuthController.php
public function becomeDriver(Request $request)
{
    $user = $request->user();

    // if ($user->role === 'driver') {
    //     return response()->json(['message' => 'Already a driver'], 400);
    // }
    

    $validator = Validator::make($request->all(), [
        'license_number'       => 'required|string',
        'vehicle_rc_number'    => 'required|string',
        'insurance_number'     => 'required|string',
        'brand'                => 'required|string',
        'model'                => 'required|string',
        'vehicle_type'         => 'required|string',
        'license_plate'        => 'required|string',
        'year'                 => 'required|string',
        'color'                => 'required|string',

        // Files
    'license_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    'license_back_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    'vehicle_rc_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    'insurance_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    'police_verification_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ]);


     Log::warning("Validation failed", [
        'input'  => $request->all()
    ]);



     if ($validator->fails()) {

    Log::warning("Validation failed", [
        'errors' => $validator->errors(),
        'input'  => $request->all()
    ]);

    return response()->json($validator->errors(), 422);
}
    // Handle file uploads
    $upload = function ($field) use ($request) {
        return $request->hasFile($field) && $request->file($field)->isValid()
            ? $request->file($field)->store('driver_documents', 'public')
            : null;
    };

    $licenseImage = $upload('license_image');
    $rcImage      = $upload('vehicle_rc_image');
    $insImage     = $upload('insurance_image');
    $policeImage  = $upload('police_verification_image');

    // Update user
    $user->update([
        'role' => 'driver',
        'is_verified' => false,
    ]);

    // Save documents
    RiderDocuments::create([
        'user_id' => $user->id,
        'license_number' => $request->license_number,
        'license_image'  => $licenseImage,
        'vehicle_rc_number' => $request->vehicle_rc_number,
        'vehicle_rc_image'  => $rcImage,
        'insurance_number' => $request->insurance_number,
        'insurance_image'  => $insImage,
        'police_verification_image' => $policeImage,
        'status' => 'pending',
    ]);

    // Save vehicle
    VehicleDetails::create([
        'driver_id'     => $user->id,
        'brand'         => $request->brand,
        'model'         => $request->model,
        'license_plate' => $request->license_plate,
        'vehicle_type'  => $request->vehicle_type,
        'year'          => $request->year,
        'color'         => $request->color,
    ]);

    return response()->json([
        'message' => 'Driver profile created. Awaiting verification.',
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