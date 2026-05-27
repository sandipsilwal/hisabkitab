<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerPackagePaymentHistory extends Model
{
    protected $table = 'player_package_payment_histories';

    protected $fillable = ['player_package_id', 'date', 'amount', 'remarks'];

    protected $casts = [
        'date' => 'date',
    ];

    public function playerPackage()
    {
        return $this->belongsTo(PlayerPackage::class);
    }
}
