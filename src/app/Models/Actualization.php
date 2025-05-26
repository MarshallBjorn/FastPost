<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actualization extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'actualizations';

    protected $fillable = [
        'package_id',
        'route_remaining',
        'current_warehouse_id',
        'next_warehouse_id',
        'message',
        'last_courier_id',
        'created_at'
    ];

    public function package() {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function courier() {
        return $this->belongsTo(User::class, 'last_courier_id');
    }

    public function currentWarehouse() {
        return $this->belongsTo(Warehouse::class, 'current_warehouse_id');
    }

    public function nextWarehouse() {
        return $this->belongsTo(Warehouse::class, 'next_warehouse_id');
    }
}
