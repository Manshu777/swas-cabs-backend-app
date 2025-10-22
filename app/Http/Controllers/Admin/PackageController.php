<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        // $packages = TourPackage::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.packages.index');
    }
}
