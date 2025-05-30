<?php

namespace App\Http\Controllers\Admin;

use App\Models\Package;
use App\Models\User;
use App\Models\Postmat;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Actualization;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with(['sender', 'receiver', 'postmat'])->get();
        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        $users = User::all();
        $postmats = Postmat::all();
        return view('admin.packages.create', compact('users', 'postmats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sender_id' => 'nullable|exists:users,id',
            'receiver_id' => 'nullable|exists:users,id',
            'start_postmat_id' => 'nullable|exists:postmats,id',
            'destination_postmat_id' => 'nullable|exists:postmats,id',
            'receiver_email' => 'required|email',
            'receiver_phone' => 'required|string',
            'status' => 'required|string|in:registered,in_transit,in_postmat,collected',
            'size' => 'required|in:S,M,L',
            'weight' => 'required|integer|min:1',
            'unlock_code' => 'required|digits:6',
        ], [
            'status.in' => 'The status must be one of the following: registered, in_transit, in_postmat, collected.',
            'weight.integer' => 'Weight must be an integer in grams.',
            'weight.min' => 'Weight must be at least 1 gram.',
            'size.in' => 'The package size must be one of the following: S, M, L',
        ]);

        // Handle sender creation if not exists
        if (empty($validated['sender_id']) || !User::find($validated['sender_id'])) {
            $sender = User::create([
                'first_name' => 'GenName',
                'last_name' => 'GenLastName',
                'phone' => $validated['receiver_phone'],
                'name' => 'Generated Sender',
                'email' => 'sender_' . uniqid() . '@example.com',
                'password' => bcrypt('secret'), // dummy password
            ]);
            $validated['sender_id'] = $sender->id;
        }

        // Handle postmat creation if not provided
        if (empty($validated['destination_postmat_id']) || !Postmat::find($validated['destination_postmat_id'])) {
            $postmat = Postmat::create([
                'name' => 'Fastpost-Tokyo',
                'city' => 'Tokyo',
                'post_code' => '33-200',
                'latitude' => 35.6895,
                'longitude' => 139.8394,
                'status' => 'active'
            ]);
            $validated['destination_postmat_id'] = $postmat->id;
        }

        $package = Package::create($validated);

        Actualization::create([
            'package_id' => $package->id,
            // message = ['sent', 'in_warehouse', 'in_delivery']
            'message' => 'sent',
            'created_at' => now(),
        ]);

        return redirect()->route('packages.index')->with('success', 'Package created.');
    }

    public function show(Package $package)
    {
        $package->load('latestActualization');

        $routePath = json_decode($package->route_path, true) ?? [];

        $routeRemaining = json_decode(optional($package->latestActualization)->route_remaining, true) ?? [];

        $warehouses = Warehouse::whereIn('id', $routePath)->get(['id', 'latitude', 'longitude', 'city']);

        return view('admin.packages.show', compact('package', 'warehouses', 'routePath', 'routeRemaining'));
    }

    public function edit(Package $package)
    {
        $users = User::all();
        $postmats = Postmat::all();
        return view('admin.packages.edit', compact('package', 'users', 'postmats'));
    }

    public function update(Request $request, Package $package)
    {
        $data = $request->validate([
            'sender_id' => 'nullable|exists:users,id',
            'receiver_id' => 'nullable|exists:users,id',
            'start_postmat_id' => 'nullable|exists:postmats,id',
            'destination_postmat_id' => 'nullable|exists:postmats,id',
            'receiver_email' => 'required|email',
            'receiver_phone' => 'required|string',
            'status' => 'required|string|in:registered,in_transit,in_postmat,collected',
            'unlock_code' => 'required|digits:6',
        ]);

        $package->update($data);
        return redirect()->route('packages.index')->with('success', 'Package updated.');
    }

    public function destroy(Package $package)
    {
        $package->delete();
        return redirect()->route('packages.index')->with('success', 'Package deleted.');
    }

    public function advancePackage(Package $package)
    {
        $latest = $package->latestActualization;
        $route = json_decode($latest->route_remaining, true);

        if (empty($route)) {
            Actualization::create([
                'package_id' => $package->id,
                'route_remaining' => json_encode([]),
                'current_warehouse_id' => null,
                'message' => 'in_delivery',
                'created_at' => now(),
            ]);
            return;
        }

        $current = array_shift($route);
        $next = $route[0] ?? null;

        Actualization::create([
            'package_id' => $package->id,
            'route_remaining' => json_encode($route),
            'current_warehouse_id' => $current,
            'next_warehouse_id' => $next,
            'message' => 'in_warehouse',
            'last_courier_id' => auth()->id(),
            'created_at' => now(),
        ]);
    }

    public function advancePackageRedirect(Package $package)
    {
        $this->advancePackage($package);
        return redirect()->route('packages.index')
            ->with('success', 'Package advanced along its route');
    }
}
