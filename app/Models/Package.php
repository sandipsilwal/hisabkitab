<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = ['game_type_id', 'time_per_day', 'no_of_days', 'amount'];

    public function gameType()
    {
        return $this->belongsTo(GameType::class);
    }

    public function playerPackages()
    {
        return $this->hasMany(PlayerPackage::class);
    }
}
