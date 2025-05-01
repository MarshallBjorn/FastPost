<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $table = 'stashes';

    protected $fillable = [
        'sender_id',
        'goal_postmat_ud',
        'reciever_email',
        'reciever_phone',
        'reciever_id',
        'status',
        'register_date',
        'delivered_date',
        'recieval_date'
    ];

    // public function sender() {
    //     return $this->belongsTo(User::class, 'sender_id');
    // }

    // public function reciever() {
    //     return $this->belongsTo(User::class, 'reciever_id');
    // }

    public function postmat() {
        return $this->belongsTo(Postmat::class);
    }

    public function stash() {
        return $this->hasOne(Stash::class);
    }
}
