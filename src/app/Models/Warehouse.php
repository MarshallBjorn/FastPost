<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Warehouse extends Model
{
    use HasFactory;

    protected $table = 'warehouses';

    protected $fillable = [
        'city',
        'post_code',
        'latitude',
        'longitude',
        'status'
    ];

    public function stashes() {
        return $this->hasMany(Stash::class);
    }

    public function postmats() {
        return $this->hasMany(Postmat::class);
    }

    public function staff() {
        return $this->hasMany(Staff::class);
    }

    public function actualization()
    {
        return $this->hasMany(Actualization::class);
    }

    public function connectionsFrom()
    {
        return $this->hasMany(WarehouseConnection::class, 'from_warehouse_id');
    }

    public function connections()
    {
        return $this->hasMany(WarehouseConnection::class, 'from_warehouse_id')
            ->orWhere('to_warehouse_id', $this->id);
    }

    public function outgoingPackages() {
        return $this->hasMany(Actualization::class, 'current_warehouse_id')
                    ->where('status', 'in_warehouse');
    }

    public function incomingPackages() {
        return $this->hasMany(Actualization::class, 'next_warehouse_id')
                    ->where('status', 'in_transit');
    }
}
