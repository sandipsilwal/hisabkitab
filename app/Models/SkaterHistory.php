<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkaterHistory extends Model
{
    protected $fillable = [
        'start_time',
        'end_time',
        'session_minutes',
        'skater_id',
        'amount',
        'no_of_skaters',
        'payment_status',
    ];

    public function skater()
    {
        return $this->belongsTo(Skater::class, 'skater_id');
    }
}
