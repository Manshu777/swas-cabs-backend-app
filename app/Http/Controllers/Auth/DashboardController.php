<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class DashboardController extends Controller
{
    public function index()
    {
      

        return view('admin.dashboard.index');
    }

    public function bookings()
    {
      

        return view('admin.bookings.index');
    }

    //bookings
}