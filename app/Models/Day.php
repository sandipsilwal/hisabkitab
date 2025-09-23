<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_bs',
        'date_ad',
        'total',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'day_id');
    }
}