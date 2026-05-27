<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $fillable = ['game_type_id', 'default_time_id', 'rate'];

    public function gameType()
    {
        return $this->belongsTo(GameType::class);
    }

    public function defaultTime()
    {
        return $this->belongsTo(DefaultTime::class);
    }
}
