<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\Postmat;
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

        return [
            'postmat_id' => Postmat::factory(),
            'size' => fake()->randomElement(['S', 'M', 'L']),
            'package_id' => $hasPackage
                ? Package::factory()->state(['status' => 'in_postmat', 'pickup_code' => fake()->regexify('[A-Z0-9]{6}')])
                : null,
        ];
    }
}
