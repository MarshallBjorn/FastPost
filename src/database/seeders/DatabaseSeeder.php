<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            WarehouseSeeder::class,
            WarehouseConnectionSeeder::class,
            PackageSeeder::class,
            StashSeeder::class,
            UserSeeder::class,
            ActualizationSeeder::class,
        ]);
    }
}
