<?php

namespace App\Http\Controllers\Delivery;

use App\Enums\PackageStatus;
use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Actualization;
use App\Models\Package;
use App\Models\Warehouse;
use App\Models\WarehouseConnection;
use Illuminate\Http\Request;
use Log;

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

            $response = $this->getRouteStatus($routeData['from']->id, $routeData['to']->id);
            $data = json_decode($response->getContent(), true);
            $status = $data['status'] ?? 'available';

            $routeData['status'] = $status;

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

        // Check if route already exists between from and to warehouses
        $route = Route::where('from_warehouse_id', $fromId)
            ->where('to_warehouse_id', $toId)
            ->first();

        if (!$route) {
            // Create new route record
            $route = Route::create([
                'from_warehouse_id' => $fromId,
                'to_warehouse_id' => $toId,
                'courier_id' => $courierId,
                'status' => 'en_route',
            ]);
        } else {
            // Update courier and status if route already exists but not assigned or taken
            $route->update([
                'courier_id' => $courierId,
                'status' => 'en_route',
            ]);
        }

        // Load relevant packages on this route in transit
        $packages = Package::with('latestActualization')
            ->where('status', PackageStatus::IN_TRANSIT)
            ->get()
            ->filter(function ($package) use ($fromId, $toId) {
                $a = $package->latestActualization;
                return $a &&
                    $a->route_remaining &&
                    $a->message === 'in_warehouse' &&
                    $a->current_warehouse_id == $fromId &&
                    $a->next_warehouse_id == $toId;
            });

        foreach ($packages as $package) {
            $package->actualizations()->create([
                'current_warehouse_id' => $fromId,
                'next_warehouse_id' => $toId,
                'message' => 'in_warehouse',
                'created_at' => now(),
                'last_courier_id' => auth()->user()->id,
                'route_remaining' => $package->latestActualization->route_remaining, // keep same or adjust
            ]);
            
            $package->update(['status' => PackageStatus::IN_TRANSIT]);
        }

        return back()->with('status', 'Route taken from ' . Warehouse::find($fromId)->city . ' to ' . Warehouse::find($toId)->city);
    }

    public function confirmArrival(Request $request, $fromId, $toId)
    {
        $courierId = auth()->id();

        $route = Route::where('from_warehouse_id', $fromId)
            ->where('to_warehouse_id', $toId)
            ->where('courier_id', $courierId)
            ->first();

        if (!$route) {
            return back()->withErrors('Route not found or not taken by you.');
        }

        // Fetch actualizations filtered by courier_id, from and to warehouses, and other conditions
        $actualizations = Actualization::where([
            ['current_warehouse_id', $fromId],
            ['next_warehouse_id', $toId],
            ['last_courier_id', $courierId],
            ['message', 'in_warehouse'],
        ])->get();

        foreach ($actualizations as $actualization) {
            $package = $actualization->package;
            $routeRemaining = json_decode($actualization->route_remaining, true);

            if (is_array($routeRemaining)) {
                array_shift($routeRemaining);
            }

            $nextWarehouseId = (count($routeRemaining) > 0) ? $routeRemaining[0] : null;

            // Create new actualization for arrival confirmation
            $newActualization = Actualization::create([
                'package_id' => $package->id,
                'route_remaining' => json_encode($routeRemaining),
                'current_warehouse_id' => $toId,
                'next_warehouse_id' => $nextWarehouseId,
                'message' => 'in_warehouse',
                'last_courier_id' => null,
                'created_at' => now(),
            ]);
        }

        // Update route status to arrived, clear courier assignment (or adjust as per your logic)
        $route->update([
            'status' => 'arrived',
        ]);

        return back()->with('status', 'Arrival confirmed at ' . Warehouse::find($toId)->city);
    }

    public function confirmReturn(Request $request, $fromId, $toId)
    {
        $courierId = auth()->id();

        $route = Route::where('from_warehouse_id', $fromId)
            ->where('to_warehouse_id', $toId)
            ->where('courier_id', $courierId)
            ->first();

        if (!$route) {
            return back()->withErrors('Return route not found or not taken by you.');
        }

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

        }

        $route->update([
            'status' => 'available',
            'courier_id' => null,
        ]);

        return back()->with('status', 'Returned to ' . Warehouse::find($toId)->city);
    }

    public function startReturnTrip()
    {
        $courier = auth()->user();
        $staff = $courier->staff;
        $motherWarehouseId = $staff->warehouse_id;

        $route = Route::where('from_warehouse_id', $motherWarehouseId)
            ->where('courier_id', $courier->id)
            ->first();

        $packages = Package::with('latestActualization')
            ->where('status', PackageStatus::IN_TRANSIT)
            ->get()
            ->filter(function ($package) use ($motherWarehouseId) {
                $a = $package->latestActualization;
                if (!$a) return false;

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

        $route->update([
            'status' => 'returning',
        ]);

        return back()->with('status', 'Started return trip to your mother warehouse.');
    }

    public function getRouteStatus(int $fromId, int $toId)
    {
        $route = Route::where('from_warehouse_id', $fromId)
            ->where('to_warehouse_id', $toId)
            ->first();

        if (!$route) {
            return response()->json(['status' => 'available']);
        }

        return response()->json(['status' => $route->status]);
    }

    public function myPackages()
    {
        $packages = auth()->user()->staff->currentPackages();

        return view('warehouse.delivery.my_packages', compact('packages'));
    }
}
