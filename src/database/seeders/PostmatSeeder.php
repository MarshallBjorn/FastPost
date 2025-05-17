<?php

namespace Database\Seeders;

use App\Models\Postmat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostmatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Postmat::factory()->count(10)->create();
    }
}
