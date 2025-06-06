<?php

namespace App\Http\Controllers\Delivery;

use App\Enums\PackageStatus;
use App\Http\Controllers\Controller;
use App\Models\Actualization;
use App\Models\Postmat;
use App\Models\Package;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class PostmatRouteController extends Controller
{

    public function index(Request $request)
    {
        $currentWarehouseId = auth()->user()->staff->warehouse_id;
        $routes = [];

        // Get all registered or in_transit packages that are in any stash with is_package_in = true
        $packages = Package::whereIn('status', [PackageStatus::REGISTERED->value, PackageStatus::IN_TRANSIT->value])
            ->whereHas('stash', function (Builder $query) {
                $query->where('is_package_in', true);
            })
            ->get();

        foreach ($packages as $package) {
            $actualization = $package->latestActualization;

            if (!$actualization || !$actualization->route_remaining) {
                continue;
            }

            $route = json_decode($actualization->route_remaining, true);

            if (!is_array($route) || count($route) < 1) {
                continue;
            }

            $nextWarehouseId = $route[0]; // Next step should be the delivery guy’s warehouse

            if ($nextWarehouseId != $currentWarehouseId) {
                continue;
            }

            // Start postmat is now from the package field
            $postmat = Postmat::where('id', $package->start_postmat_id)
                ->where('status', 'active')
                ->first();

            if (!$postmat) {
                continue;
            }

            if (!isset($routes[$postmat->id])) {
                $distance = \App\Utils\DistanceUtils::haversineDistance(
                    $postmat->latitude,
                    $postmat->longitude,
                    $postmat->warehouse->latitude,
                    $postmat->warehouse->longitude
                );

                $routes[$postmat->id] = [
                    'postmat' => $postmat,
                    'count' => 0,
                    'distance' => $distance,
                ];
            }

            $routes[$postmat->id]['count']++;
        }

        return view('postmat.delivery.index', [
            'routes' => $routes,
        ]);
    }

    public function pickup(Request $request, $postmatId)
    {
        $user = auth()->user();
        $currentWarehouseId = $user->staff->warehouse_id;

        // Load the Postmat first and check warehouse assignment
        $postmat = Postmat::where('id', $postmatId)
            ->where('status', 'active')
            ->first();

        if (!$postmat) {
            return redirect()->route('postmat.delivery.index')
                ->with('error', 'Postmat not found or inactive.');
        }

        if ($postmat->warehouse_id != $currentWarehouseId) {
            return redirect()->route('postmat.delivery.index')
                ->with('error', 'You are not authorized to pick up packages from this postmat.');
        }

        // Get all packages from this postmat going to this warehouse
        $packages = Package::where('start_postmat_id', $postmatId)
            ->where('status', PackageStatus::REGISTERED)
            ->get();

        foreach ($packages as $package) {
            $actualization = $package->latestActualization;

            if (!$actualization || !$actualization->route_remaining) {
                continue;
            }

            $route = json_decode($actualization->route_remaining, true);

            if (!is_array($route) || count($route) < 1) {
                continue;
            }

            $nextWarehouseId = $route[0];

            // Make sure this package is going to this warehouse
            if ($nextWarehouseId != $currentWarehouseId) {
                continue;
            }

            // Update route_remaining (remove first step)
            array_shift($route);

            // Create new Actualization
            Actualization::create([
                'package_id' => $package->id,
                'route_remaining' => json_encode($route),
                'current_warehouse_id' => $currentWarehouseId,
                'next_warehouse_id' => $route[0] ?? null, // could be null if last step
                'message' => 'collected',
                'last_courier_id' => $user->id,
                'created_at' => now(),
            ]);

            // Update package status
            $package->status = PackageStatus::IN_TRANSIT;
            $package->save();
        }

        return redirect()->route('postmat.delivery.index')
            ->with('success', 'Packages picked up successfully!');
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
