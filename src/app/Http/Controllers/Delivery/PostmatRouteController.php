<?php

namespace App\Http\Controllers\Delivery;

use App\Enums\PackageStatus;
use App\Http\Controllers\Controller;
use App\Models\Actualization;
use App\Models\Postmat;
use App\Models\Package;
use App\Models\Stash;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class PostmatRouteController extends Controller
{

    public function index(Request $request)
    {
        $user = auth()->user();
        $currentWarehouseId = $user->staff->warehouse_id;

        $allPostmats = [];

        // Fetch packages for pickup (start)
        $start_packages = Package::whereIn('status', [PackageStatus::REGISTERED->value, PackageStatus::IN_TRANSIT->value])
            ->whereHas('stash', function (Builder $query) {
                $query->where('is_package_in', true);
            })
            ->get();

        foreach ($start_packages as $package) {
            $actualization = $package->latestActualization;
            $route = json_decode($actualization->route_remaining ?? '[]', true);
            $postmat = $package->startPostmat;

            if (!$postmat || ($route[0] ?? null) != $currentWarehouseId) {
                continue;
            }

            if (!isset($allPostmats[$postmat->id])) {
                $allPostmats[$postmat->id] = [
                    'postmat' => $postmat,
                    'pickup_count' => 0,
                    'delivery_count' => 0,
                    'distance' => \App\Utils\DistanceUtils::haversineDistance(
                        $postmat->latitude,
                        $postmat->longitude,
                        $postmat->warehouse->latitude,
                        $postmat->warehouse->longitude
                    ),
                ];
            }

            $allPostmats[$postmat->id]['pickup_count']++;
        }

        // Fetch packages for delivery (end)
        $dest_packages = Package::with('latestActualization.currentWarehouse', 'destinationPostmat.warehouse')
            ->where('status', PackageStatus::IN_TRANSIT)
            ->whereHas('destinationPostmat', function ($q) {
                $q->where('status', 'active');
            })
            ->get()
            ->filter(function ($package) {
                $latest = $package->latestActualization;
                $routeRemaining = optional($latest)->route_remaining;
                $message = optional($latest)->message;

                // Keep only if route_remaining is empty AND message is NOT 'in_delivery'
                return empty(json_decode($routeRemaining ?? '[]', true)) && $message !== 'in_delivery';
            });

        foreach ($dest_packages as $package) {
            $postmat = $package->destinationPostmat;

            if (!$postmat || optional($package->latestActualization)->currentWarehouse?->id != $currentWarehouseId) continue;

            if (!isset($allPostmats[$postmat->id])) {
                $allPostmats[$postmat->id] = [
                    'postmat' => $postmat,
                    'pickup_count' => 0,
                    'delivery_count' => 0,
                    'distance' => \App\Utils\DistanceUtils::haversineDistance(
                        $postmat->warehouse->latitude,
                        $postmat->warehouse->longitude,
                        $postmat->latitude,
                        $postmat->longitude
                    ),
                ];
            }

            $allPostmats[$postmat->id]['delivery_count']++;
        }

        return view('postmat.delivery.index', [
            'postmatRoutes' => collect($allPostmats)->values(),
        ]);
    }

    public function pickup($postmatId)
    {
        $user = auth()->user();
        $currentWarehouseId = $user->staff->warehouse_id;

        $postmat = Postmat::findOrFail($postmatId);

        // === 1. START PACKAGES: pick up from postmat to warehouse ===
        $startPackages = Package::where('start_postmat_id', $postmatId)
            ->where('status', PackageStatus::REGISTERED)
            ->with('latestActualization')
            ->get();

        foreach ($startPackages as $package) {
            $actualization = $package->latestActualization;
            $route = json_decode($actualization->route_remaining ?? '[]', true);

            if (($route[0] ?? null) != $currentWarehouseId) {
                continue;
            }

            // Clear from stash
            if ($package->stash) {
                $package->stash->update([
                    'is_package_in' => false,
                    'reserved_until' => null,
                    'package_id' => null,
                ]);
            }

            // Record actualization
            Actualization::create([
                'package_id' => $package->id,
                'route_remaining' => json_encode($route),
                'current_warehouse_id' => $currentWarehouseId,
                'next_warehouse_id' => $route[0] ?? null,
                'message' => 'collected',
                'last_courier_id' => $user->id,
                'created_at' => now(),
            ]);

            $package->status = PackageStatus::IN_TRANSIT;
            $package->save();
        }

        // === 2. END PACKAGES: deliver from warehouse to postmat ===
        $destinationPackages = Package::with('latestActualization')
            ->where('destination_postmat_id', $postmatId)
            ->where('status', PackageStatus::IN_TRANSIT)
            ->get()
            ->filter(function ($package) use ($currentWarehouseId) {
                $a = $package->latestActualization;
                return $a &&
                    empty(json_decode($a->route_remaining ?? '[]')) &&
                    $a->message === 'in_warehouse' &&
                    $a->current_warehouse_id == $currentWarehouseId;
            });

        foreach ($destinationPackages as $package) {
            $stash = $postmat->stashes()->available()->first();

            if (!$stash) {
                continue; // optionally log or flash error about no available stash
            }

            $stash->update([
                'is_package_in' => true,
                'package_id' => $package->id,
                'reserved_until' => now()->addDay(),
            ]);

            Actualization::create([
                'package_id' => $package->id,
                'route_remaining' => json_encode([]),
                'current_warehouse_id' => $currentWarehouseId,
                'next_warehouse_id' => null,
                'message' => 'in_delivery',
                'last_courier_id' => $user->id,
                'created_at' => now(),
            ]);

            $package->save();
        }

        return redirect()->route('postmat.delivery.index')->with('success', 'Packages moved successfully.');
    }

    public function putPackagesInWarehouse(Request $request)
    {
        $user = auth()->user();
        $currentWarehouseId = $user->staff->warehouse_id;

        $packageIds = $request->input('package_ids', []);

        $packages = Package::whereIn('id', $packageIds)->get();

        $errors = [];
        $processedCount = 0;

        foreach ($packages as $package) {
            $actualization = $package->latestActualization;
            $route = [];

            if ($actualization && $actualization->route_remaining) {
                $route = json_decode($actualization->route_remaining, true);
            } elseif ($package->route_path) {
                $route = json_decode($package->route_path, true);
            }

            // Skip and log error if route is final (empty)
            if (empty($route)) {
                $errors[] = "Package #{$package->id} has a final route and cannot be put in warehouse.";
                continue;
            }

            // Remove the current warehouse from the route
            array_shift($route);

            try {
                Actualization::create([
                    'package_id' => $package->id,
                    'route_remaining' => json_encode($route),
                    'current_warehouse_id' => $currentWarehouseId,
                    'next_warehouse_id' => $route[0] ?? null,
                    'message' => 'in_warehouse',
                    'last_courier_id' => null,
                    'created_at' => now(),
                ]);

                $package->status = PackageStatus::IN_TRANSIT;
                $package->save();
                $processedCount++;
            } catch (\Exception $e) {
                $errors[] = "Failed to update Package #{$package->id}: " . $e->getMessage();
            }
        }

        $message = $processedCount > 0
            ? "Successfully put {$processedCount} package(s) in warehouse."
            : "No packages were put in warehouse.";

        if (!empty($errors)) {
            return redirect()->back()
                ->with('success', $message)
                ->with('errors', $errors);
        }

        return redirect()->back()->with('success', $message);
    }

    public function deliverToPostmat(Request $request)
    {
        $packageIds = $request->input('package_ids', []);

        if (empty($packageIds)) {
            return back()->with('errors', ['No packages were selected.']);
        }

        $errors = [];
        $successCount = 0;

        foreach ($packageIds as $id) {
            $package = Package::find($id);

            if (!$package || !$package->destination_postmat_id) {
                $errors[] = "Package #$id is invalid or missing destination postmat.";
                continue;
            }

            // Find available stash
            $stash = Stash::where('postmat_id', $package->destination_postmat_id)
                ->whereNull('package_id')
                ->orWhere(function ($query) {
                    $query->whereNotNull('reserved_until')
                        ->where('reserved_until', '<', now());
                })
                ->first();

            if (!$stash) {
                $errors[] = "No stash available for Package #$id.";
                continue;
            }

            // Assign stash
            $stash->update([
                'package_id' => $package->id,
                'reserved_until' => now()->addDays(3),
                'is_package_in' => true,
            ]);

            // Log actualization (adjust fields as needed)
            $package->actualizations()->create([
                'route_remaining' => json_encode([]),
                'current_warehouse_id' => null,
                'next_warehouse_id' => null,
                'message' => 'in_delivery',
                'last_courier_id' => auth()->id(),
                'created_at' => now(),
            ]);

            $package->status = PackageStatus::IN_POSTMAT;
            $package->unlock_code = fake()->regexify('[A-Z0-9]{6}');
            $package->pickup_code = $package->unlock_code;
            $package->save();

            $successCount++;
        }

        return back()->with([
            'success' => "$successCount package(s) delivered.",
            'errors' => $errors,
        ]);
    }

    private function getDistanceBetween($fromId, $toId)
    {
        $conn = \App\Models\WarehouseConnection::where('from_warehouse_id', $fromId)
            ->where('to_warehouse_id', $toId)
            ->first();

        return $conn ? $conn->distance_km : null;
    }

    public function takeOrder(Request $request, $fromId, $toId)
    {
        $packageIds = $request->input('packages', []);
        $packages = Package::whereIn('id', $packageIds)->get();

        foreach ($packages as $package) {
            $package->advancePackage();
            $package->status = PackageStatus::IN_TRANSIT;
            $package->save();
        }

        return back()->with('status', 'Order from warehouse ' . Warehouse::find($fromId)->city . ' to ' . Warehouse::find($toId)->city . ' taken!');
    }

    public function myPackages()
    {
        $packages = auth()->user()->staff->currentPackages();

        return view('postmat.delivery.my_packages', compact('packages'));
    }
}
