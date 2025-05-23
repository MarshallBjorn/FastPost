<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['first_name' => 'Admin', 'last_name' => 'Adminowicz', 'email' => 'admin@email.com', 'phone' => '420692137', 'password' => 'admin123'],
        ];

        foreach($users as $user) {
            $u = User::create([
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
                'email_verified_at' => now(),
                'phone' => $user['phone'],
                'password' => Hash::make($user['password']),
                'remember_token' => Str::random(10),
            ]);

            Staff::create([
                'user_id' => $u->id,
                'staff_type' => 'admin',
                'warehouse_id' => null,
                'hire_date' => now(),
                'termination_date' => null
            ]);
        }

        User::factory()->count(50)->create();
    }
}
