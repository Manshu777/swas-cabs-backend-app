<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// use App\Models\VehicleDetail; 
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        // $units = VehicleDetail::with('user')->paginate(10);
        return view('admin.units.index');
    }
}