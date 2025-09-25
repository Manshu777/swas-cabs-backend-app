<?php

namespace App\Http\Controllers;

use App\Models\RegUsers as User;
use App\Models\RegRiders as Driver;
use App\Models\RiderDocuments as DriverDocument;
use App\Models\VehicleDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{


    public function userRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:reg_users',
            'phone' => 'required|string|unique:reg_users',
            'gender' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'emergency_contacts' => 'nullable|json'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
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
        ]);

        // Send OTP (placeholder for Text SMS API)
        // dispatch(SendOtpJob::class, $user->phone);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
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