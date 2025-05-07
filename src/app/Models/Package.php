<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $table = 'packages';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'destination_postmat_id',
        'receiver_email',
        'receiver_phone',
        'status',
        'sent_at',
        'delivered_date',
        'collected_date'
    ];

    public function sender() {
       return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver() {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function postmat() {
        return $this->belongsTo(Postmat::class);
    }

    public function stash() {
        return $this->hasOne(Stash::class);
    }
}
