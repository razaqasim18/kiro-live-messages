<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCoinsTransection extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'coins',
        'reference_id',
        'reference_by',
    ];
}
