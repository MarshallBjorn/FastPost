<?php

namespace App\Jobs;

use App\Models\Stash;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ClearExpiredStashReservationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        Log::channel('jobs')->info("Started ClearExpiredStashReservationsJob");

        $count = Stash::where('reserved_until', '<=', now())
            ->whereNotNull('reserved_until')
            ->where('is_package_in', false)
            ->update([
                'package_id' => null,
                'reserved_until' => null,
                'is_package_in' => false,
            ]);

        Log::channel('jobs')->info("Cleared {$count} expired stash reservations via queue");
    }
}