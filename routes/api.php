<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RideController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SosAlertController;
use App\Http\Controllers\Users\BookRideControlle;
use App\Http\Controllers\Users\BookRideController;
use App\Http\Controllers\Users\PaymentController;
use App\Http\Controllers\Users\PlacesController;
use App\Http\Controllers\Drivers\LocationController;
use App\Http\Controllers\Drivers\VehicleDetailsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('otp/send', [AuthController::class, 'sendTwilioOtp']);
    Route::post('otp/verify', [AuthController::class, 'verifyTwilioOtp']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'userLogin']);
    Route::get('auth/google', [AuthController::class, 'redirectToGoogle']);
    Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

    Route::get('/places/search', [PlacesController::class, 'searchPlaces']);
    Route::get('/places/{placeId}', [PlacesController::class, 'getPlaceDetails']);
    Route::get('/calculate-distance', [PlacesController::class, 'calculateDistance']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::put('/emergency-contacts', [AuthController::class, 'updateContacts']);
        Route::post('/become-driver', [AuthController::class, 'becomeDriver']);

        Route::prefix('user')->group(function () {
            Route::post('/rides/create', [BookRideControlle::class, 'createRide']);
        });

        Route::prefix('driver')->group(function () {
            Route::post('/{id}/location', [LocationController::class, 'update']);
            Route::get('/rides', [RideController::class, 'driverRides']);
            Route::get('/ratings', [RideController::class, 'driverRatings']);
        });

        Route::prefix('admin')->group(function () {
            Route::get('/users', [AuthController::class, 'getAllUsers']);
            Route::get('/drivers', [AuthController::class, 'getAllDrivers']);
            Route::post('/drivers/{id}/verify', [AuthController::class, 'updateDriverStatus']);
        });


        // routes/api.php → inside auth:sanctum group
        Route::prefix('payment')->group(function () {
            Route::post('/create-order', [PaymentController::class, 'createOrder']);
            Route::post('/verify', [PaymentController::class, 'verifyPayment']);
        });

        Route::prefix('rides')->group(function () {
            Route::get('/', [RideController::class, 'index']);
            Route::post('/', [RideController::class, 'store']);
            Route::get('/{id}', [RideController::class, 'show']);
            Route::put('/{id}', [RideController::class, 'update']);
            Route::delete('/{id}', [RideController::class, 'destroy']);
            Route::post('/{id}/cancel', [RideController::class, 'cancel']);
            Route::post('/{id}/accept', [RideController::class, 'accept']);
            Route::post('/{id}/status', [RideController::class, 'updateStatus']);
        });

        Route::post('/sos', [SosAlertController::class, 'store']);
        Route::apiResource('vehicle-details', VehicleDetailsController::class);



    });

    Route::post("/service/create", [ServiceController::class, "store"]);


});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});