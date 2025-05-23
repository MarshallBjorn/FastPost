<?php

namespace Database\Factories;

use App\Models\Actualization;
use App\Models\Package;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Actualization>
 */
class ActualizationFactory extends Factory
{
    protected $model = Actualization::class;

    public function definition(): array
    {
        $courierStaff = Staff::where('staff_type', 'courier')->inRandomOrder()->first();

        // If no courier staff exists, create one (and a linked user)
        if (!$courierStaff) {
            $courierStaff = Staff::factory()->state(['staff_type' => 'courier'])->create();
        }

        $package = Package::inRandomOrder()->first();

        return [
            'package_id' => $package,
            'message' => $this->faker->randomElement([
                'sent',
                'in_warehouse',
                'in_delivery'
            ]),
            'route_remaining' => $package->route_path,
            'last_courier_id' => $courierStaff->user_id,
            'created_at' => $this->faker->dateTimeBetween('-10 days', 'now'),
        ];
    }
}
