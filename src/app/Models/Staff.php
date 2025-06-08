<?php

namespace App\Models;

use App\Enums\PackageStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Staff extends Model
{
    protected $table = "staff";
    protected $primaryKey = "user_id";
    public $incrementing = false;

    protected $fillable = [
        "user_id",
        "staff_type",
        "warehouse_id",
        "hire_date",
        "termination_date",
    ];

    protected $casts = [
        'hire_date' => 'datetime',
        'termination_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function currentPackages()
    {
        return Package::whereHas('latestActualization', function ($query) {
            $query->where('last_courier_id', $this->user_id);
        })
            ->where('status', '!=', PackageStatus::IN_POSTMAT) // exclude delivered packages
            ->get();
    }
}
