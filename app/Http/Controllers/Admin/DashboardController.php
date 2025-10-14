<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



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


    public function users()
    {
      

        return view('admin.users.index');
    }

    public function driver()
    {
      

        return view('admin.driver.index');
    }



    //driver

    //users

    //bookings
}