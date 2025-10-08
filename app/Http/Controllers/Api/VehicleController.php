<?php

   namespace App\Http\Controllers;

   use App\Models\VehicleDetail;
   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Auth;

   class VehicleController extends Controller
   {
       public function store(Request $request)
       {
           $validated = $request->validate([
               'brand' => 'required|string',
               'model' => 'required|string',
               'license_plate' => 'required|string|unique:vehicle_details,license_plate',
               'vehicle_type' => 'required|string',
               'year' => 'required|integer|min:1900|max:' . date('Y'),
               'color' => 'required|string',
           ]);

           $vehicle = VehicleDetail::create([
               'driver_id' => Auth::id(),
               'brand' => $validated['brand'],
               'model' => $validated['model'],
               'license_plate' => $validated['license_plate'],
               'vehicle_type' => $validated['vehicle_type'],
               'year' => $validated['year'],
               'color' => $validated['color'],
           ]);

           return response()->json(['message' => 'Vehicle added', 'vehicle' => $vehicle], 201);
       }

       public function index()
       {
           $vehicles = Auth::user()->vehicles;
           return response()->json($vehicles);
       }

       public function update(Request $request, VehicleDetail $vehicle)
       {
           if ($vehicle->driver_id !== Auth::id()) {
               return response()->json(['message' => 'Unauthorized'], 403);
           }

           $validated = $request->validate([
               'brand' => 'string',
               'model' => 'string',
               'license_plate' => 'string|unique:vehicle_details,license_plate,' . $vehicle->id,
               'vehicle_type' => 'string',
               'year' => 'integer|min:1900|max:' . date('Y'),
               'color' => 'string',
           ]);

           $vehicle->update($validated);
           return response()->json(['message' => 'Vehicle updated', 'vehicle' => $vehicle]);
       }

       public function destroy(VehicleDetail $vehicle)
       {
           if ($vehicle->driver_id !== Auth::id()) {
               return response()->json(['message' => 'Unauthorized'], 403);
           }

           $vehicle->delete();
           return response()->json(['message' => 'Vehicle deleted']);
       }
   }