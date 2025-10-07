<?php

use App\Http\Controllers\Drivers\RideController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\SosAlertControler as SOSController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriverVerificationController;
use App\Http\Controllers\Drivers\VehicleDetailsController;

use App\Http\Controllers\Users\PlacesController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');






Route::get('/calculate-distance', [PlacesController::class, 'calculateDistance']);


Route::get('/places/search', [PlacesController::class, 'searchPlaces']);
Route::get('/places/{placeId}', [PlacesController::class, 'getPlaceDetails']);



// API routes for user and driver authentication

Route::prefix('v1')->group(function () {

    Route::middleware('auth:sanctum')->get('/profile', [AuthController::class, 'profile']);

    Route::post('/user/register', [AuthController::class, 'userRegister']);
    Route::post('/driver/register', [AuthController::class, 'driverRegister']);

    Route::post('/user/login', [AuthController::class, 'userLogin']);
    Route::post('/driver/login', [AuthController::class, 'driverLogin']);

    Route::post('/verify/{id}/driver', [AuthController::class, 'updateDriverStatus']);
    Route::get('/getall/users', [AuthController::class, 'getAllUsers']);
    Route::get('/getall/driver', [AuthController::class, 'getAllDrivers']);


    Route::middleware('auth:sanctum')->post('/emergency-contacts', [AuthController::class, 'updateContacts']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
    Route::middleware('auth:sanctum')->get('/user/profile', [AuthController::class, 'profile']);

    // ride booking and management routes

    Route::apiResource('vehicle-details', VehicleDetailsController::class);
    // Protected routes for authenticated users and drivers
    Route::middleware('auth:sanctum')->group(function () {

        // Ride management routes
        Route::post('/ride/estimate', [RideController::class, 'estimateFare']);
        Route::post('/ride/book', [RideController::class, 'createRide']);
        Route::patch('/ride/{rideId}/assign-driver', [RideController::class, 'assignDriver']);
        Route::post('/sos', [SOSController::class, 'triggerSOS']);

        // User routes
        Route::post('/ride/search', [RideController::class, 'searchRide']);
        Route::post('/ride/book', [RideController::class, 'bookRide']);
        Route::post('/ride/track', [RideController::class, 'trackRide']);

        // Driver routes
        Route::post('/ride/accept', [RideController::class, 'acceptRide']);
        Route::post('/driver/location', [RideController::class, 'updateLocation']);

        // Admin routes (assuming admin middleware)
        Route::post('/driver/verify', [DriverVerificationController::class, 'verifyDriver']);
    });


});