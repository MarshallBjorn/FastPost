<?php

namespace Database\Factories;

use App\Models\Postmat;
use App\Models\User;
use App\Services\Pathfinder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Package>
 */

class PackageFactory extends Factory
{
    public function definition(): array
    {
        $start_postmat = Postmat::inRandomOrder()->first();
        do {
            $destination_postmat = Postmat::inRandomOrder()->first();
        } while ($destination_postmat === $start_postmat);

        $pathfinder = new Pathfinder();
        $route_path = json_encode($pathfinder->findPath($start_postmat->warehouse_id, $destination_postmat->warehouse_id));

        return [
            'sender_id' => User::factory(),
            'receiver_id' => User::factory(),
            'start_postmat_id' => $start_postmat->id,
            'destination_postmat_id' => $destination_postmat->id,
            'receiver_email' => fake()->safeEmail(),
            'receiver_phone' => fake()->phoneNumber(),
            'status' => fake()->randomElement(['registered', 'in_transit', 'collected']),
            'sent_at' => now()->subDays(rand(1, 7)),
            'delivered_at' => fake()->optional()->dateTimeBetween('-5 days', 'now'),
            'collected_at' => fake()->optional()->dateTimeBetween('-3 days', 'now'),
            'pickup_code' => null,
            'route_path' => $route_path
        ];
    }
}
