<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RiderDocuments;
use App\Models\VehicleDetails;
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



     public function document()
    {
      


$documents = Riderdocuments::orderBy('created_at', 'desc')->paginate(10);

// return response()->json(["docume"=>$documents]);

        return view('admin.documents.index',compact("documents"));
    }



    public function reject(Request $request,$id){


$document = Riderdocuments::find($id)->update(["status"=>"rejected","rejection_reason"=>$request["rejection_reason"]]);
 return redirect()->back()->with("success","Document rejected");

    }



 public function approve ($id){
$document = Riderdocuments::find($id)->update(["status"=>"approved","rejection_reason"=>null]);

     return redirect()->back()->with("success","Document Approve");


 }



public function vehicle (){
 
    $vehicles = VehicleDetails::orderBy('created_at', 'desc')->paginate(10);


            return view('admin.vehicles.index',compact("vehicles"));

}

 public function vehicleapprove ($id){
$document = VehicleDetails::find($id)->update(["status"=>"approved","rejection_reason"=>null]);

     return redirect()->back()->with("success","Vehicle Approve");


 }
public function vehiclereject(Request $request,$id){

$document = VehicleDetails::find($id)->update(["status"=>"rejected","rejection_reason"=>$request["rejection_reason"]]);
 return redirect()->back()->with("success","Document rejected");
}




}


