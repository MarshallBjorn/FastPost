<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Actualization>
 */
class ActualizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'package_id' => Package::factory(),
            'message' => fake()->randomElement([
                'sent',
                'in_warehouse',
                'in_delivery'
            ]),
            'last_courier_id' => User::where('user_type', 'courier')->inRandomOrder()->first()?->id ?? User::factory()->create(['user_type' => 'courier'])->id,
            'created_at' => fake()->dateTimeBetween('-10 days', 'now'),
        ];
    }
}
