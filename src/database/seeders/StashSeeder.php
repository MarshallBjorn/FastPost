<?php

namespace Database\Seeders;

use App\Models\Stash;
use Illuminate\Database\Seeder;
use App\Models\Postmat;
use App\Models\Package;

    /**
     * Run the database seeds.
     */

class StashSeeder extends Seeder
{
    public function run(): void
    {
        Postmat::all()->each(function ($postmat) {
            for ($i = 0; $i < 30; $i++) {
                $stashData = [
                    'postmat_id' => $postmat->id,
                    'size' => fake()->randomElement(['S', 'M', 'L']),
                ];

                if (fake()->boolean(50)) {
                    $package = Package::factory()->create([
                        'status' => 'in_postmat',
                        'pickup_code' => fake()->regexify('[A-Z0-9]{6}'),
                    ]);
                    $stashData['package_id'] = $package->id;
                }

                Stash::create($stashData);
            }
        });
    }
}
