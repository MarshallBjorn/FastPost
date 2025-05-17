<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;
use App\Models\Postmat;
use App\Models\User;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->count() < 2) {
            User::factory(2)->create(); // Ensure you have enough users
            $users = User::all();
        }

        Postmat::all()->each(function ($postmat) use ($users) {
            $sender = $users->random();
            $receiver = $users->random();

            Package::factory()->create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'receiver_email' => $receiver->email,
                'receiver_phone' => $receiver->phone,
                'destination_postmat_id' => $postmat->id,
            ]);
        });
    }
}
