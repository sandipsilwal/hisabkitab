<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PlayRecord extends Model
{
    protected $fillable = [
        'name',
        'player_type',
        'player_package_id',
        'token_id',
        'game_type_id',
        'default_time',
        'start_time',
        'end_time',
        'actual_time',
        'no_of_players',
        'amount',
        'payment_type_id'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function playerPackage()
    {
        return $this->belongsTo(PlayerPackage::class);
    }

    public function token()
    {
        return $this->belongsTo(Token::class);
    }

    public function gameType()
    {
        return $this->belongsTo(GameType::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function playRecordExtras()
    {
        return $this->hasMany(PlayRecordExtra::class);
    }

    /**
     * Calculate duration in minutes between start and end times (minimum 1 minute).
     */
    public static function calculateDurationMinutes(Carbon $startTime, Carbon $endTime): int
    {
        $diffInSeconds = max(0, $startTime->diffInSeconds($endTime, false));

        return max(1, (int) round($diffInSeconds / 60));
    }

    /**
     * Resolve actual minutes played from timestamps when available, falling back to stored actual_time.
     */
    public function resolvedActualMinutes(): ?int
    {
        if ($this->start_time && $this->end_time) {
            return self::calculateDurationMinutes($this->start_time, $this->end_time);
        }

        return $this->actual_time !== null ? (int) $this->actual_time : null;
    }
}
