<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerPackage extends Model
{
    protected $fillable = ['player_id', 'package_id', 'total_amount', 'package_status', 'remarks'];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function payments()
    {
        return $this->hasMany(PlayerPackagePaymentHistory::class);
    }

    public function playRecords()
    {
        return $this->hasMany(PlayRecord::class);
    }

    /**
     * Format minutes as Xh Ym (e.g. 2h 15m or 0h 0m).
     */
    public static function formatHoursMinutes(int $minutes): string
    {
        $hours = intdiv(max(0, $minutes), 60);
        $mins = max(0, $minutes) % 60;

        return $hours . 'h ' . $mins . 'm';
    }

    /**
     * Build package summary metrics and play record history for modal display.
     */
    public function toHistoryArray(): array
    {
        $package = $this->package;
        $timePerDay = (int) ($package?->time_per_day ?? 0);
        $noOfDays = (int) ($package?->no_of_days ?? 0);
        $allowedMinutes = $timePerDay * $noOfDays;

        $records = $this->playRecords
            ->sortByDesc(fn ($rec) => $rec->start_time?->timestamp ?? $rec->created_at?->timestamp ?? 0)
            ->values();

        $totalPlayedMinutes = (int) $records
            ->whereNotNull('end_time')
            ->sum(fn ($rec) => $rec->resolvedActualMinutes() ?? 0);
        $remainingMinutes = max(0, $allowedMinutes - $totalPlayedMinutes);

        $totalDaysPlayed = $records
            ->filter(fn ($rec) => $rec->start_time !== null || $rec->end_time !== null)
            ->map(fn ($rec) => ($rec->start_time ?? $rec->end_time)->format('Y-m-d'))
            ->unique()
            ->count();

        $remainingDays = max(0, $noOfDays - $totalDaysPlayed);

        return [
            'id' => $this->id,
            'game_name' => $package?->gameType?->game_name ?? '—',
            'time_per_day' => $timePerDay,
            'no_of_days' => $noOfDays,
            'total_amount' => $this->total_amount,
            'package_status' => $this->package_status,
            'purchased_date' => $this->created_at?->format('Y-m-d') ?? '—',
            'remarks' => $this->remarks,
            'allowed_minutes' => $allowedMinutes,
            'allowed_hours_formatted' => self::formatHoursMinutes($allowedMinutes),
            'total_played_minutes' => $totalPlayedMinutes,
            'total_hours_played_formatted' => self::formatHoursMinutes($totalPlayedMinutes),
            'remaining_minutes' => $remainingMinutes,
            'remaining_hours_formatted' => self::formatHoursMinutes($remainingMinutes),
            'total_days_played' => $totalDaysPlayed,
            'remaining_days' => $remainingDays,
            'total_sessions' => $records->count(),
            'play_records' => $records->map(function ($rec) use ($package) {
                $actualMinutes = $rec->resolvedActualMinutes();
                $status = $rec->end_time ? 'completed' : ($rec->start_time ? 'playing' : 'pending');

                return [
                    'id' => $rec->id,
                    'date' => ($rec->start_time ?? $rec->created_at)?->format('Y-m-d') ?? '—',
                    'token' => $rec->token?->name ?? '—',
                    'game_type' => $rec->gameType?->game_name ?? $package?->gameType?->game_name ?? '—',
                    'start_time' => $rec->start_time?->format('h:i A') ?? '—',
                    'end_time' => $rec->end_time?->format('h:i A') ?? '—',
                    'actual_time' => $actualMinutes,
                    'actual_time_formatted' => $actualMinutes !== null
                        ? ($actualMinutes >= 60 ? self::formatHoursMinutes($actualMinutes) : $actualMinutes . 'm')
                        : '—',
                    'no_of_players' => (int) $rec->no_of_players,
                    'status' => $status,
                ];
            })->values(),
        ];
    }
}
