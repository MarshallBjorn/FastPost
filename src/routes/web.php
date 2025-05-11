<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Temporary login route so 'auth' middleware doesn't crash
Route::get('/login', function () {
    return 'TODO: Login form goes here';
})->name('login');


// Below code will redirect to login if request user is not admin (app.php withMiddleware section would be used)
// Route::middleware(['auth', 'is_admin'])->group(function () {
//     Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.dashboard');
// });

Route::get('/admin', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('admin.dashboard');

Route::prefix('admin')->group(function () {
    Route::resource('packages', App\Http\Controllers\Admin\PackageController::class);
    Route::resource('postmats', App\Http\Controllers\Admin\PostmatController::class);
    Route::resource('warehouses', App\Http\Controllers\Admin\WarehouseController::class);
    // Route::resource('actualizations', ActualizationController::class);
});

// When middleware of admin is created then don't forgot to make all admin routes go through admin middleware
// Route::prefix('admin')->middleware(['auth', 'is_admin'])->group(function () {
//     Route::resource('packages', App\Http\Controllers\Admin\PackageController::class);
//     // Route::resource('actualizations', ActualizationController::class);
// });
