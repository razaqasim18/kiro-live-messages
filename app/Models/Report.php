<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;
    protected $fillable = [
        'reported_id',   // one user in the conversation
        'reported_by_id',   // the other user
        'message',
        'is_processed',
        'processed_at',
    ];

    public function reportby()
    {
        return $this->belongsTo(User::class, 'reported_by_id');
    }
    public function reportof()
    {
        return $this->belongsTo(User::class, 'reported_id');
    }
}
