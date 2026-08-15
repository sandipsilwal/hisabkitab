<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayRecordExtra extends Model
{
    protected $fillable = [
        'play_record_id',
        'extra_id',
        'quantity',
        'total_amount',
        'payment_type_id',
        'status'
    ];

    public function playRecord()
    {
        return $this->belongsTo(PlayRecord::class);
    }

    public function extra()
    {
        return $this->belongsTo(Extra::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }
}
