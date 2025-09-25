<?php

namespace App\Http\Controllers;

use App\Models\Disputes as Dispute;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'ride_id' => 'required|exists:book_rides,id',
            'issue' => 'required|string'
        ]);

        $dispute = Dispute::create([
            'ride_id' => $request->ride_id,
            'user_id' => auth()->user()->id ?? null,
            'driver_id' => auth()->guard('driver')->user()->id ?? null,
            'issue' => $request->issue
        ]);

        return response()->json(['status' => true, 'message' => 'Dispute filed successfully', 'dispute' => $dispute]);
    }

    public function resolve($id)
    {
        $dispute = Dispute::findOrFail($id);
        $dispute->status = 'resolved';
        $dispute->save();

        return response()->json(['status' => true, 'message' => 'Dispute resolved successfully']);
    }
}
