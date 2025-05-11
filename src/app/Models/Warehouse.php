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

    public function staff() {
        return $this->hasMany(Staff::class);
    }

    public function actualization()
    {
        return $this->hasMany(Actualization::class);
    }
}
