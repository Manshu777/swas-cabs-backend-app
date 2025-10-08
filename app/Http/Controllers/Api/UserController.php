<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
   {
       public function register(Request $request)
       {
           $validated = $request->validate([
               'name' => 'required|string|max:255',
               'email' => 'required|email|unique:users,email',
               'phone' => 'required|string|unique:users,phone',
               'password' => 'required|string|min:8',
               'gender' => 'nullable|in:male,female,other',
               'language' => 'nullable|string',
               'emergency_contacts' => 'nullable|json',
               'profile_image' => 'nullable|image',
           ]);

           $user = User::create([
               'name' => $validated['name'],
               'email' => $validated['email'],
               'phone' => $validated['phone'],
               'password' => Hash::make($validated['password']),
               'gender' => $validated['gender'],
               'language' => $validated['language'],
               'emergency_contacts' => $validated['emergency_contacts'],
               'role' => 'passenger',
               'is_active' => true,
           ]);

           if ($request->hasFile('profile_image')) {
               $user->profile_image = $request->file('profile_image')->store('profiles', 'public');
               $user->save();
           }

           $token = $user->createToken('auth_token')->plainTextToken;

           return response()->json(['message' => 'Registered successfully', 'token' => $token, 'user' => $user], 201);
       }

       public function login(Request $request)
       {
           $validated = $request->validate([
               'email' => 'required|email',
               'password' => 'required|string',
           ]);

           if (!Auth::attempt($validated)) {
               throw ValidationException::withMessages(['email' => 'Invalid credentials']);
           }

           $user = Auth::user();
           $token = $user->createToken('auth_token')->plainTextToken;

           return response()->json(['message' => 'Logged in successfully', 'token' => $token, 'user' => $user]);
       }

       public function profile()
       {
           return response()->json(Auth::user());
       }

       public function update(Request $request)
       {
           $validated = $request->validate([
               'name' => 'string|max:255',
               'phone' => 'string|unique:users,phone,' . Auth::id(),
               'gender' => 'nullable|in:male,female,other',
               'language' => 'nullable|string',
               'emergency_contacts' => 'nullable|json',
               'profile_image' => 'nullable|image',
           ]);

           $user = Auth::user();
           $user->update($validated);

           if ($request->hasFile('profile_image')) {
               $user->profile_image = $request->file('profile_image')->store('profiles', 'public');
               $user->save();
           }

           return response()->json(['message' => 'Profile updated', 'user' => $user]);
       }
   }