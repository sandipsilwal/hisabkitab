<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skater extends Model
{
    protected $fillable = [
        'name',
        'membership_no',
        'phone',
        'age',
        'email',
        'gender',
        'shoes_size',
    ];

    public function skater_histories()
    {
        return $this->hasMany(SkaterHistory::class, 'skater_id');
    }
}
