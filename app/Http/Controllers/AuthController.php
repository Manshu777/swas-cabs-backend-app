<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RegRiders as Driver;
use App\Models\RiderDocuments as DriverDocument;
use App\Models\VehicleDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

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
        // Step 1: Get access token (reuse if valid)
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            return response()->json(['message' => 'Failed to get access token'], 401);
        }

        Log::info('Access Token: ' . $accessToken);

        // Step 2: Prepare API headers and data
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

        // Step 3: Call Aadhaar OTP API
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
            'message' => 'OTP sent successfully to Aadhaar linked mobile number',
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
    // 1️⃣ Check cached token first
    $tokenData = Cache::get('sandbox_access_token');

    if ($tokenData && isset($tokenData['access_token'], $tokenData['expires_at'])) {
        if (now()->lt($tokenData['expires_at'])) {
            return $tokenData['access_token']; // Still valid
        }
    }

    // 2️⃣ Fetch new token from Sandbox
    $authResponse = Http::withHeaders([
        'accept' => 'application/json',
        'x-api-key' => $this->apiKey,
        'x-api-secret' => $this->apiSecret,
        'x-api-version' => '2.0', // ✅ fixed (was '20')
    ])->post('https://api.sandbox.co.in/authenticate');

    if ($authResponse->failed()) {
        Log::error('Sandbox Auth Failed:', $authResponse->json());
        return null;
    }

    $authData = $authResponse->json();

    // ✅ Actual access token is inside "data.access_token"
    $accessToken = $authData['data']['access_token'] ?? null;
    $timestamp = $authData['timestamp'] ?? now()->timestamp;

    if (!$accessToken) {
        Log::error('No access token found in Sandbox auth response:', $authData);
        return null;
    }

    // 3️⃣ Sandbox tokens are valid for 24 hours (customize if needed)
    $expiresAt = now()->addHours(24);

    // 4️⃣ Store in cache with timestamp
    Cache::put('sandbox_access_token', [
        'access_token' => $accessToken,
        'timestamp' => $timestamp,
        'expires_at' => $expiresAt,
    ], $expiresAt);

    Log::info('New Sandbox Access Token cached at ' . now() . ' with expiry ' . $expiresAt);

    return $accessToken;
}

public function becomeDriver(Request $request)
{
    $user = $request->user();

    $validator = Validator::make($request->all(), [
        'license_number' => 'required|string',
        'license_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
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

    // Mark user as driver
    $user->role = 'driver';
    $user->is_verified = false;
    $user->save();

    // Save driver documents
    $documentData = [
        'driver_id' => $user->id,
        'license_number' => $request->license_number,
        'license_image' => $request->file('license_image')->store('driver_documents', 'public'),
        'vehicle_rc_number' => $request->vehicle_rc_number,
        'vehicle_rc_image' => $request->file('vehicle_rc_image')->store('driver_documents', 'public'),
        'insurance_number' => $request->insurance_number,
        'insurance_image' => $request->file('insurance_image')->store('driver_documents', 'public'),
        'police_verification_image' => $request->hasFile('police_verification_image')
            ? $request->file('police_verification_image')->store('driver_documents', 'public')
            : null,
        'status' => 'pending',
    ];

    $documents = RiderDocument::create($documentData);

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
        'documents' => $documents,
    ], 201);
}





public function verifyAdharOtp(Request $request)
{
    $request->validate([
        'reference_id' => 'required',
        'otp' => 'required|string|size:6',
        'email' => 'required|email',
        'password' => 'required|min:6'
    ]);

    $token = $this->getAccessToken();
    if (!$token) return response()->json(['message' => 'Token fetch failed'], 401);

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
        'otp' => $request->otp,
    ];

    $response = Http::withHeaders($headers)->post('https://api.sandbox.co.in/kyc/aadhaar/okyc/otp/verify', $payload);
    if ($response->failed()) return response()->json(['message' => 'KYC failed', 'error' => $response->json()], 400);

    $data = $response->json();

    if (isset($data['data']['status']) && $data['data']['status'] === 'VALID') {
        $aadhaar = $data['data'];

        // ✅ Save verified user with custom password
        $user = User::updateOrCreate(
            ['adhar_number' => $aadhaar['reference_id']],
            [
                'name' => $aadhaar['name'] ?? null,
                'gender' => $aadhaar['gender'] ?? null,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'dob' => $aadhaar['date_of_birth'] ?? null,
                'full_address' => $aadhaar['full_address'] ?? null,
                'kyc_status' => 'verified',
                'kyc_verified_at' => now(),
            ]
        );

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'KYC verified successfully',
            'user' => $user,
            'aadhaar_data' => $aadhaar,
            'token' => $token,
        ]);
    }

    return response()->json(['message' => 'Invalid or expired OTP', 'response' => $data], 400);
}


   

  public function userRegister(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|unique:reg_users',
        'phone' => 'required|string|unique:reg_users',
        'gender' => 'required|string',
        'password' => 'required|string|min:8|confirmed',
        'adhar_number' => 'required|string|min:12|max:12',
        'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'emergency_contacts' => 'nullable|json'
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // Aadhaar Sandbox API Call (Fake Verification for Demo)
    $aadhaarResponse = Http::post('https://sandbox.aadhaarkycapi.com/api/v1/verify', [
        'aadhaar_number' => $request->adhar_number,
        'sandbox_key' => env('AADHAAR_SANDBOX_KEY')
    ]);

    if ($aadhaarResponse->failed()) {
        return response()->json(['message' => 'KYC verification failed. Please try again.'], 400);
    }

    $responseData = $aadhaarResponse->json();

    if (!isset($responseData['status']) || $responseData['status'] !== 'success') {
        return response()->json(['message' => 'Invalid Aadhaar details.'], 400);
    }

    $profileImagePath = null;
    if ($request->hasFile('profile_image')) {
        $profileImagePath = $request->file('profile_image')->store('profile_images', 'public');
    }

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'gender' => $request->gender,
        'password' => Hash::make($request->password),
        'profile_image' => $profileImagePath,
        'emergency_contacts' => $request->emergency_contacts,
        'adhar_number' => $request->adhar_number,
        'kyc_status' => 'verified',
        'kyc_verified_at' => now(),
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'User registered successfully with KYC verification',
        'user' => $user,
        'token' => $token
    ], 201);
}


    public function userLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Determine if login is email or phone
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $user = User::where($loginField, $request->login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ], 200);
    }

    // Update Emergency Contacts

    public function updateContacts(Request $request)
    {
        $user = $request->User(); // authenticated user via token

        $request->validate([
            'contacts' => 'required|array',
            'contacts.*.name' => 'required|string|max:255',
            'contacts.*.phone' => 'required|string|max:20',
            'contacts.*.relation' => 'nullable|string|max:100',
        ]);

        // Update the emergency_contacts column
        $user->emergency_contacts = $request->contacts;
        $user->save();

        return response()->json([
            'message' => 'Emergency contacts updated successfully',
            'emergency_contacts' => $user->emergency_contacts,
        ]);
    }





    public function driverRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:reg_riders',
            'phone' => 'required|string|unique:reg_riders',
            'password' => 'required|string|min:8|confirmed',
            'gender' => 'required|string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'license_number' => 'required|string',
            'license_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'aadhaar_number' => 'required|string',
            'aadhaar_front_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'aadhaar_back_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
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

        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('profile_images', 'public');
        }

        // Create driver
        $driver = Driver::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'password' => Hash::make($request->password),
            'profile_image' => $profileImagePath,
        ]);

        // Store driver documents
        $documentData = [
            'driver_id' => $driver->id,
            'license_number' => $request->license_number,
            'license_image' => $request->file('license_image')->store('driver_documents', 'public'),
            'aadhaar_number' => $request->aadhaar_number,
            'aadhaar_front_image' => $request->file('aadhaar_front_image')->store('driver_documents', 'public'),
            'aadhaar_back_image' => $request->hasFile('aadhaar_back_image')
                ? $request->file('aadhaar_back_image')->store('driver_documents', 'public')
                : null,
            'vehicle_rc_number' => $request->vehicle_rc_number,
            'vehicle_rc_image' => $request->file('vehicle_rc_image')->store('driver_documents', 'public'),
            'insurance_number' => $request->insurance_number,
            'insurance_image' => $request->file('insurance_image')->store('driver_documents', 'public'),
            'police_verification_image' => $request->hasFile('police_verification_image')
                ? $request->file('police_verification_image')->store('driver_documents', 'public')
                : null,
        ];

        DriverDocument::create($documentData);

        // Create vehicle details
        $VehicleData = [
            'driver_id' => $driver->id,
            'brand' => $request->brand,
            'model' => $request->model,
            'license_plate' => $request->license_plate,
            'vehicle_type' => $request->vehicle_type,
            'year' => $request->year,
            'color' => $request->color,

        ];

        VehicleDetails::create($VehicleData);

        // Notify admin for verification (placeholder)
        // dispatch(NotifyAdminJob::class, $driver);

        return response()->json([
            'message' => 'Driver registered, awaiting verification',
            'driver' => $driver
        ], 201);
    }

    public function driverLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Check if login input is email or phone
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $driver = Driver::where($loginField, $request->login)->first();

        if (!$driver) {
            return response()->json(['message' => 'Invalid email or phone number'], 401);
        }


        if (!Hash::check($request->password, $driver->password)) {
            return response()->json(['message' => 'Invalid password'], 401);
        }

        if (!$driver->is_verified) {
            return response()->json(['message' => 'Verified Is Pending'], 403);
        }

        $token = $driver->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'driver' => $driver
        ], 200);
    }

    // Riders Status Update to verified
    public function updateDriverStatus(Request $request, $id)
    {
        $request->validate([
            'is_verified' => 'required|boolean'
        ]);

        $driver = Driver::find($id);

        if (!$driver) {
            return response()->json(['message' => 'Driver not found'], 404);
        }

        $driver->is_verified = $request->is_verified;
        $driver->save();

        return response()->json([
            'message' => 'Driver verification status updated successfully',
            'driver' => $driver
        ], 200);
    }


    public function getAllUsers()
    {
        $users = User::all();

        return response()->json([
            'status' => true,
            'users' => $users
        ], 200);
    }

    // Get all drivers
    public function getAllDrivers()
    {
        $drivers = Driver::with('documents', 'vehicleDetails')->get();

        return response()->json([
            'status' => true,
            'drivers' => $drivers
        ], 200);
    }




    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'message' => 'User data fetched successfully',
            'user' => $request->user(),
        ]);
    }

}