<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SkaterHistory extends Model
{
    protected $fillable = [
        'start_time',
        'end_time',
        'session_minutes',
        'skater_id',
        'amount',
        'no_of_skaters',
        'payment_status',
        'status'
    ];

    public function skater()
    {
        return $this->belongsTo(Skater::class, 'skater_id');
    }

    // public function getRemainingSecondsAttribute()
    // {
    //     if ($this->status !== 'playing' || !$this->start_time) {
    //         return $this->session_minutes * 60;
    //     }
    //     $elapsed = Carbon::parse($this->start_time)->diffInSeconds(now(), false);
    //     return max(0, ($this->session_minutes * 60) - $elapsed);
    // }

    // public function getOvertimeSecondsAttribute()
    // {
    //     if ($this->status !== 'over_time' || !$this->start_time) {
    //         return 0;
    //     }

    //     $elapsed = Carbon::parse($this->start_time)->diffInSeconds(now(), false);
    //     return max(0, $elapsed - ($this->session_minutes * 60));
    // }
    public function getRemainingSecondsAttribute()
    {
        if ($this->status !== 'playing' || !$this->start_time) {
            return $this->session_minutes * 60;
        }
        $startTime = Carbon::parse($this->start_time);
        $sessionEndTime = $startTime->copy()->addMinutes($this->session_minutes);
        $elapsed = $startTime->diffInSeconds(now(), false);
        return max(0, ($this->session_minutes * 60) - $elapsed);
    }

    public function getOvertimeSecondsAttribute()
    {
        if ($this->status !== 'over_time' || !$this->start_time) {
            return 0;
        }
        $startTime = Carbon::parse($this->start_time);
        $sessionEndTime = $startTime->copy()->addMinutes($this->session_minutes);
        if (now()->lessThan($sessionEndTime)) {
            return 0; // Not yet in overtime
        }
        $elapsed = $startTime->diffInSeconds(now(), false);
        return max(0, $elapsed - ($this->session_minutes * 60));
    }

}
