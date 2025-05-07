<?php

namespace Database\Seeders;

use App\Models\Actualization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActualizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Actualization::factory()->count(200)->create();
    }
}
