<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraIncome extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_bs',
        'date_ad',
        'title',
        'amount',
        'to_account_id',
        'remarks',
        'is_posted',
    ];

    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }
}