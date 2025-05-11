<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verify extends Model
{
    protected $table = "verifies";
    protected $primaryKey = "user_id";
    protected $incrementing = false;

    protected $fillable = [
        "user_id",
        "code",
        "expire_date",
    ];  

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
