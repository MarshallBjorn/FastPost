<?php

namespace Database\Seeders;

use App\Models\Postmat;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Str;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = [
            ['city' => 'Warsaw', 'post_code' => '00-001', 'lat' => 52.2297, 'lng' => 21.0122],
            ['city' => 'Krakow', 'post_code' => '30-001', 'lat' => 50.0647, 'lng' => 19.9450],
            ['city' => 'Wroclaw', 'post_code' => '50-001', 'lat' => 51.1079, 'lng' => 17.0385],
            ['city' => 'Gdansk', 'post_code' => '80-001', 'lat' => 54.3520, 'lng' => 18.6466],
            ['city' => 'Poznan', 'post_code' => '60-001', 'lat' => 52.4064, 'lng' => 16.9252],
            ['city' => 'Katowice', 'post_code' => '40-001', 'lat' => 50.2649, 'lng' => 19.0238],
            ['city' => 'Lublin', 'post_code' => '20-001', 'lat' => 51.2465, 'lng' => 22.5684],
            ['city' => 'Bialystok', 'post_code' => '15-001', 'lat' => 53.1325, 'lng' => 23.1688],
            ['city' => 'Szczecin', 'post_code' => '70-001', 'lat' => 53.4285, 'lng' => 14.5528],
            ['city' => 'Rzeszow', 'post_code' => '35-001', 'lat' => 50.0413, 'lng' => 21.9990],
        ];

        foreach ($warehouses as $data) {
            $warehouse = Warehouse::create([
                'city' => $data['city'],
                'post_code' => $data['post_code'],
                'latitude' => $data['lat'],
                'longitude' => $data['lng'],
                'status' => 'active',
            ]);

            // Generate 5–15 nearby postmats
            $count = rand(5, 15);
            for ($i = 0; $i < $count; $i++) {
                $offsetLat = fake()->randomFloat(5, -0.2, 0.2); // ~22km latitude
                $offsetLng = fake()->randomFloat(5, -0.3, 0.3); // ~22km longitude (varies more w/ latitude)

                Postmat::create([
                    'name' => 'PM-' . strtoupper(Str::random(6)),
                    'city' => fake()->city(),
                    'post_code' => fake()->postcode(),
                    'latitude' => $data['lat'] + $offsetLat,
                    'longitude' => $data['lng'] + $offsetLng,
                    'status' => 'active',
                    'warehouse_id' => $warehouse->id,
                ]);
            }
        }
    }
}
