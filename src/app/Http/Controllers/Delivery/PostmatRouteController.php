<?php

namespace App\Http\Controllers\Delivery;

use App\Enums\PackageStatus;
use App\Http\Controllers\Controller;
use App\Models\Actualization;
use App\Models\Postmat;
use App\Models\Package;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class PostmatRouteController extends Controller
{
    public function index(Request $request)
    {
        $currentWarehouseId = auth()->user()->staff->warehouse_id;

        // Get all registered packages
        $packages = Package::where('status', 'registered')->get();

        $routes = [];

        foreach ($packages as $package) {
            $actualization = $package->latestActualization;

            if (!$actualization || !$actualization->route_remaining) {
                continue;
            }

            $route = json_decode($actualization->route_remaining, true);

            if (!is_array($route) || count($route) < 2) {
                continue;
            }

            $startPostmatId = $route[0];
            $nextWarehouseId = $route[1];

            // Only show packages being sent TO the current warehouse
            if ($nextWarehouseId != $currentWarehouseId) {
                continue;
            }

            // Don't filter by postmat's warehouse_id here
            $postmat = Postmat::find($startPostmatId)
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
}
