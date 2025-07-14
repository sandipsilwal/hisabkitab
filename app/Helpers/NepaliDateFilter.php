<?php

namespace App\Helpers;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use DateTime;

class NepaliDateFilter
{
    public static function getBsRange(string $filter): array
    {
        $todayAD = now(); // Or new DateTime()
        $todayBS = LaravelNepaliDate::from($todayAD)->toNepaliDate();

        switch ($filter) {
            case 'this_bs_month':
                $startBS = LaravelNepaliDate::fromBsDate("{$todayBS->year}-{$todayBS->month}-1");
                $endBS = LaravelNepaliDate::fromBsDate("{$todayBS->year}-{$todayBS->month}-32"); // will auto-fix to last day
                break;

            case 'last_bs_month':
                $prevMonth = $todayBS->month - 1;
                $prevYear = $todayBS->year;
                if ($prevMonth < 1) {
                    $prevMonth = 12;
                    $prevYear--;
                }
                $startBS = LaravelNepaliDate::fromBsDate("{$prevYear}-{$prevMonth}-1");
                $endBS = LaravelNepaliDate::fromBsDate("{$prevYear}-{$prevMonth}-32");
                break;

            case 'this_bs_week':
                $startDay = $todayBS->day - $todayBS->dayOfWeek;
                $endDay = $todayBS->day + (6 - $todayBS->dayOfWeek);
                $startBS = LaravelNepaliDate::fromBsDate("{$todayBS->year}-{$todayBS->month}-{$startDay}");
                $endBS = LaravelNepaliDate::fromBsDate("{$todayBS->year}-{$todayBS->month}-{$endDay}");
                break;

            case 'last_bs_week':
                $startDay = $todayBS->day - $todayBS->dayOfWeek - 7;
                $endDay = $todayBS->day - $todayBS->dayOfWeek - 1;
                $startBS = LaravelNepaliDate::fromBsDate("{$todayBS->year}-{$todayBS->month}-{$startDay}");
                $endBS = LaravelNepaliDate::fromBsDate("{$todayBS->year}-{$todayBS->month}-{$endDay}");
                break;

            default:
                return [null, null, null];
        }

        return [
            $startBS->toEnglishDate(), // AD
            $endBS->toEnglishDate(),   // AD
            $startBS->toDateString() . ' - ' . $endBS->toDateString() // BS range string
        ];
    }
}
