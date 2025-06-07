<?php

namespace App\Http\Controllers\Delivery;

use App\Enums\PackageStatus;
use App\Http\Controllers\Controller;
use App\Models\Actualization;
use App\Models\Package;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseRouteController extends Controller
{
    public function index(Request $request)
    {
        $currentWarehouseId = auth()->user()->staff->warehouse_id;

        $packages = Package::with('latestActualization')
            ->where('status', PackageStatus::IN_TRANSIT)
            ->get()
            ->filter(function ($package) use ($currentWarehouseId) {
                $actualization = $package->latestActualization;
                return $actualization &&
                    $actualization->route_remaining &&
                    $actualization->message == 'in_warehouse' &&
                    ($actualization->current_warehouse_id == $currentWarehouseId ||
                    $actualization->next_warehouse_id == $currentWarehouseId);
            });

        $grouped = $packages->groupBy(function ($package) {
            $actualization = $package->latestActualization;
            return $actualization->current_warehouse_id . '-' . $actualization->next_warehouse_id;
        });

        $routes = [];

        foreach ($grouped as $key => $groupPackages) {
            // Parse from and to warehouse IDs from key
            [$fromId, $toId] = explode('-', $key);

            $distance = $this->getDistanceBetween($fromId, $toId);

            // Calculate return count: number of packages on return trip (reverse route)
            $returnKey = $toId . '-' . $fromId;
            $returnCount = $grouped->has($returnKey) ? $grouped[$returnKey]->count() : 0;

            $routes[$key] = [
                'from' => Warehouse::find($fromId),
                'to' => Warehouse::find($toId),
                'count' => $groupPackages->count(),
                'return_count' => $returnCount,
                'distance' => $distance,
                'packages' => $groupPackages->values(), // reset keys for cleaner iteration
            ];
        }

        return view('warehouse.delivery.index', compact('routes', 'packages'));
    }

    private function getDistanceBetween($fromId, $toId)
    {
        $conn = \App\Models\WarehouseConnection::where('from_warehouse_id', $fromId)
            ->where('to_warehouse_id', $toId)
            ->first();

        return $conn ? $conn->distance_km : null;
    }

    public function takeRoute(Request $request, $fromId, $toId)
    {
        $courierId = auth()->id();

        $packages = Package::with('latestActualization')
            ->where('status', PackageStatus::IN_TRANSIT)
            ->get()
            ->filter(function ($package) use ($fromId, $toId) {
                $actualization = $package->latestActualization;
                return $actualization &&
                    $actualization->current_warehouse_id == $fromId &&
                    $actualization->next_warehouse_id == $toId &&
                    $actualization->message === 'in_warehouse';
            });

        foreach ($packages as $package) {
            Actualization::create([
                'package_id' => $package->id,
                'route_remaining' => $package->latestActualization->route_remaining,
                'current_warehouse_id' => $fromId,
                'next_warehouse_id' => $toId,
                'message' => 'in_warehouse',
                'last_courier_id' => $courierId,
                'created_at' => now(),
            ]);
        }

        return back()->with('status', 'Route taken from ' . Warehouse::find($fromId)->city . ' to ' . Warehouse::find($toId)->city);
    }

    public function confirmArrival(Request $request, $fromId, $toId)
    {
        $courierId = auth()->id();

        $packages = Package::with('latestActualization')
            ->whereHas('latestActualization', function ($query) use ($fromId, $toId, $courierId) {
                $query->where('current_warehouse_id', $fromId)
                    ->where('next_warehouse_id', $toId)
                    ->where('last_courier_id', $courierId)
                    ->where('message', 'in_transit');
            })
            ->get();

        foreach ($packages as $package) {
            $latest = $package->latestActualization;
            $routeRemaining = json_decode($latest->route_remaining, true);

            if (is_array($routeRemaining)) {
                array_shift($routeRemaining); // Remove the just-finished segment
            }

            Actualization::create([
                'package_id' => $package->id,
                'route_remaining' => json_encode($routeRemaining),
                'current_warehouse_id' => $toId,
                'next_warehouse_id' => null,
                'message' => 'in_warehouse',
                'last_courier_id' => null,
                'created_at' => now(),
            ]);
        }

        return back()->with('status', 'Arrival confirmed at ' . Warehouse::find($toId)->city);
    }

    public function confirmReturn(Request $request, $fromId, $toId)
    {
        $courierId = auth()->id();

        $packages = Package::with('latestActualization')
            ->whereHas('latestActualization', function ($query) use ($fromId, $toId, $courierId) {
                $query->where('current_warehouse_id', $fromId)
                    ->where('next_warehouse_id', $toId)
                    ->where('last_courier_id', $courierId)
                    ->where('message', 'in_transit');
            })
            ->get();

        foreach ($packages as $package) {
            $latest = $package->latestActualization;
            $routeRemaining = json_decode($latest->route_remaining, true);

            if (is_array($routeRemaining)) {
                array_shift($routeRemaining);
            }

            Actualization::create([
                'package_id' => $package->id,
                'route_remaining' => json_encode($routeRemaining),
                'current_warehouse_id' => $toId,
                'next_warehouse_id' => null,
                'message' => 'in_warehouse',
                'last_courier_id' => null,
                'created_at' => now(),
            ]);

            $package->advancePackage(); // Optional
        }

        return back()->with('status', 'Returned to ' . Warehouse::find($toId)->city);
    }

    public function startReturnTrip()
    {
        $courier = auth()->user();
        $staff = $courier->staff;
        $motherWarehouseId = $staff->warehouse_id;

        // Find packages that are IN this warehouse and should go BACK to mother
        $packages = Package::with('latestActualization')
            ->where('status', PackageStatus::IN_TRANSIT)
            ->get()
            ->filter(function ($package) use ($motherWarehouseId, $staff) {
                $a = $package->latestActualization;
                return $a &&
                    $a->message === 'in_warehouse' &&
                    $a->current_warehouse_id !== $motherWarehouseId &&
                    $a->next_warehouse_id === null &&
                    (
                        is_array(json_decode($a->route_remaining, true)) &&
                        in_array($motherWarehouseId, array_column(json_decode($a->route_remaining, true), 'to'))
                    );
            });

        foreach ($packages as $package) {
            $a = $package->latestActualization;
            Actualization::create([
                'package_id' => $package->id,
                'route_remaining' => $a->route_remaining,
                'current_warehouse_id' => $a->current_warehouse_id,
                'next_warehouse_id' => $motherWarehouseId,
                'message' => 'in_transit',
                'last_courier_id' => $courier->id,
                'created_at' => now(),
            ]);
        }

        return back()->with('status', 'Started return trip to your mother warehouse.');
    }

    public function myPackages()
    {
        $packages = auth()->user()->staff->currentPackages();

        return view('warehouse.delivery.my_packages', compact('packages'));
    }
}
