<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostmatPublicController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::view('/auth', 'public.auth')->name('auth');

    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

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
    Route::resource('stashes', App\Http\Controllers\Admin\StashController::class);
    Route::get('postmats/{postmat}/stashes', [App\Http\Controllers\Admin\StashController::class, 'index'])->name('stashes.index');
    Route::get('postmats/{postmat}/stashes/create', [App\Http\Controllers\Admin\StashController::class, 'create'])->name('stashes.create');
    Route::post('postmats/{postmat}/stashes', [App\Http\Controllers\Admin\StashController::class, 'store'])->name('stashes.store');
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
});

Route::get('/test404', function() {
    abort(404);
});

Route::get('/test403', function() {
    abort(403);
});

Route::get('/test500', function() {
    abort(500);
});

Route::get('/test429', function() {
    abort(429);
});

// When middleware of admin is created then don't forgot to make all admin routes go through admin middleware
// Route::prefix('admin')->middleware(['auth', 'is_admin'])->group(function () {
//     Route::resource('packages', App\Http\Controllers\Admin\PackageController::class);
//     // Route::resource('actualizations', ActualizationController::class);
// });
