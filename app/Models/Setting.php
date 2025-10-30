<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'name',   // one user in the conversation
        'value',   // the other user
    ];
    public $timestamps = false;
}
