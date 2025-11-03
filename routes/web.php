<?php



use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\RideController;
use App\Http\Controllers\Admin\FinancialController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\SosController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\ChatController;

//ChatController
// use App\Http\Controllers\Admin\UserController;
// use App\Http\Controllers\Admin\RoleController;
// use App\Http\Controllers\Admin\PermissionController;
// use App\Http\Controllers\Admin\SettingsController;

use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return view('welcome');
});


  Route::get('/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'login']);

Route::prefix('admin')->middleware(['auth', 'admin.access:admin,super_admin'])->name('admin.')->group(function () {

  Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    return response()->json(['message' => '✅ Cache cleared successfully!']);
})->name('admin.clear-cache');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/bookings', [DashboardController::class, 'bookings'])->name('bookings');
    Route::get('/users', [DashboardController::class, 'users'])->name('users');
    Route::get('/drivers', [DashboardController::class, 'driver'])->name('drivers');
     Route::get('/documents', [DashboardController::class, 'document'])->name('documents');  
     
      Route::get('documents/{id}', [DashboardController::class, 'show'])->name('documents.show');
    Route::put('documents/{id}/approve', [DashboardController::class, 'approve'])->name('documents.approve');
    Route::put('documents/{id}/reject', [DashboardController::class, 'reject'])->name('documents.reject');

    

        Route::get('/vehicle', [DashboardController::class, 'vehicle'])->name('vehicles');  
    Route::put('vehicle/{id}/approve', [DashboardController::class, 'vehicleapprove'])->name('vehicles.approve');
    Route::put('vehicles/{id}/reject', [DashboardController::class, 'vehiclereject'])->name('vehicles.reject');




    Route::get('/units', [UnitController::class, 'index'])->name('units');
    Route::get('/rides', [RideController::class, 'index'])->name('rides');
    Route::get('/financial', [FinancialController::class, 'index'])->name('financial');
    Route::get('/packages', [PackageController::class, 'index'])->name('packages');
    Route::get('/sos', [SosController::class, 'index'])->name('sos');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
  
});

  Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


 Route::get('/chat', [ChatController::class, 'index']);
    Route::post('/messages', [ChatController::class, 'sendMessage']);




    // Authenticated routes
    // Route::middleware(['auth', 'admin'])->group(function () {
    //     Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    //     Route::resource('users', UserController::class);
    //     Route::resource('roles', npm install -D tailwindcss postcss autoprefixerRoleController::class);
    //     Route::resource('permissions', PermissionController::class);
    //     Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings');
    //     Route::post('/settings', [SettingsController::class, 'update'])->name('admin.settings.update');
    // });
// });