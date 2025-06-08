<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;

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

    /**
     * Relationships
     */
    public function postmat()
    {
        return $this->belongsTo(Postmat::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    /**
     * Scope: Available stashes
     * - No package currently inside
     * - Not reserved or reservation expired
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_package_in', false)
                     ->where(function ($q) {
                         $q->whereNull('reserved_until')
                           ->orWhere('reserved_until', '<=', now());
                     })
                     ->whereNull('package_id');
    }

    /**
     * Check if the stash is currently reserved
     */
    public function isReserved(): bool
    {
        return $this->reserved_until && $this->reserved_until->isFuture();
    }

    /**
     * Check if the stash is available for a new package
     */
    public function isAvailable(): bool
    {
        return !$this->is_package_in &&
            (!$this->reserved_until || $this->reserved_until->isPast()) &&
            !$this->package_id;
    }

    /**
     * Reserve the stash for a package
     */
    public function reserveFor(Package $package): void
    {
        $this->update([
            'package_id' => $package->id,
            'reserved_until' => now()->addHours(24),
            'is_package_in' => false,
        ]);
    }

    /**
     * Mark the stash as having a delivered package inside
     */
    public function markPackageDelivered(): void
    {
        $this->update(['is_package_in' => true]);
    }

    /**
     * Clear the stash reservation and contents
     */
    public function clearReservation(): void
    {
        $this->update([
            'package_id' => null,
            'reserved_until' => null,
            'is_package_in' => false,
        ]);
    }
}
