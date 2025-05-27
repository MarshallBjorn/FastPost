<?php

namespace App\Models;

use App\Enums\PackageSize;
use App\Enums\PackageStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $table = 'packages';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'start_postmat_id',
        'destination_postmat_id',
        'receiver_email',
        'receiver_phone',
        'status',
        'sent_at',
        'delivered_date',
        'collected_date',
        'size',
        'weight',
        'unlock_code',
        'route_path'
    ];

    protected function casts(): array
    {
        return [
            'size' => PackageSize::class,
            'status' => PackageStatus::class,
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($package) {
            if (empty($package->sent_at)) {
                $package->sent_at = now();
            }
        });
    }

    public function sender() {
       return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver() {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function postmat() {
        return $this->belongsTo(Postmat::class);
    }

    public function destinationPostmat()
    {
        return $this->belongsTo(Postmat::class, 'destination_postmat_id');
    }

    public function startPostmat()
    {
        return $this->belongsTo(Postmat::class, 'start_postmat_id');
    }

    public function stash() {
        return $this->hasOne(Stash::class);
    }

    public function actualizations()
    {
        return $this->hasMany(Actualization::class)->orderBy('created_at', 'asc');
    }

    public function latestActualization()
    {
        return $this->hasOne(Actualization::class)->latestOfMany();
    }

    public function getDeliveredDate(): string
    {
        return $this->delivered_date ? $this->delivered_date->format('d M Y, H:i') : 'N/A';
    }

    public function getCollectedDate(): string
    {
        return $this->collected_date ? $this->collected_date->format('d M Y, H:i') : 'N/A';
    }

    public function getUnlockCode(): string
    {
        return $this->unlock_code ?? 'N/A';
    }
}
