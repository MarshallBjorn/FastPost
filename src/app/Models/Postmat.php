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
        'post-code',
        'latitude',
        'longtitude',
        'status'
    ];

    public function stashes(){
        return $this->hasMany(Stash::class);
    }
}
