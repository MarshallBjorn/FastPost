<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stash extends Model
{
    use HasFactory;

    protected $table = 'stashes';

    protected $fillable = [
        'postmat_id',
        'size',
        'package_id'
    ];

    public function postmat() {
        return $this->belongsTo(Postmat::class);
    }

    // public function stash() {
    //     return $this->belongsTo();
    // }
}
