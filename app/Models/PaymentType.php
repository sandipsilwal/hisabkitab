<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    protected $fillable = ['name', 'is_default', 'is_alert'];

    protected $casts = [
        'is_default' => 'boolean',
        'is_alert' => 'boolean',
    ];

    public function playRecords()
    {
        return $this->hasMany(PlayRecord::class);
    }
}
