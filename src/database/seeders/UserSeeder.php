<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'first_name' => 'root',
            'last_name' => 'root',
            'email' => 'root@post',
            'email_verified_at' => now(),
            'phone' => 0,
            'password' => Hash::make('password'),
        ]);

        Staff::create([
            'user_id' => $user->id,
            'staff_type' => 'admin',
            'hire_date' => now()
        ]);

        User::factory()->count(50)->create();
    }
}
