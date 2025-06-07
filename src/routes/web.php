<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Delivery\PostmatRouteController;
use App\Http\Controllers\Delivery\RouteController;
use App\Http\Controllers\Delivery\WarehouseRouteController;
use App\Http\Controllers\PostmatPublicController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// PUBLIC ROUTES
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Authentication: Guests only
Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('auth');
    Route::post('/login', action: [AuthController::class, 'login'])->name('login');
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
Route::prefix('admin')->middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('admin.dashboard');

    Route::resource('packages', App\Http\Controllers\Admin\PackageController::class);
    Route::resource('postmats', App\Http\Controllers\Admin\PostmatController::class);
    Route::resource('warehouses', App\Http\Controllers\Admin\WarehouseController::class);
    Route::resource('actualizations', App\Http\Controllers\Admin\ActualizationController::class);
    Route::resource('stashes', App\Http\Controllers\Admin\StashController::class);
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);

    Route::post('packages/{package}/advance', [App\Http\Controllers\Admin\PackageController::class, 'advancePackageRedirect'])
        ->name('packages.advance');

    Route::get('postmats/{postmat}/stashes', [App\Http\Controllers\Admin\StashController::class, 'index'])->name('stashes.index');
    Route::get('postmats/{postmat}/stashes/create', [App\Http\Controllers\Admin\StashController::class, 'create'])->name('stashes.create');
    Route::get('postmats/stashes/edit/{stash}', [App\Http\Controllers\Admin\StashController::class, 'edit'])->name('stashes.edit');
    Route::post('postmats/{postmat}/stashes', [App\Http\Controllers\Admin\StashController::class, 'store'])->name('stashes.store');
    Route::delete('postmats/{postmat}/stashes/{stash}', [App\Http\Controllers\Admin\StashController::class, 'destroy'])->name('stashes.destroy');
    Route::put('postmats/{postmat}/edit/{stash}', [App\Http\Controllers\Admin\StashController::class, 'update'])->name('stashes.update');
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
});

Route::prefix('warehouse')->middleware(['auth', 'verified', 'role:warehouse_courier'])->group(function () {
    Route::get('/delivery', [WarehouseRouteController::class, 'index'])->name('warehouse.delivery.index');
    Route::get('/my-packages', [WarehouseRouteController::class, 'myPackages'])->name('warehouse.delivery.my_packages');
    Route::post('/take/{from}/{to}', [WarehouseRouteController::class, 'takeRoute'])->name('warehouse.delivery.take');
    Route::post('/confirm-arrival/{from}/{to}', [WarehouseRouteController::class, 'confirmArrival'])->name('warehouse.delivery.confirm_arrival');
    Route::post('/return-to-mother', [WarehouseRouteController::class, 'startReturnTrip'])->name('warehouse.delivery.start_return');
    Route::post('/confirm-return/{from}/{to}', [WarehouseRouteController::class, 'confirmReturn'])->name('warehouse.delivery.confirm_return');
});

Route::prefix('postmat')->middleware(['auth', 'verified', 'role:postmat_courier'])->group(function () {
    Route::get('/delivery', [PostmatRouteController::class, 'index'])->name('postmat.delivery.index');
    Route::get('/my-packages', [PostmatRouteController::class, 'myPackages'])->name('postmat.delivery.my_packages');
    Route::post('/delivery/pickup/{postmat}', [PostmatRouteController::class, 'pickup'])->name('postmat.delivery.pickup');
    Route::post('/delivery/put-in-warehouse', [PostmatRouteController::class, 'putPackagesInWarehouse'])->name('postmat.delivery.putPackagesInWarehouse');
    Route::post('/take/{from}/{to}', [PostmatRouteController::class, 'takeOrder'])->name('postmat.delivery.take');
});

Route::prefix('warehouse')->middleware(['auth', 'verified', 'role:warehouse'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\WarehouseController::class, 'index'])->name('warehouse.dashboard');
});

// Client routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/client/packages/', [App\Http\Controllers\Client\PackageController::class, 'show_user_packages'])->name('client.packages');
    Route::get('/client/packages/send_package', [App\Http\Controllers\Client\PackageController::class, 'showForm'])->name('client.send_package');
    Route::post('/client/packages/send_package', [App\Http\Controllers\Client\PackageController::class, 'send_package'])->name('client.send_package.submit');
    Route::post('/client/packages/put_package_in_postmat', [App\Http\Controllers\Client\PackageController::class, 'put_package_in_postmat'])->name('client.put_package_in_postmat');
});

Route::get('/track', [App\Http\Controllers\Client\PackageController::class, 'track'])->name('package.lookup');

Route::get('/client/packages/collect', [App\Http\Controllers\Client\PackageController::class, 'show_collect_package'])->name('client.collect_package');
Route::post('/client/packages/collect', [App\Http\Controllers\Client\PackageController::class, 'collect_package'])->name('client.collect_package.submit');
Route::view('/client/packages/collected', 'public.client.packages.collected')->name('client.package.collected');

// Public Postmats
Route::get('/postmats', [PostmatPublicController::class, 'index'])->name('public.postmats.index');
Route::get('/postmats/filter', [PostmatPublicController::class, 'filter'])->name('public.postmats.filter');

// Error testing
Route::get('/test404', fn() => abort(404));
Route::get('/test403', fn() => abort(403));
Route::get('/test500', fn() => abort(500));
Route::get('/test429', fn() => abort(429));
