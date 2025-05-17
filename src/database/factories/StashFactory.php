<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\Postmat;
use App\Models\Stash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stash>
 */
class StashFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        $hasPackage = fake()->boolean(50);

        Postmat::all()->each(function ($postmat)  {
            $hasPackage = fake()->boolean(50);

            $stashData = [
                'postmat_id' => $postmat->id,
                'size' => fake()->randomElement(['S', 'M', 'L']),
            ];

            if ($hasPackage) {
                $package = Package::factory()->create([
                    'status' => 'in_postmat',
                    'pickup_code' => fake()->regexify('[A-Z0-9]{6}'),
                ]);
                $stashData['package_id'] = $package->id;
            }

            Stash::create($stashData);
        });
    }
}
