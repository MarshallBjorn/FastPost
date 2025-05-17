<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Postmat>
 */
class PostmatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => strtoupper(fake()->bothify('???##?')),
            'city' => fake()->city,
            'post_code' => fake()->postcode,
            'latitude' => fake()->latitude,
            'longitude' => fake()->longitude,
            'status' => 'active'
        ];
    }
}
