<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_ad',
        'date_bs',
        'title',
        'amount',
        'from_account_id',
        'remarks',
        'is_posted',
    ];

    public function fromAccount()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }
}