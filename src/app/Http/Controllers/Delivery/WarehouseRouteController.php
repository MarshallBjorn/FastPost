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

        $packages = Package::with('latestActualization')->where('status', PackageStatus::IN_TRANSIT)->get();

        foreach ($packages as $package) {
            $actualization = $package->latestActualization;

            // if (!$actualization) {  // package never moved, therefore at a starting location
            //     $package->advancePackage();
            //     $package->load('latestActualization'); // Reload the relationship
            //     $actualization = $package->latestActualization;
            // }

            if (
                !$actualization ||
                !$actualization->route_remaining ||
                $actualization->message != 'in_warehouse' ||
                !($actualization->current_warehouse_id == $currentWarehouseId ||
                    $actualization->next_warehouse_id == $currentWarehouseId)
            ) {
                continue;
            }

            // $path = json_decode($actualization->route_remaining, true);
            $start = $actualization->current_warehouse_id;
            $next = $actualization->next_warehouse_id;

            if (!$start || !$next) {
                continue;
            }

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
                        'packages' => [],
                    ];
                }

                $routes[$key]['count']++;
                $routes[$key]['packages'][] = $package;
            }

            // Return trip check
            if ($next == $currentWarehouseId) {
                $key = "$next-$start"; // Same as forward key reversed
                if (isset($routes[$key])) {
                    $routes[$key]['return_count']++;
                    $routes[$key]['packages'][] = $package;
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
