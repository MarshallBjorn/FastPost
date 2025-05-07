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
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sender = User::inRandomOrder()->first() ?? User::factory()->create();
        $receiver = User::inRandomOrder()->first() ?? User::factory()->create();

        return [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'destination_postmat_id' => Postmat::factory(),
            'receiver_email' => $receiver->email,
            'receiver_phone' => $receiver->phone,
            'status' => fake()->randomElement(['registered', 'in_transit', 'collected']),
            'sent_at' => now()->subDays(rand(1, 7)),
            'delivered_at' => fake()->optional()->dateTimeBetween('-5 days', 'now'),
            'collected_at' => fake()->optional()->dateTimeBetween('-3 days', 'now'),
            'pickup_code' => null
        ];
    }
}
