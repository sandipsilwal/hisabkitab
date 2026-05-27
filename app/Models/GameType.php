<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameType extends Model
{
    protected $fillable = ['game_name'];

    public function tokens()
    {
        return $this->hasMany(Token::class);
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function rates()
    {
        return $this->hasMany(Rate::class);
    }
}
