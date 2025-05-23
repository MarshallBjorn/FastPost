<?php

namespace App\Services;

use App\Models\Warehouse;
use App\Models\WarehouseConnection;
use App\Utils\DistanceUtils;

class Pathfinder {
    protected $distanceUtil;
    protected $warehouses;
    protected $connections;

    public function __construct()
    {
        $this->warehouses = Warehouse::all()->keyBy('id');
        $this->connections = WarehouseConnection::all()->groupBy('from_warehouse_id');
    }

    public function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // in km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) ** 2;

        $c = 2 * asin(sqrt($a));
        return $earthRadius * $c;
    }

    public function findPath($startWarehouseId, $endWarehouseId) {
        $openSet = [$startWarehouseId];
        $cameFrom = [];
        $gScore = [];
        $fScore = [];

        foreach ($this->warehouses as $id => $warehouse) {
            $gScore[$id] = INF;
            $fScore[$id] = INF;
        }

        $gScore[$startWarehouseId] = 0;
        $start = $this->warehouses[$startWarehouseId];
        $end = $this->warehouses[$endWarehouseId];

        $fScore[$startWarehouseId] = $this->haversineDistance($start->latitude, $start->longitude, $end->latitude, $end->longitude);

        while (!empty($openSet)) {
            $current = array_reduce($openSet, function($carry, $id) use ($fScore) {
                return ($carry == null || $fScore[$id] < $fScore[$carry]) ? $id : $carry;
            });

            if ($current == $endWarehouseId) {
                $path = [];

                while (isset($cameFrom[$current])) {
                    $path[] = $current;
                    $current = $cameFrom[$current];
                }
                $path[] = $startWarehouseId;
                return array_reverse($path);
            }

            $openSet = array_filter($openSet, fn($id) => $id != $current);

            foreach ($this->connections[$current] ?? [] as $connection) {
                $neighbor = $connection->to_warehouse_id;
                $tentative_gScore = $gScore[$current] + $connection->distance_km;

                if ($tentative_gScore < $gScore[$neighbor]) {
                    $cameFrom[$neighbor] = $current;
                    $gScore[$neighbor] = $tentative_gScore;

                    $neighborWarehouse = $this->warehouses[$neighbor];
                    $fScore[$neighbor] = $tentative_gScore + $this->haversineDistance(
                        $neighborWarehouse->latitude,
                        $neighborWarehouse->longitude,
                        $end->latitude,
                        $end->longitude
                    );

                    if (!in_array($neighbor, $openSet)) {
                        $openSet[] = $neighbor;
                    }
                }
            }
        }    
        return null;
    }
}