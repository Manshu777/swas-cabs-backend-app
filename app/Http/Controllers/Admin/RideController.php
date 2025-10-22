<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RideController extends Controller
{
    public function index()
    {
        // $rides = Ride::with(['ridesAsPassenger', 'ridesAsDriver'])->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.rides.index');
    }
}
