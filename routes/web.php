<?php



use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
// use App\Http\Controllers\Admin\UserController;
// use App\Http\Controllers\Admin\RoleController;
// use App\Http\Controllers\Admin\PermissionController;
// use App\Http\Controllers\Admin\SettingsController;



Route::get('/', function () {
    return view('welcome');
});



Route::prefix('admin')->group(function () {
    // Public login route
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'login']);
Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/bookings', [DashboardController::class, 'bookings'])->name('admin.booking');
Route::get('/users', [DashboardController::class, 'users'])->name('admin.users');


Route::get('/driver', [DashboardController::class, 'driver'])->name('admin.driver');
    // Authenticated routes
    // Route::middleware(['auth', 'admin'])->group(function () {
    //     Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    //     Route::resource('users', UserController::class);
    //     Route::resource('roles', npm install -D tailwindcss postcss autoprefixerRoleController::class);
    //     Route::resource('permissions', PermissionController::class);
    //     Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings');
    //     Route::post('/settings', [SettingsController::class, 'update'])->name('admin.settings.update');
    // });
});