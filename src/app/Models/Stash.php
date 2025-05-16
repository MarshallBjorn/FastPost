<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Stash extends Model
{
    /** @use HasFactory<\Database\Factories\StashFactory> */
    use HasFactory, Notifiable;

    protected $table = 'stashes';

    protected $fillable = [
        'postmat_id',
        'size',
        'package_id'
    ];

    public function postmat() {
        return $this->belongsTo(Postmat::class);
    }

    public function package() {
        return $this->belongsTo(Package::class, 'package_id');
    }
}
