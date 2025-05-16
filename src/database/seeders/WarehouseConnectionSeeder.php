<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use App\Models\WarehouseConnection;
use Illuminate\Database\Seeder;

class WarehouseConnectionSeeder extends Seeder
{
    public function run(): void
    {
        $pairs = [
            [1, 2, 180.25],
            [1, 3, 250.80],
            [2, 6, 95.20],
            [2, 10, 205.30],
            [3, 5, 120.00],
            [3, 6, 300.00],
            [4, 1, 160.00],
            [4, 9, 219.00],
            [5, 1, 313.00],
            [5, 9, 266.00],
            [5, 4, 331.00],
            [7, 10, 253.30],
            [7, 8, 421.50],
            [7, 1, 240.00],
            [8, 1, 350.00]
        ];

        foreach ($pairs as [$from, $to, $distance]) {
            WarehouseConnection::create([
                'from_warehouse_id' => $from,
                'to_warehouse_id' => $to,
                'distance_km' => $distance
            ]);

            WarehouseConnection::create([
                'from_warehouse_id' => $to,
                'to_warehouse_id' => $from,
                'distance_km' => $distance
            ]);
        }
    }
}
