<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostmatPublicController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// PUBLIC ROUTES
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Authentication: Guests only
Route::middleware('guest')->group(function () {
    Route::view('/auth', 'auth.login')->name('auth');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

// Email verification
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', fn() => view('auth.verify-email'))->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/dashboard');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification email sent!');
    })->middleware('throttle:6,1')->name('verification.send');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->middleware('verified')->name('dashboard');
});

// Admin routes
Route::prefix('admin')->middleware(['auth', 'verified', 'is_admin'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('admin.dashboard');

    Route::resource('packages', App\Http\Controllers\Admin\PackageController::class);
    Route::resource('postmats', App\Http\Controllers\Admin\PostmatController::class);
    Route::resource('warehouses', App\Http\Controllers\Admin\WarehouseController::class);
    Route::resource('actualizations', App\Http\Controllers\Admin\ActualizationController::class);
    Route::resource('stashes', App\Http\Controllers\Admin\StashController::class);
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);

    Route::get('postmats/{postmat}/stashes', [App\Http\Controllers\Admin\StashController::class, 'index'])->name('stashes.index');
    Route::get('postmats/{postmat}/stashes/create', [App\Http\Controllers\Admin\StashController::class, 'create'])->name('stashes.create');
    Route::post('postmats/{postmat}/stashes', [App\Http\Controllers\Admin\StashController::class, 'store'])->name('stashes.store');
});

// Public package routes (client)
Route::get('/client/packages/send_package', [App\Http\Controllers\Client\PackageController::class, 'showForm'])->name('client.send_package');
Route::post('/client/packages/send_package', [App\Http\Controllers\Client\PackageController::class, 'send_package'])->name('client.send_package.submit');
Route::get('/track', [App\Http\Controllers\Client\PackageController::class, 'track'])->name('package.lookup');

// Public Postmats
Route::get('/postmats', [PostmatPublicController::class, 'index'])->name('public.postmats.index');
Route::get('/postmats/filter', [PostmatPublicController::class, 'filter'])->name('public.postmats.filter');

// Error testing
Route::get('/test404', fn() => abort(404));
Route::get('/test403', fn() => abort(403));
Route::get('/test500', fn() => abort(500));
Route::get('/test429', fn() => abort(429));
