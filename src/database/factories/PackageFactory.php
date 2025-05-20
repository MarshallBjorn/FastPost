<?php

namespace Database\Factories;

use App\Models\Postmat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Package>
 */

class PackageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sender_id' => User::factory(),
            'receiver_id' => User::factory(),
            'destination_postmat_id' => Postmat::inRandomOrder()->first()?->id,
            'receiver_email' => fake()->safeEmail(),
            'receiver_phone' => fake()->phoneNumber(),
            'status' => fake()->randomElement(['registered', 'in_transit', 'collected']),
            'sent_at' => now()->subDays(rand(1, 7)),
            'delivered_at' => fake()->optional()->dateTimeBetween('-5 days', 'now'),
            'collected_at' => fake()->optional()->dateTimeBetween('-3 days', 'now'),
            'pickup_code' => null,
        ];
    }
}
