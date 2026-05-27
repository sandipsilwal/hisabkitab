<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = ['name', 'contact', 'address'];

    public function playerPackages()
    {
        return $this->hasMany(PlayerPackage::class);
    }
}
