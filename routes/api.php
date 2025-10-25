<?php

use App\Http\Controllers\RideController;
use App\Http\Controllers\SosAlertController;
use App\Http\Controllers\Users\BookRidecontrolle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\SosAlertControler as SOSController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriverVerificationController;
use App\Http\Controllers\Drivers\VehicleDetailsController;
use App\Events\TestEvent;

use App\Http\Controllers\Users\PlacesController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::get('/test-broadcast', function () {
    event(new TestEvent("Hello, Laravel WebSocket!"));
    return response()->json(['status' => 'Message broadcasted!']);
});

Route::get('/calculate-distance', [PlacesController::class, 'calculateDistance']);
Route::get('/places/search', [PlacesController::class, 'searchPlaces']);
Route::get('/places/{placeId}', [PlacesController::class, 'getPlaceDetails']);


Route::middleware('auth:sanctum')->post('/become-driver', [AuthController::class, 'becomeDriver']);



Route::middleware('auth:api')->post('/drivers/{id}/location', [App\Http\Controllers\Drivers\LocationController::class, 'update']);

Route::middleware('auth:api')->group(function () {
    Route::post('/rides', [App\Http\Controllers\Api\RideController::class, 'store']);
    Route::get('/rides', [App\Http\Controllers\Api\RideController::class, 'passengerRides']);
    Route::post('/rides/{ride}/accept', [App\Http\Controllers\Api\RideController::class, 'accept']);
    Route::post('/rides/{ride}/status', [App\Http\Controllers\Api\RideController::class, 'updateStatus']);
    Route::get('/drivers/rides', [App\Http\Controllers\Api\RideController::class, 'driverRides']);
    Route::get('/drivers/ratings', [App\Http\Controllers\Api\RideController::class, 'driverRatings']);
    Route::post('/sos', [SosAlertController::class, 'store']);
    Route::post('/drivers/{id}/location', [App\Http\Controllers\Drivers\LocationController::class, 'update']);
});



//   driver routes
Route::prefix("/v1/driver")->group(function(){
Route::post("/register",[AuthController::class,"driverRegister"]);
Route::post("/login ",[AuthController::class,"driverLogin"]);
});







// user routes
Route::prefix("/v1/user")->group(function(){
Route::post("/genrate-otp", [AuthController::class,"generateAadhaarOtp"]);
Route::post("/verify-otp", [AuthController::class,"verifyAdharOtp"]);
Route::post("/login", [AuthController::class,"userLogin"]);
Route::put("/update-contect",[AuthController::class,"userLogin"]);

Route::post("/create-ride",[BookRidecontrolle::class,"createRide"]);

});







Route::prefix('/v1/rides')->group(function () {
    Route::get('/', [RideController::class, 'index']);         
    Route::post('/', [RideController::class, 'store']);         
    Route::get('/{id}', [RideController::class, 'show']);      
    Route::put('/{id}', [RideController::class, 'update']);   
    Route::delete('/{id}', [RideController::class, 'destroy']); 
    Route::post('/{id}/cancel', [RideController::class, 'cancel']); 
});







//







// API routes for user and driver authentication



// Route::middleware('auth:sanctum')->group(function () {
//     // Common routes
//     Route::get('/profile', [UserController::class, 'profile']);
//     Route::put('/profile', [UserController::class, 'update']);

//     // Passenger routes
//     Route::middleware('role:passenger')->group(function () {
//         Route::post('/rides', [RideController::class, 'store']);
//         Route::get('/rides', [RideController::class, 'passengerRides']);
//         Route::post('/rides/{ride}/rate', [RatingController::class, 'store']);
//         Route::post('/rides/{ride}/cancel', [RideController::class, 'cancel']);
//         Route::post('/sos', [SosAlertController::class, 'store']);
//         Route::post('/rides/{ride}/pay', [TransactionController::class, 'pay']);
//     });

//     // Driver routes
//     Route::middleware('role:driver')->group(function () {
//         Route::get('/rides/available', [RideController::class, 'available']);
//         Route::post('/rides/{ride}/accept', [RideController::class, 'accept']);
//         Route::post('/rides/{ride}/update-status', [RideController::class, 'updateStatus']);
//         Route::get('/driver/rides', [RideController::class, 'driverRides']);
//         Route::get('/driver/ratings', [RatingController::class, 'driverRatings']);
//         Route::post('/documents', [DocumentController::class, 'store']);
//         Route::get('/documents', [DocumentController::class, 'index']);
//         Route::post('/vehicles', [VehicleController::class, 'store']);
//         Route::get('/vehicles', [VehicleController::class, 'index']);
//         Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update']);
//         Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy']);
//     });
// });

// Route::prefix('v1')->group(function () {

//     Route::middleware('auth:sanctum')->get('/profile', [AuthController::class, 'profile']);

//     Route::post('/user/register', [AuthController::class, 'userRegister']);
//     Route::post('/driver/register', [AuthController::class, 'driverRegister']);

//     Route::post('/user/login', [AuthController::class, 'userLogin']);
//     Route::post('/driver/login', [AuthController::class, 'driverLogin']);

//     Route::post('/verify/{id}/driver', [AuthController::class, 'updateDriverStatus']);
//     Route::get('/getall/users', [AuthController::class, 'getAllUsers']);
//     Route::get('/getall/driver', [AuthController::class, 'getAllDrivers']);


//     Route::middleware('auth:sanctum')->post('/emergency-contacts', [AuthController::class, 'updateContacts']);
//     Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
//     Route::middleware('auth:sanctum')->get('/user/profile', [AuthController::class, 'profile']);

//     // ride booking and management routes

//     Route::apiResource('vehicle-details', VehicleDetailsController::class);
//     // Protected routes for authenticated users and drivers
//     Route::middleware('auth:sanctum')->group(function () {

//         // Ride management routes
//         Route::post('/ride/estimate', [RideController::class, 'estimateFare']);
//         Route::post('/ride/book', [RideController::class, 'createRide']);
//         Route::patch('/ride/{rideId}/assign-driver', [RideController::class, 'assignDriver']);
//         Route::post('/sos', [SOSController::class, 'triggerSOS']);

//         // User routes
//         Route::post('/ride/search', [RideController::class, 'searchRide']);
//         Route::post('/ride/book', [RideController::class, 'bookRide']);
//         Route::post('/ride/track', [RideController::class, 'trackRide']);

//         // Driver routes
//         Route::post('/ride/accept', [RideController::class, 'acceptRide']);
//         Route::post('/driver/location', [RideController::class, 'updateLocation']);

//         // Admin routes (assuming admin middleware)
//         Route::post('/driver/verify', [DriverVerificationController::class, 'verifyDriver']);
//     });


// });