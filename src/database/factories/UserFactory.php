<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Staff;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'phone' => fake()->unique()->phoneNumber(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            // 70% chance this user becomes staff
            if (fake()->boolean(70)) {
                $staffType = fake()->randomElement(['admin', 'courier', 'warehouse']);

                Staff::create([
                    'user_id' => $user->id,
                    'staff_type' => $staffType,
                    'warehouse_id' => $staffType === 'warehouse'
                        ? Warehouse::inRandomOrder()->first()?->id
                        : null,
                    'hire_date' => now()->subMonths(rand(1, 24)),
                    'termination_date' => fake()->boolean(20)
                        ? now()->subMonths(rand(0, 6))
                        : null,
                ]);
            }
        });
    }
}
