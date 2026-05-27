<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerPackage extends Model
{
    protected $fillable = ['player_id', 'package_id', 'total_amount', 'package_status', 'remarks'];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function payments()
    {
        return $this->hasMany(PlayerPackagePaymentHistory::class);
    }

    public function playRecords()
    {
        return $this->hasMany(PlayRecord::class);
    }
}
