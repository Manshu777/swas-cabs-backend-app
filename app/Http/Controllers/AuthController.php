<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RiderDocuments as DriverDocument;
use App\Models\VehicleDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    private $apiKey;
    private $apiSecret;

    public function __construct()
    {
        $this->apiKey = env('AADHAAR_SANDBOX_KEY');
        $this->apiSecret = env('AADHAAR_SANDBOX_SECRET');
    }

    public function generateAadhaarOtp(Request $request)
    {
        $request->validate([
            'aadhaar_number' => 'required|string|size:12',
        ]);

        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                return response()->json(['message' => 'Failed to get access token'], 401);
            }

            Log::info('Access Token: ' . $accessToken);

            $headers = [
                'accept' => 'application/json',
                'authorization' => $accessToken,
                'x-api-key' => $this->apiKey,
                'x-api-version' => '2.0',
                'content-type' => 'application/json',
            ];

            $payload = [
                '@entity' => 'in.co.sandbox.kyc.aadhaar.okyc.otp.request',
                'reason' => 'for kyc',
                'consent' => 'y',
                'aadhaar_number' => $request->aadhaar_number,
            ];

            $otpResponse = Http::withHeaders($headers)
                ->post('https://api.sandbox.co.in/kyc/aadhaar/okyc/otp', $payload);

            Log::info('OTP Response:', $otpResponse->json());

            if ($otpResponse->failed()) {
                return response()->json([
                    'message' => 'Failed to generate Aadhaar OTP',
                    'error' => $otpResponse->json(),
                ], 400);
            }

            $otpData = $otpResponse->json();

            return response()->json([
                'message' => 'OTP sent successfully to Aadhaar-linked mobile number',
                'txn_id' => $otpData['data']['txn_id'] ?? null,
                'response' => $otpData,
            ]);
        } catch (\Exception $e) {
            Log::error('Aadhaar OTP Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function getAccessToken()
    {
        $tokenData = Cache::get('sandbox_access_token');

        if ($tokenData && isset($tokenData['access_token'], $tokenData['expires_at'])) {
            if (now()->lt($tokenData['expires_at'])) {
                return $tokenData['access_token'];
            }
        }

        $authResponse = Http::withHeaders([
            'accept' => 'application/json',
            'x-api-key' => $this->apiKey,
            'x-api-secret' => $this->apiSecret,
            'x-api-version' => '2.0',
        ])->post('https://api.sandbox.co.in/authenticate');

        if ($authResponse->failed()) {
            Log::error('Sandbox Auth Failed:', $authResponse->json());
            return null;
        }

        $authData = $authResponse->json();
        $accessToken = $authData['data']['access_token'] ?? null;
        $timestamp = $authData['timestamp'] ?? now()->timestamp;

        if (!$accessToken) {
            Log::error('No access token found in Sandbox auth response:', $authData);
            return null;
        }

        $expiresAt = now()->addHours(24);
        Cache::put('sandbox_access_token', [
            'access_token' => $accessToken,
            'timestamp' => $timestamp,
            'expires_at' => $expiresAt,
        ], $expiresAt);

        Log::info('New Sandbox Access Token cached at ' . now() . ' with expiry ' . $expiresAt);

        return $accessToken;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'aadhaar_number' => [
                'required',
                'string',
                'size:12'
            ],
            'aadhaar_otp' => 'required|string|size:6',
            'email' => [
                'required',
                'email'
            ],
            'password' => 'required|string|min:8|confirmed',
            'phone' => [
                'nullable',
                'string'
            ],
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'emergency_contacts' => 'nullable|json',
            'reference_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Verify Aadhaar OTP
        $token = $this->getAccessToken();
        if (!$token) {
            return response()->json(['message' => 'Token fetch failed'], 401);
        }

        $headers = [
            'accept' => 'application/json',
            'authorization' => $token,
            'x-api-key' => $this->apiKey,
            'x-api-version' => '2.0',
            'content-type' => 'application/json',
        ];

        $payload = [
            '@entity' => 'in.co.sandbox.kyc.aadhaar.okyc.request',
            'reference_id' => $request->reference_id,
            'otp' => $request->aadhaar_otp,
        ];

        $response = Http::withHeaders($headers)->post('https://api.sandbox.co.in/kyc/aadhaar/okyc/otp/verify', $payload);
        if ($response->failed()) {
            return response()->json(['message' => 'KYC verification failed', 'error' => $response->json()], 400);
        }

        $data = $response->json();
        if (!isset($data['data']['status']) || $data['data']['status'] !== 'VALID') {
            return response()->json(['message' => 'Invalid or expired OTP', 'response' => $data], 400);
        }

        $aadhaar = $data['data'];
        $profileImagePath = $request->hasFile('profile_image')
            ? $request->file('profile_image')->store('profile_images', 'public')
            : null;

        // Register as user with Aadhaar data
        $user = User::updateOrCreate(
            ['aadhaar_number' => $request->aadhaar_number],
            [
                'name' => $aadhaar['name'] ?? null,
                'gender' => $aadhaar['gender'] ?? null,
                'email' => $request->email,
                
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'dob' => $aadhaar['date_of_birth'] ?? null,
                'full_address' => $aadhaar['full_address'] ?? null,
                'profile_image' => $profileImagePath,
                'emergency_contacts' => $request->emergency_contacts,
                'kyc_status' => 'verified',
                'kyc_verified_at' => now(),
                'role' => 'user',
                'is_verified' => false,
            ]
        );

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully with KYC verification',
            'user' => $user,
            'aadhaar_data' => $aadhaar,
            'token' => $token,
        ], 201);
    }

    public function userLogin(Request $request)
    {
       
       $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // Find user by email
    $user = User::where('email', $request->email)->first();

    // Check credentials
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid email or password'], 401);
    }

    // Generate API token
    $token = $user->createToken('auth_token')->plainTextToken;

    // Return response
    return response()->json([
        'message' => 'Login successful',
        'token' => $token,
        'user' => $user,
    ], 200);
    }

    public function becomeDriver(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'driver') {
            return response()->json(['message' => 'User is already a driver'], 400);
        }

        $validator = Validator::make($request->all(), [
            'license_number' => 'required|string',
            'license_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            // 'aadhaar_front_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            // 'aadhaar_back_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'vehicle_rc_number' => 'required|string',
            'vehicle_rc_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'insurance_number' => 'required|string',
            'insurance_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'police_verification_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'brand' => 'required|string',
            'model' => 'required|string',
            'vehicle_type' => 'required|string',
            'license_plate' => 'required|string',
            'year' => 'required|string',
            'color' => 'required|string',
        ]);

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
        return response()->json([
            'message' => 'User data fetched successfully',
            'user' => $request->user(),
        ], 200);
    }
}