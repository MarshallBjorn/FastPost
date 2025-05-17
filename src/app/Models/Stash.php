<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Stash extends Model
{
    /** @use HasFactory<\Database\Factories\StashFactory> */
    use HasFactory, Notifiable;

    protected $table = 'stashes';

    protected $fillable = [
        'postmat_id',
        'size',
        'package_id',
        'reserved_until',
        'is_package_in'
    ];

    protected $casts = [
        'reserved_until' => 'datetime',
        'is_package_in' => 'boolean'
    ];

    public function postmat()
    {
        return $this->belongsTo(Postmat::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    /**
     * Check if the stash is currently reserved
     */
    public function isReserved()
    {
        return $this->reserved_until && $this->reserved_until->isFuture();
    }

    /**
     * Check if the stash is available
     */
    public function isAvailable()
    {
        return !$this->package_id ||
            ($this->reserved_until && $this->reserved_until->isPast());
    }

    /**
     * Reserve the stash for a package
     */
    public function reserveFor(Package $package)
    {
        $this->update([
            'package_id' => $package->id,
            'reserved_until' => now()->addHours(24),
            'is_package_in' => false
        ]);
    }

    /**
     * Mark package as placed in the stash
     */
    public function markPackageDelivered()
    {
        $this->update(['is_package_in' => true]);
    }

    /**
     * Clear the reservation
     */
    public function clearReservation()
    {
        $this->update([
            'package_id' => null,
            'reserved_until' => null,
            'is_package_in' => false
        ]);
    }
}
