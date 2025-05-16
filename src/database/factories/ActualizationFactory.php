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

        return [
            'package_id' => Package::factory(),
            'message' => $this->faker->randomElement([
                'sent',
                'in_warehouse',
                'in_delivery'
            ]),
            'last_courier_id' => $courierStaff->user_id,
            'created_at' => $this->faker->dateTimeBetween('-10 days', 'now'),
        ];
    }
}
