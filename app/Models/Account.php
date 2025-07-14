<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_default_cash_account',
        'is_default_online_account',
        'balance',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'to_account_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'from_account_id');
    }

    public function extraIncomes()
    {
        return $this->hasMany(ExtraIncome::class, 'to_account_id');
    }

    public function fromAccountTransactions()
    {
        return $this->hasMany(AccountTransaction::class, 'from_account_id');
    }

    public function toAccountTransactions()
    {
        return $this->hasMany(AccountTransaction::class, 'to_account_id');
    }
}