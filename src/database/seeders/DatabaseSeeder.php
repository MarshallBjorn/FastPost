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
            StashSeeder::class,
            UserSeeder::class,
            PackageSeeder::class,
            ActualizationSeeder::class,
            WarehouseSeeder::class,
            WarehouseConnectionSeeder::class
        ]);
    }
}
