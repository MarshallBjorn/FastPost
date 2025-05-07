<?php

namespace Database\Seeders;

use App\Models\Stash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StashSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Stash::factory()->count(100)->create();
    }
}
