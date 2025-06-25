<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PackageStatus;
use App\Models\Package;
use App\Models\User;
use App\Models\Postmat;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Actualization;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $query = Package::with(['sender', 'receiver', 'postmat'])->latest();

        if ($request->filled('id')) {
            $query->where('id', $request->input('id'));
        }

        if ($request->filled('sender_email')) {
            $query->whereHas('sender', function ($q) use ($request) {
                $q->where('email', 'like', '%' . $request->input('sender_email') . '%');
            });
        }

        if ($request->filled('receiver_email')) {
            $query->where('receiver_email', 'like', '%' . $request->input('receiver_email') . '%');
        }

        if ($request->filled('receiver_phone')) {
            $query->where('receiver_phone', 'like', '%' . $request->input('receiver_phone') . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('size')) {
            $query->where('size', $request->input('size'));
        }

        $packages = $query->paginate(6)->withQueryString();

        $statuses = \App\Enums\PackageStatus::cases();
        $sizes = \App\Enums\PackageSize::cases();

        return view('admin.packages.index', compact('packages', 'statuses', 'sizes'));
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
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'nullable|exists:users,id',
            'start_postmat_id' => 'required|exists:postmats,id',
            'destination_postmat_id' => 'required|exists:postmats,id',
            'receiver_email' => 'required|email',
            'receiver_phone' => 'required|string|max:14',
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

        // Create the package
        $package = Package::create($validated);

        // Generate the route path (if applicable)
        $startPostmat = Postmat::find($validated['start_postmat_id']);
        $destinationPostmat = Postmat::find($validated['destination_postmat_id']);

        if ($startPostmat && $destinationPostmat) {
            $pathfinder = new \App\Services\Pathfinder();
            $routePath = $pathfinder->findPath($startPostmat->warehouse_id, $destinationPostmat->warehouse_id);
    
            // Store the route path in the package
            $package->update(['route_path' => json_encode($routePath)]);
    
            // Create the initial actualization
            Actualization::create([
                'package_id' => $package->id,
                'message' => 'sent',
                'route_remaining' => json_encode($routePath),
                'created_at' => now(),
            ]);
        }

        // Create the actualization
        Actualization::create([
            'package_id' => $package->id,
            'message' => 'sent',
            'route_remaining' => $routePath ? json_encode($routePath) : '[]', // Ensure route_remaining is not null
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
        $validated = $request->validate([
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'nullable|exists:users,id',
            'start_postmat_id' => 'required|exists:postmats,id',
            'destination_postmat_id' => 'required|exists:postmats,id',
            'receiver_email' => 'required|email',
            'receiver_phone' => 'required|string|max:14',
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

        // Update the package
        $package->update($validated);

        // Update the route path if applicable
        $startPostmat = Postmat::find($validated['start_postmat_id']);
        $destinationPostmat = Postmat::find($validated['destination_postmat_id']);

        $routePath = null;
        if ($startPostmat && $destinationPostmat) {
            $pathfinder = new \App\Services\Pathfinder();
            $routePath = $pathfinder->findPath($startPostmat->warehouse_id, $destinationPostmat->warehouse_id);
        }

        // Update the latest actualization with the new route path
        $package->latestActualization()->update([
            'route_remaining' => $routePath ? json_encode($routePath) : '[]',
        ]);

        return redirect()->route('packages.index', $request->query())->with('success', 'Package updated.');
    }

    public function destroy(Request $request, Package $package)
    {
        $package->delete();
        return redirect()->route('packages.index', $request->query())->with('success', 'Package deleted.');
    }

    public function advancePackageRedirect(Request $request, Package $package)
    {
        $package->advancePackage();
        return redirect()->route('packages.index', $request->query())
            ->with('success', 'Package advanced along its route');
    }
}
