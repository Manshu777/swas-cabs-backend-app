<?php

namespace App\Http\Controllers;

use App\Models\RegRiders as Driver;
use App\Models\RiderDocuments as DriverDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DriverVerificationController extends Controller
{
    public function verifyDriver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|exists:drivers,id',
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'nullable|string|required_if:status,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $driver = Driver::find($request->driver_id);
        $document = DriverDocument::where('driver_id', $driver->id)->first();

        if (!$document) {
            return response()->json(['message' => 'No documents found for driver'], 404);
        }

        $document->update([
            'status' => $request->status,
            'rejection_reason' => $request->status === 'rejected' ? $request->rejection_reason : null,
        ]);

        if ($request->status === 'approved') {
            $driver->update(['is_verified' => true]);
        }

        return response()->json([
            'message' => 'Driver verification updated',
            'driver' => $driver,
            'document' => $document
        ], 200);
    }
}