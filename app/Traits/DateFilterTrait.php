<?php

namespace App\Traits;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use DateTime;
use Illuminate\Http\Request;

trait DateFilterTrait
{
    /**
     * Apply Nepali date filters to a query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function applyDateFilters($query, Request $request)
    {
        $filter_date_range = '';
        
        // Handle custom date range filter
        if ($request->filled('date_range_filter') && !$request->filled('date_filter')) {
            $filter_date_range = $request->date_range_filter;
            [$startDateBS, $endDateBS] = explode(' - ', $filter_date_range);
            $startDateAD = new DateTime(LaravelNepaliDate::from($startDateBS)->toEnglishDate());
            $endDateAD = new DateTime(LaravelNepaliDate::from($endDateBS)->toEnglishDate());
            $query->whereBetween('date_ad', [$startDateAD->format('Y-m-d'), $endDateAD->format('Y-m-d')]);
        }

        // Handle predefined date filters (This Month, Last Month, This Week, Last Week)
        if ($request->filled('date_filter')) {
            $today = LaravelNepaliDate::from(now())->toNepaliDate();
            [$currentYear, $currentMonth, $currentDay] = explode('-', $today);

            switch ($request->date_filter) {
                case 'this_month':
                    $startDateBS = "$currentYear-$currentMonth-01";
                    $endDateBS = "$currentYear-$currentMonth-32";
                    $filter_date_range = "$startDateBS - $endDateBS";
                    break;
                case 'last_month':
                    $lastYear = $currentYear;
                    $lastMonth = $currentMonth-1;
                    if($lastMonth<1){
                        $lastMonth = 12;
                        $lastYear -= 1;
                    }
                    $startDateBS = "$lastYear-$lastMonth-01";
                    $endDateBS = "$lastYear-$lastMonth-32";
                    $filter_date_range = "$startDateBS - $endDateBS";
                    break;
                case 'this_week':
                    $startDateBS = LaravelNepaliDate::from(now()->startOfWeek())->toNepaliDate();
                    $endDateBS = LaravelNepaliDate::from(now()->endOfWeek())->toNepaliDate();
                    $filter_date_range = "$startDateBS - $endDateBS";
                    break;
                case 'last_week':
                    $lastWeek = now()->subWeek();
                    $startDateBS = LaravelNepaliDate::from($lastWeek->startOfWeek())->toNepaliDate();
                    $endDateBS = LaravelNepaliDate::from($lastWeek->endOfWeek())->toNepaliDate();
                    $filter_date_range = "$startDateBS - $endDateBS";
                    break;
            }

            // Convert BS dates to AD for query
            [$startDateBS, $endDateBS] = explode(' - ', $filter_date_range);
            $startDateAD = new DateTime(LaravelNepaliDate::from($startDateBS)->toEnglishDate());
            $endDateAD = new DateTime(LaravelNepaliDate::from($endDateBS)->toEnglishDate());
            $query->whereBetween('date_ad', [$startDateAD->format('Y-m-d'), $endDateAD->format('Y-m-d')]);
        }

        return [$query, $filter_date_range];
    }
}