<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseConnection extends Model
{
    protected $fillable = ['from_warehouse_id', 'to_warehouse_id', 'distance_km'];

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }
}
