<?php

use App\Http\Controllers\PostmatPublicController;
use Illuminate\Support\Facades\Route;


// Temporary login route so 'auth' middleware doesn't crash
Route::get('/login', function () {
    return 'TODO: Login form goes here';
})->name('login');

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);

// Below code will redirect to login if request user is not admin (app.php withMiddleware section would be used)
// Route::middleware(['auth', 'is_admin'])->group(function () {
//     Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.dashboard');
// });

Route::get('/admin', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('admin.dashboard');

Route::get('/postmats', [PostmatPublicController::class,'index'])->name('public.postmats.index');
Route::get('/postmats/filter', [PostmatPublicController::class, 'filter'])->name('public.postmats.filter');

Route::get('/client/packages/send_package', [App\Http\Controllers\Client\PackageController::class, 'showForm'])->name('client.send_package');
Route::post('/client/packages/send_package', [App\Http\Controllers\Client\PackageController::class, 'send_package'])->name('client.send_package.submit');
Route::get('/track', [App\Http\Controllers\Client\PackageController::class, 'track'])->name('package.lookup');


Route::prefix('admin')->group(function () {
    Route::resource('packages', App\Http\Controllers\Admin\PackageController::class);
    Route::resource('postmats', App\Http\Controllers\Admin\PostmatController::class);
    Route::resource('warehouses', App\Http\Controllers\Admin\WarehouseController::class);
    Route::resource('actualizations', App\Http\Controllers\Admin\ActualizationController::class);
});

// When middleware of admin is created then don't forgot to make all admin routes go through admin middleware
// Route::prefix('admin')->middleware(['auth', 'is_admin'])->group(function () {
//     Route::resource('packages', App\Http\Controllers\Admin\PackageController::class);
//     // Route::resource('actualizations', ActualizationController::class);
// });
