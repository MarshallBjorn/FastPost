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
        'message',
        'last_courier_id',
        'created_at'
    ];
    
    public function package() {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function courier() {
        return $this->belongsTo(Staff::class, 'last_courier_id');
    }

    public function lastWareHouse()
    {
        return $this->belongsTo(Warehouse::class,'last_warehouse_id');
    }
}
