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
        $faker = \Faker\Factory::create();

        Postmat::all()->each(function ($postmat) use ($faker) {
            $hasPackage = $faker->boolean(50);

            $stashData = [
                'postmat_id' => $postmat->id,
                'size' => $faker->randomElement(['S', 'M', 'L']),
            ];

            if ($hasPackage) {
                $package = Package::factory()->create([
                    'status' => 'in_postmat',
                    'pickup_code' => $faker->regexify('[A-Z0-9]{6}'),
                ]);
                $stashData['package_id'] = $package->id;
            }

            Stash::create($stashData);
        });
    }
}
