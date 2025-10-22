<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Ride;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function index()
    {
        $totalUsers = User::count();
        $totalDrivers = User::where('role', 'driver')->count();
        // $totalRides = Ride::count();
        // $pendingRides = Ride::where('status', 'pending')->count();

        return view('admin.dashboard.index', compact('totalUsers', 'totalDrivers'));
    }

    public function bookings()
    {
        // $bookings = Ride::with(['ridesAsPassenger', 'ridesAsDriver'])
        //     ->orderBy('created_at', 'desc')
        //     ->paginate(10);

        return view('admin.bookings.index');
    }

    public function users()
    {
        // $users = User::where('role', 'passenger')
        //     ->orderBy('created_at', 'desc')
        //     ->paginate(10);

        return view('admin.users.index');
    }

    public function driver()
    {
        // $drivers = User::where('role', 'driver')
        //     ->with(['documents', 'vehicles'])
        //     ->orderBy('created_at', 'desc')
        //     ->paginate(10);

        return view('admin.driver.index');
    }
}