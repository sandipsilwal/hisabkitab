<?php

namespace App\Traits;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use DateTime;
use Illuminate\Http\Request;

trait DateFilterTrait
{
    /**
     * Resolve Nepali date filter range to AD dates.
     *
     * @param \Illuminate\Http\Request $request
     * @return array [$startDateADString|null, $endDateADString|null, $filter_date_range]
     */
    protected function resolveNepaliDateRange(Request $request)
    {
        $filter_date_range = '';

        // Handle custom date range filter
        if ($request->filled('date_range_filter') && !$request->filled('date_filter')) {
            $filter_date_range = $request->date_range_filter;
        }

        // Handle predefined date filters (Today, This Month, Last Month, This Week, Last Week)
        if ($request->filled('date_filter')) {
            $today = LaravelNepaliDate::from(now())->toNepaliDate();
            [$currentYear, $currentMonth, $currentDay] = explode('-', $today);

            switch ($request->date_filter) {
                case 'today':
                    $filter_date_range = "$today - $today";
                    break;
                case 'this_month':
                    $startDateBS = "$currentYear-$currentMonth-01";
                    $endDateBS = "$currentYear-$currentMonth-32";
                    $filter_date_range = "$startDateBS - $endDateBS";
                    break;
                case 'last_month':
                    $lastYear = $currentYear;
                    $lastMonth = $currentMonth - 1;
                    if ($lastMonth < 1) {
                        $lastMonth = 12;
                        $lastYear -= 1;
                    }
                    $lastMonth = str_pad($lastMonth, 2, '0', STR_PAD_LEFT);
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
        }

        if (!empty($filter_date_range)) {
            [$startDateBS, $endDateBS] = explode(' - ', $filter_date_range);
            $startDateAD = new DateTime(LaravelNepaliDate::from(trim($startDateBS))->toEnglishDate());
            $endDateAD = new DateTime(LaravelNepaliDate::from(trim($endDateBS))->toEnglishDate());
            return [$startDateAD->format('Y-m-d'), $endDateAD->format('Y-m-d'), $filter_date_range];
        }

        return [null, null, $filter_date_range];
    }

    /**
     * Apply Nepali date filters to a query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function applyDateFilters($query, Request $request)
    {
        [$startDateAD, $endDateAD, $filter_date_range] = $this->resolveNepaliDateRange($request);

        if ($startDateAD && $endDateAD) {
            $query->whereBetween('date_ad', [$startDateAD, $endDateAD]);
        }

        return [$query, $filter_date_range];
    }
}