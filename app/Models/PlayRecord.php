<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayRecord extends Model
{
    protected $fillable = [
        'name',
        'player_type',
        'player_package_id',
        'token_id',
        'default_time',
        'start_time',
        'end_time',
        'actual_time',
        'no_of_players',
        'amount',
        'payment_type_id'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function playerPackage()
    {
        return $this->belongsTo(PlayerPackage::class);
    }

    public function token()
    {
        return $this->belongsTo(Token::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }
}
