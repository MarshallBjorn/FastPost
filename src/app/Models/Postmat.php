<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postmat extends Model
{
    use HasFactory;

    protected $table = 'postmats';

    protected $fillable = [
        'name',
        'city',
        'post_code',
        'latitude',
        'longitude',
        'status',
        'warehouse_id'
    ];

    public function stashes(){
        return $this->hasMany(Stash::class);
    }

    public function warehouse() {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}
