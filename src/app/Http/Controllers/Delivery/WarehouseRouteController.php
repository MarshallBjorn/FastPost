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
        $routes = [];

        $packages = Package::where('status', ['registered', 'in_transit'])->get();

        foreach ($packages as $package) {
            $path = json_decode($package->route_path, true);
            if (!$path || count($path) < 2) continue;

            $start = $path[0];
            $next = $path[1];

            // Forward trip
            if ($start == $currentWarehouseId) {
                $key = "$start-$next";
                if (!isset($routes[$key])) {
                    $distance = $this->getDistanceBetween($start, $next);
                    $routes[$key] = [
                        'from' => Warehouse::find($start),
                        'to' => Warehouse::find($next),
                        'count' => 0,
                        'return_count' => 0,
                        'distance' => $distance,
                    ];
                }

                $routes[$key]['count']++;
            }

            // Return trip check
            if ($next == $currentWarehouseId) {
                $key = "$next-$start"; // Same as forward key reversed
                if (isset($routes[$key])) {
                    $routes[$key]['return_count']++;
                }
            }
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

    public function takeOrder(Request $request, $fromId, $toId)
    {
        $packageIds = $request->input('packages', []);
        $packages = Package::whereIn('id', $packageIds)->get();

        foreach ($packages as $package) {
            $remaining = json_decode($package->route_path, true);

            array_shift($remaining); // move to next

            Actualization::create([
                'package_id' => $package->id,
                'message' => 'in_warehouse',
                'route_remaining' => json_encode($remaining),
                'last_courier_id' => auth()->user()->id,
                'created_at' => now(),
            ]);

            $package->route_path = json_encode($remaining);
            $package->status = PackageStatus::IN_TRANSIT;

            $package->save();
        }

        return back()->with('status', 'Order from warehouse ' . Warehouse::find($fromId)->city . ' to ' . Warehouse::find($toId)->city . ' taken!');
    }
}
