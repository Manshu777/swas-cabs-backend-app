<?php

namespace App\Http\Controllers;

use App\Models\VehicleDetails;
use Illuminate\Http\Request;

class VehicleDetailsController extends Controller
{
    public function index()
    {
        return VehicleDetails::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:reg_riders,id',
            'brand' => 'required|string',
            'model' => 'required|string',
            'license_plate' => 'required|string|unique:vehicle_details,license_plate',
            'vehicle_type' => 'required|string',
            'year' => 'required|string',
            'color' => 'nullable|string',
        ]);

        $vehicle = VehicleDetails::create($validated);
        return response()->json($vehicle, 201);
    }

    public function show($id)
    {
        return VehicleDetails::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $vehicle = VehicleDetails::findOrFail($id);

        $validated = $request->validate([
            'brand' => 'sometimes|required|string',
            'model' => 'sometimes|required|string',
            'license_plate' => 'sometimes|required|string|unique:vehicle_details,license_plate,' . $vehicle->id,
            'vehicle_type' => 'sometimes|required|string',
            'year' => 'sometimes|required|string',
            'color' => 'nullable|string',
        ]);

        $vehicle->update($validated);
        return response()->json($vehicle);
    }

    public function destroy($id)
    {
        $vehicle = VehicleDetails::findOrFail($id);
        $vehicle->delete();

        return response()->json(['message' => 'Vehicle deleted successfully']);
    }
}
