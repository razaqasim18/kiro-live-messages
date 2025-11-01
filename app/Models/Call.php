<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    public $fillable = [
        "caller_id",
        "calle_id",
        "channel"
    ];
}
