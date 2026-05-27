<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $fillable = ['name', 'game_type_id', 'display_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function gameType()
    {
        return $this->belongsTo(GameType::class);
    }

    public function playRecords()
    {
        return $this->hasMany(PlayRecord::class);
    }
}
