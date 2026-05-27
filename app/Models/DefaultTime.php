<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefaultTime extends Model
{
    protected $fillable = ['label', 'minutes', 'display_order'];

    public function rates()
    {
        return $this->hasMany(Rate::class);
    }
}
