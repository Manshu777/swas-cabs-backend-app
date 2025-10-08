<?php

   namespace App\Http\Controllers;

   use App\Models\RiderDocument;
   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Auth;

   class DocumentController extends Controller
   {
       public function store(Request $request)
       {
           $validated = $request->validate([
               'license_number' => 'required|string',
               'license_image' => 'required|image',
               'aadhaar_number' => 'required|string',
               'aadhaar_front_image' => 'required|image',
               'aadhaar_back_image' => 'required|image',
               'vehicle_rc_number' => 'required|string',
               'vehicle_rc_image' => 'required|image',
               'insurance_number' => 'required|string',
               'insurance_image' => 'required|image',
               'police_verification_image' => 'nullable|image',
           ]);

           $document = RiderDocument::create([
               'driver_id' => Auth::id(),
               'license_number' => $validated['license_number'],
               'aadhaar_number' => $validated['aadhaar_number'],
               'vehicle_rc_number' => $validated['vehicle_rc_number'],
               'insurance_number' => $validated['insurance_number'],
               'status' => 'pending',
           ]);

           foreach (['license_image', 'aadhaar_front_image', 'aadhaar_back_image', 'vehicle_rc_image', 'insurance_image', 'police_verification_image'] as $field) {
               if ($request->hasFile($field)) {
                   $document->$field = $request->file($field)->store('documents', 'public');
               }
           }

           $document->save();

           return response()->json(['message' => 'Documents submitted', 'document' => $document], 201);
       }

       public function index()
       {
           $documents = Auth::user()->documents;
           return response()->json($documents);
       }
   }