<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Extra extends Model
{
    protected $fillable = ['title', 'amount', 'is_default', 'display_order'];
}
