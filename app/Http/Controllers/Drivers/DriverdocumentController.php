<?php

namespace App\Http\Controllers\Drivers;
use App\Http\Controllers\Controller;

use App\Models\Driverdocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DriverdocumentController extends Controller
{
    

  public function AddDriverDocuments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|exists:reg_riders,id',

            'license_number' => 'required|string',
            'license_image' => 'required|file|mimes:jpg,jpeg,png,pdf',

            'aadhaar_number' => 'required|string',
            'aadhaar_front_image' => 'required|file|mimes:jpg,jpeg,png,pdf',
            'aadhaar_back_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf',

            'vehicle_rc_number' => 'required|string',
            'vehicle_rc_image' => 'required|file|mimes:jpg,jpeg,png,pdf',

            'insurance_number' => 'required|string',
            'insurance_image' => 'required|file|mimes:jpg,jpeg,png,pdf',

            'police_verification_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only([
            'driver_id',
            'license_number',
            'aadhaar_number',
            'vehicle_rc_number',
            'insurance_number',
        ]);

        // Handle file uploads
        $uploadPath = 'uploads/driver_documents/';

        $data['license_image'] = $request->file('license_image')->store($uploadPath, 'public');
        $data['aadhaar_front_image'] = $request->file('aadhaar_front_image')->store($uploadPath, 'public');
        $data['aadhaar_back_image'] = $request->file('aadhaar_back_image')?->store($uploadPath, 'public');
        $data['vehicle_rc_image'] = $request->file('vehicle_rc_image')->store($uploadPath, 'public');
        $data['insurance_image'] = $request->file('insurance_image')->store($uploadPath, 'public');
        $data['police_verification_image'] = $request->file('police_verification_image')?->store($uploadPath, 'public');

        $document = Driverdocument::create($data);

        return response()->json([
            'message' => 'Driver documents uploaded successfully',
            'data' => $document
        ]);
    }



  public function rejectDocument(Request $request, $id)
{
    // Validate incoming data
    $request->validate([
        'document' => 'required|string|in:license,aadhaar,vehicle_rc,insurance,police_verification',
        'reason'   => 'required|string|max:500',
    ]);

    // Find the document record
    $document = DriverDocument::find($id);

    if (!$document) {
        return response()->json(['error' => 'Document not found'], 404);
    }

    // Reject the selected document type
    switch ($request->document) {
        case 'license':
            $document->license_status = 'rejected';
            $document->license_rejection_reason = $request->reason;
            break;

        case 'aadhaar':
            $document->aadhaar_status = 'rejected';
            $document->aadhaar_rejection_reason = $request->reason;
            break;

        case 'vehicle_rc':
            $document->vehicle_rc_status = 'rejected';
            $document->vehicle_rc_rejection_reason = $request->reason;
            break;

        case 'insurance':
            $document->insurance_status = 'rejected';
            $document->insurance_rejection_reason = $request->reason;
            break;

        case 'police_verification':
            $document->police_verification_status = 'rejected';
            $document->police_verification_rejection_reason = $request->reason;
            break;
    }

    // Save changes
    $document->save();

    return response()->json([
        'message' => ucfirst($request->document) . ' document has been rejected successfully.',
        'data' => $document
    ]);
}


public function verifiedDocument(Request $request ,$id)
{

    $request->validate([
        'document' => 'required|string|in:license,aadhaar,vehicle_rc,insurance,police_verification',
    ]);

    // Find the document record
    $document = DriverDocument::find($id);

    if (!$document) {
        return response()->json(['error' => 'Document not found'], 404);
    }

    switch ($request->document) {
        case 'license':
            $document->license_status = 'verified';
            break;

        case 'aadhaar':
            $document->aadhaar_status = 'verified';
           
            break;

        case 'vehicle_rc':
            $document->vehicle_rc_status = 'verified';
           
            break;

        case 'insurance':
            $document->insurance_status = 'verified';
           
            break;

        case 'police_verification':
            $document->police_verification_status = 'verified';
          
            break;
    }
 $document->save();

    return response()->json([
        'message' => ucfirst($request->document) . ' document has been verified successfully.',
        'data' => $document
    ]);

}



    







}
