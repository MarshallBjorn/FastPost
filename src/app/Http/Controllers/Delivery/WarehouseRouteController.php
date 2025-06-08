<?php

namespace App\Http\Controllers\Delivery;

use App\Enums\PackageStatus;
use App\Http\Controllers\Controller;
use App\Models\WarehouseConnection;
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
            $a = $package->latestActualization;
            $ids = [$a->current_warehouse_id, $a->next_warehouse_id];
            sort($ids); // always smallest-first, ensures 1-2 and 2-1 are grouped
            return implode('-', $ids);
        });
        
        $routes = [];

        foreach ($grouped as $key => $groupPackages) {
            [$idA, $idB] = explode('-', $key) + [null, null];

            if (!is_numeric($idA) || !is_numeric($idB)) {
                continue;
            }

            $fromAtoB = $groupPackages->filter(function ($p) use ($idA, $idB) {
                $a = $p->latestActualization;
                return $a->current_warehouse_id == $idA && $a->next_warehouse_id == $idB;
            });

            $fromBtoA = $groupPackages->filter(function ($p) use ($idA, $idB) {
                $a = $p->latestActualization;
                return $a->current_warehouse_id == $idB && $a->next_warehouse_id == $idA;
            });

            // Only continue if there are packages in either direction
            if ($fromAtoB->count() === 0 && $fromBtoA->count() === 0) {
                continue; // skip routes without any packages
            }

            $routeData = [
                'from' => Warehouse::find($idA),
                'to' => Warehouse::find($idB),
                'count_to_deliver' => $fromAtoB->count(),
                'count_to_return' => $fromBtoA->count(),
                'distance' => $this->getDistanceBetween($idA, $idB) ?? $this->getDistanceBetween($idB, $idA),
            ];

            $courierId = auth()->id();

            $routeData['status'] = $this->getRouteStatus($routeData, $currentWarehouseId, $courierId);

            $routes[$key] = $routeData;
        }

        return view('warehouse.delivery.index', compact('routes', 'packages'));
    }    

    private function getDistanceBetween($fromId, $toId)
    {
        if (!$fromId || !$toId || !is_numeric($fromId) || !is_numeric($toId)) {
            return null;
        }

        return WarehouseConnection::where('from_warehouse_id', $fromId)
            ->where('to_warehouse_id', $toId)
            ->value('distance_km');
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
                    ->where('message', 'in_warehouse');
            })
            ->get();

        foreach ($packages as $package) {
            $latest = $package->latestActualization;
            $routeRemaining = json_decode($latest->route_remaining, true);

            if (is_array($routeRemaining)) {
                array_shift($routeRemaining);
            }

            $nextWarehouseId = is_array($routeRemaining) && count($routeRemaining) > 0
                ? $routeRemaining[0]
                : null;

            Actualization::create([
                'package_id' => $package->id,
                'route_remaining' => json_encode($routeRemaining),
                'current_warehouse_id' => $toId,
                'next_warehouse_id' => $nextWarehouseId,
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
                $query->where('current_warehouse_id', $toId)
                    ->where('next_warehouse_id', $fromId)
                    ->where('last_courier_id', $courierId)
                    ->where('message', 'in_warehouse');
            })
            ->get();

        foreach ($packages as $package) {
            $latest = $package->latestActualization;
            $routeRemaining = json_decode($latest->route_remaining, true);

            if (is_array($routeRemaining)) {
                array_shift($routeRemaining);
            }

            $nextWarehouseId = is_array($routeRemaining) && count($routeRemaining) > 0
                ? $routeRemaining[0]
                : null;

            Actualization::create([
                'package_id' => $package->id,
                'route_remaining' => json_encode($routeRemaining),
                'current_warehouse_id' => $fromId,
                'next_warehouse_id' => $nextWarehouseId,
                'message' => 'in_warehouse',
                'last_courier_id' => null,
                'created_at' => now(),
            ]);

            // $package->advancePackage();
        }

        return back()->with('status', 'Returned to ' . Warehouse::find($toId)->city);
    }

    public function startReturnTrip()
    {
        $courier = auth()->user();
        $staff = $courier->staff;
        $motherWarehouseId = $staff->warehouse_id;

        // Get packages that are IN TRANSIT, currently in some warehouse (not mother),
        // have no next_warehouse assigned (stuck), but have motherWarehouseId in route_remaining
        $packages = Package::with('latestActualization')
            ->where('status', PackageStatus::IN_TRANSIT)
            ->get()
            ->filter(function ($package) use ($motherWarehouseId) {
                $a = $package->latestActualization;
                if (!$a) return false;

                // Decode route_remaining to array, must be valid
                $routeRemaining = json_decode($a->route_remaining, true);
                if (!is_array($routeRemaining) || empty($routeRemaining)) {
                    return false;
                }

                return
                    $a->message === 'in_warehouse' &&
                    $a->current_warehouse_id !== $motherWarehouseId &&
                    $a->next_warehouse_id === $motherWarehouseId &&
                    in_array($motherWarehouseId, $routeRemaining);
            });

        foreach ($packages as $package) {
            $a = $package->latestActualization;
            $routeRemaining = json_decode($a->route_remaining, true);

            // The next warehouse is the first warehouse in the route_remaining
            $nextWarehouseId = $routeRemaining[0];

            Actualization::create([
                'package_id' => $package->id,
                'route_remaining' => $a->route_remaining,  // keep same route_remaining for now
                'current_warehouse_id' => $a->current_warehouse_id,
                'next_warehouse_id' => $nextWarehouseId,
                'message' => 'in_warehouse',
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
    private function getRouteStatus($route, $currentWarehouseId, $courierId)
    {
        $from = $route['from']->id;
        $to = $route['to']->id;

        $packages = Package::with('latestActualization')->get();

        $enRoutePackages = $packages->filter(function ($package) use ($from, $to, $courierId) {
            $a = $package->latestActualization;
            return $a &&
                $a->current_warehouse_id == $from &&
                $a->next_warehouse_id == $to &&
                $a->message === 'in_warehouse' &&
                $a->last_courier_id == $courierId;
        });

        if ($enRoutePackages->count() > 0) {
            return 'en_route';
        }

        $returnPackages = $packages->filter(function ($package) use ($from, $to, $courierId) {
            $a = $package->latestActualization;
            return $a &&
                $a->current_warehouse_id == $to &&
                $a->next_warehouse_id == $from &&
                $a->message === 'in_warehouse' &&
                $a->last_courier_id == $courierId;
        });

        if ($returnPackages->count() > 0) {
            return 'returning';
        }

        // Check if there are packages waiting to be delivered (in warehouse waiting to depart)
        $waitingToDeliver = $packages->filter(function ($package) use ($from, $to) {
            $a = $package->latestActualization;
            return $a &&
                $a->current_warehouse_id == $from &&
                $a->next_warehouse_id == $to &&
                $a->message === 'in_warehouse' &&
                $a->last_courier_id === null;
        });

        if ($waitingToDeliver->count() > 0) {
            return 'available';
        }

        // Check if packages have arrived at destination warehouse (waiting to return)
        $waitingToReturn = $packages->filter(function ($package) use ($from, $to) {
            $a = $package->latestActualization;
            return $a &&
                $a->current_warehouse_id == $to &&
                $a->next_warehouse_id == $from &&
                $a->message === 'in_warehouse' &&
                $a->last_courier_id === null;
        });

        if ($waitingToReturn->count() > 0) {
            return 'available';
        }

        // No packages anywhere means route is really free to be taken (or hidden)
        return 'available';
    }

}
