@extends('layouts.app')

@section('content')

<style>
    .report-summary-card {
        border-radius: 14px;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.07);
        transition: transform 0.18s ease, box-shadow 0.18s ease;
    }
    .report-summary-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 28px rgba(0,0,0,0.12);
    }
    .report-summary-card .card-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .report-summary-card .stat-value {
        font-size: 1.6rem;
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -0.5px;
    }
    .report-summary-card .stat-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #64748b;
        font-weight: 600;
    }

    .filter-bar {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid #e2e8f0;
    }

    .report-table-wrap {
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .report-table-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .report-table-title {
        color: #0f172a;
    }
    .report-table thead th {
        background: #0f172a;
        color: #94a3b8;
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.9px;
        font-weight: 700;
        padding: 0.75rem 0.85rem;
        border: none;
        white-space: nowrap;
    }
    .report-table tbody tr {
        font-size: 0.84rem;
        transition: background 0.12s;
    }
    .report-table tbody td {
        padding: 0.55rem 0.85rem;
        vertical-align: middle;
        border-color: #f1f5f9;
    }
    .report-table tbody tr.pkg-row {
        background-color: #f0fdf4;
    }
    .report-table tbody tr.pkg-row:hover {
        background-color: #dcfce7;
    }
    .report-table tbody tr.normal-row:hover {
        background-color: #f8fafc;
    }

    .badge-type {
        font-size: 0.65rem;
        padding: 0.25em 0.65em;
        border-radius: 50px;
        font-weight: 700;
        letter-spacing: 0.4px;
    }

    .payment-pill {
        font-size: 0.7rem;
        padding: 0.2em 0.6em;
        border-radius: 50px;
        font-weight: 600;
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }

    .breakdown-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 50px;
        padding: 0.3rem 0.85rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #334155;
    }
    .breakdown-pill .dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .page-header-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    @media print {
        .no-print { display: none !important; }
        .report-table-wrap { box-shadow: none; border: 1px solid #ccc; }
    }

    body.dark-mode .filter-bar,
    body.dark-mode .report-table-wrap {
        background: #121212 !important;
        border-color: rgba(255,255,255,0.08) !important;
    }
    body.dark-mode .report-table-header {
        background: #1a1a1a !important;
        border-color: rgba(255,255,255,0.08) !important;
    }
    body.dark-mode .report-table-title {
        color: #f1f5f9 !important;
    }
    body.dark-mode .report-summary-card {
        background: #1a1a1a !important;
    }
    body.dark-mode .report-table thead th {
        background: #0a0a0a !important;
    }
    body.dark-mode .report-table tbody td {
        border-color: rgba(255,255,255,0.05) !important;
        color: #e2e8f0 !important;
    }
    body.dark-mode .report-table tbody tr.pkg-row {
        background-color: rgba(34,197,94,0.07) !important;
    }
    body.dark-mode .breakdown-pill {
        background: #1e293b !important;
        border-color: rgba(255,255,255,0.1) !important;
        color: #cbd5e1 !important;
    }

    /* Cash Card Colors */
    .payment-card-cash {
        background: #f0fdf4;
        border: 1.5px solid #86efac !important;
    }
    .payment-card-cash .stat-value,
    .payment-card-cash .stat-label {
        color: #15803d !important;
    }
    .payment-card-cash .card-icon {
        background: #dcfce7 !important;
    }
    .payment-card-cash .card-footer-text {
        color: #166534 !important;
    }
    body.dark-mode .payment-card-cash {
        background: rgba(34, 197, 94, 0.08) !important;
        border-color: rgba(34, 197, 94, 0.3) !important;
    }
    body.dark-mode .payment-card-cash .stat-value,
    body.dark-mode .payment-card-cash .stat-label,
    body.dark-mode .payment-card-cash .card-footer-text {
        color: #4ade80 !important;
    }

    /* Card Card Colors */
    .payment-card-card {
        background: #eff6ff;
        border: 1.5px solid #93c5fd !important;
    }
    .payment-card-card .stat-value,
    .payment-card-card .stat-label {
        color: #1d4ed8 !important;
    }
    .payment-card-card .card-icon {
        background: #dbeafe !important;
    }
    .payment-card-card .card-footer-text {
        color: #1e40af !important;
    }
    body.dark-mode .payment-card-card {
        background: rgba(59, 130, 246, 0.08) !important;
        border-color: rgba(59, 130, 246, 0.3) !important;
    }
    body.dark-mode .payment-card-card .stat-value,
    body.dark-mode .payment-card-card .stat-label,
    body.dark-mode .payment-card-card .card-footer-text {
        color: #60a5fa !important;
    }

    /* Online Card Colors */
    .payment-card-online {
        background: #faf5ff;
        border: 1.5px solid #c4b5fd !important;
    }
    .payment-card-online .stat-value,
    .payment-card-online .stat-label {
        color: #7c3aed !important;
    }
    .payment-card-online .card-icon {
        background: #f3e8ff !important;
    }
    .payment-card-online .card-footer-text {
        color: #6b21a8 !important;
    }
    body.dark-mode .payment-card-online {
        background: rgba(168, 85, 247, 0.08) !important;
        border-color: rgba(168, 85, 247, 0.3) !important;
    }
    body.dark-mode .payment-card-online .stat-value,
    body.dark-mode .payment-card-online .stat-label,
    body.dark-mode .payment-card-online .card-footer-text {
        color: #c084fc !important;
    }

    /* Default/Other Card Colors */
    .payment-card-default {
        background: #f8fafc;
        border: 1.5px solid #cbd5e1 !important;
    }
    .payment-card-default .stat-value,
    .payment-card-default .stat-label {
        color: #475569 !important;
    }
    .payment-card-default .card-icon {
        background: #e2e8f0 !important;
    }
    .payment-card-default .card-footer-text {
        color: #475569 !important;
    }
    body.dark-mode .payment-card-default {
        background: rgba(148, 163, 184, 0.08) !important;
        border-color: rgba(148, 163, 184, 0.3) !important;
    }
    body.dark-mode .payment-card-default .stat-value,
    body.dark-mode .payment-card-default .stat-label,
    body.dark-mode .payment-card-default .card-footer-text {
        color: #94a3b8 !important;
    }

    /* Play Records Earnings Card Colors */
    .earnings-card-play {
        background: #eef2ff;
        border: 1.5px solid #c7d2fe !important;
    }
    .earnings-card-play .stat-value,
    .earnings-card-play .stat-label {
        color: #4338ca !important;
    }
    .earnings-card-play .card-icon {
        background: #e0e7ff !important;
    }
    .earnings-card-play .card-footer-text {
        color: #4f46e5 !important;
    }
    body.dark-mode .earnings-card-play {
        background: rgba(99, 102, 241, 0.08) !important;
        border-color: rgba(99, 102, 241, 0.3) !important;
    }
    body.dark-mode .earnings-card-play .stat-value,
    body.dark-mode .earnings-card-play .stat-label,
    body.dark-mode .earnings-card-play .card-footer-text {
        color: #818cf8 !important;
    }

    /* Extras Earnings Card Colors (Interactive) */
    .earnings-card-extras {
        background: #fffbeb;
        border: 1.5px solid #fde68a !important;
        cursor: pointer;
        position: relative;
    }
    .earnings-card-extras:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 10px 25px rgba(217, 119, 6, 0.2) !important;
        border-color: #f59e0b !important;
    }
    .earnings-card-extras .stat-value,
    .earnings-card-extras .stat-label {
        color: #b45309 !important;
    }
    .earnings-card-extras .card-icon {
        background: #fef3c7 !important;
    }
    .earnings-card-extras .card-footer-text {
        color: #d97706 !important;
    }
    body.dark-mode .earnings-card-extras {
        background: rgba(245, 158, 11, 0.08) !important;
        border-color: rgba(245, 158, 11, 0.3) !important;
    }
    body.dark-mode .earnings-card-extras:hover {
        box-shadow: 0 10px 25px rgba(245, 158, 11, 0.25) !important;
        border-color: rgba(245, 158, 11, 0.6) !important;
    }
    body.dark-mode .earnings-card-extras .stat-value,
    body.dark-mode .earnings-card-extras .stat-label,
    body.dark-mode .earnings-card-extras .card-footer-text {
        color: #fbbf24 !important;
    }

    /* Extras Modal Dark Mode */
    body.dark-mode #extrasDetailModal .modal-content {
        background: #1a1a1a !important;
        color: #f1f5f9 !important;
        border: 1px solid rgba(255,255,255,0.1) !important;
    }
    body.dark-mode #extrasDetailModal .modal-header,
    body.dark-mode #extrasDetailModal .modal-footer {
        background: #141414 !important;
        border-color: rgba(255,255,255,0.08) !important;
    }
    body.dark-mode #extrasDetailModal .table {
        color: #f1f5f9 !important;
    }
    body.dark-mode #extrasDetailModal .table thead th {
        background: #0f172a !important;
        color: #94a3b8 !important;
    }
    body.dark-mode #extrasDetailModal .table tbody td {
        border-color: rgba(255,255,255,0.05) !important;
        color: #e2e8f0 !important;
    }
    body.dark-mode #extrasDetailModal .summary-pill {
        background: #1e293b !important;
        border-color: rgba(255,255,255,0.1) !important;
    }
    body.dark-mode #extrasDetailModal .title-summary-card {
        background: #141414 !important;
        border-color: rgba(255,255,255,0.08) !important;
    }
    body.dark-mode #extrasDetailModal .title-summary-card span.text-dark {
        color: #f1f5f9 !important;
    }
    body.dark-mode #extrasDetailModal .title-summary-pill {
        background: #1e293b !important;
        border-color: rgba(255,255,255,0.1) !important;
    }
    body.dark-mode #extrasDetailModal .title-summary-pill .text-dark {
        color: #f1f5f9 !important;
    }
    body.dark-mode #extrasDetailModal tfoot,
    body.dark-mode #extrasDetailModal tfoot tr,
    body.dark-mode #extrasDetailModal tfoot td {
        background-color: #141414 !important;
        color: #f1f5f9 !important;
        border-color: rgba(255,255,255,0.08) !important;
    }
    body.dark-mode #extrasDetailModal tfoot .text-dark {
        color: #f1f5f9 !important;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 no-print">
    <div>
        <h4 class="fw-bold mb-0" style="color: #0f172a;">📊 {{ __('Play Records Report') }}</h4>
        <p class="text-muted mb-0" style="font-size: 0.82rem;">{{ __('Showing completed sessions for') }} {{ \Carbon\Carbon::parse($from)->format('M d, Y') }} &mdash; {{ \Carbon\Carbon::parse($to)->format('M d, Y') }} @if(!empty($filter_date_range)) <span class="badge bg-secondary ms-1">{{ $filter_date_range }}</span> @endif</p>
    </div>
    <div class="page-header-actions">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm fw-semibold" style="border-radius: 8px; font-size: 0.8rem;">
            🖨 {{ __('Print') }}
        </button>
    </div>
</div>

{{-- Date Filter Bar --}}
<div class="filter-bar p-3 mb-4 no-print">
    <form method="GET" action="{{ route('skatepark.reports') }}">
        <div class="row g-2 align-items-end">
            <div class="col-md-4 col-sm-6">
                <label for="date_range_filter" class="form-label fw-semibold mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Start Date') }}</label>
                <input type="text" value="{{ $filter_date_range }}" class="form-control form-control-sm nepali-datepicker date-range" id="date_range_filter" name="date_range_filter" autocomplete="off" placeholder="YYYY-MM-DD - YYYY-MM-DD" style="border-radius: 7px; font-size: 0.85rem;">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm fw-bold" style="border-radius: 7px; font-size: 0.82rem;">
                    🔍 {{ __('Filter') }}
                </button>
            </div>
            <div class="col-auto d-flex gap-1 flex-wrap">
                <button type="submit" name="date_filter" value="today" class="btn btn-secondary btn-sm fw-semibold" style="border-radius: 7px; font-size: 0.82rem;">{{ __('Today') }}</button>
                <button type="submit" name="date_filter" value="this_month" class="btn btn-secondary btn-sm fw-semibold" style="border-radius: 7px; font-size: 0.82rem;">{{ __('This Month') }}</button>
                <button type="submit" name="date_filter" value="last_month" class="btn btn-secondary btn-sm fw-semibold" style="border-radius: 7px; font-size: 0.82rem;">{{ __('Last Month') }}</button>
                <button type="submit" name="date_filter" value="this_week" class="btn btn-secondary btn-sm fw-semibold" style="border-radius: 7px; font-size: 0.82rem;">{{ __('This Week') }}</button>
                <button type="submit" name="date_filter" value="last_week" class="btn btn-secondary btn-sm fw-semibold" style="border-radius: 7px; font-size: 0.82rem;">{{ __('Last Week') }}</button>
            </div>
        </div>
    </form>
</div>

@php
    $ptColors = [
        'Cash'   => ['bg' => '#f0fdf4', 'border' => '#86efac', 'text' => '#15803d', 'dot' => '#22c55e', 'icon' => '💵', 'iconBg' => '#dcfce7'],
        'Card'   => ['bg' => '#eff6ff', 'border' => '#93c5fd', 'text' => '#1d4ed8', 'dot' => '#3b82f6', 'icon' => '💳', 'iconBg' => '#dbeafe'],
        'Online' => ['bg' => '#faf5ff', 'border' => '#c4b5fd', 'text' => '#7c3aed', 'dot' => '#a855f7', 'icon' => '📱', 'iconBg' => '#f3e8ff'],
    ];
    $defaultColor = ['bg' => '#f8fafc', 'border' => '#cbd5e1', 'text' => '#475569', 'dot' => '#94a3b8', 'icon' => '🪙', 'iconBg' => '#e2e8f0'];
    $ptCounts = $records->where('player_type', 'normal')->groupBy(fn($r) => optional($r->paymentType)->name ?? 'Unknown')->map(fn($g) => $g->count());
@endphp

{{-- Summary Cards --}}
<div class="row g-3 mb-4">

    {{-- Play Records Earnings Card --}}
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card report-summary-card h-100 p-3 earnings-card-play">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="card-icon" style="font-size: 1.3rem;">🛹</div>
                <span class="stat-label">{{ __('Play Records Earnings') }}</span>
            </div>
            <div class="stat-value" style="font-size: 1.35rem;">Rs. {{ number_format($playRecordsEarnings) }}</div>
            <div class="d-flex align-items-center gap-1 mt-auto pt-2 card-footer-text" style="font-size: 0.68rem;">
                <span>{{ $normalCount }} {{ __('sessions') }}</span>
                @php
                    $playPct = $totalRevenue > 0 ? round(($playRecordsEarnings / $totalRevenue) * 100) : 0;
                @endphp
                <span class="ms-auto fw-bold">{{ $playPct }}%</span>
            </div>
            <div class="mt-1" style="height: 4px; background: rgba(0,0,0,0.06); border-radius: 10px; overflow: hidden;">
                <div style="width: {{ $playPct }}%; height: 100%; background: #6366f1; border-radius: 10px;"></div>
            </div>
        </div>
    </div>

    {{-- Extras Earnings Card (Clickable to open modal) --}}
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card report-summary-card h-100 p-3 earnings-card-extras" 
             role="button" 
             data-bs-toggle="modal" 
             data-bs-target="#extrasDetailModal"
             title="{{ __('Click to view details') }}">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="card-icon" style="font-size: 1.3rem;">🍿</div>
                    <span class="stat-label">{{ __('Extras Earnings') }}</span>
                </div>
                <span class="badge bg-warning bg-opacity-25 text-warning-emphasis rounded-pill px-2 py-1" style="font-size: 0.62rem; letter-spacing: 0.3px;">
                    🔍 {{ __('Details') }}
                </span>
            </div>
            <div class="stat-value" style="font-size: 1.35rem;">Rs. {{ number_format($extrasTotal) }}</div>
            <div class="d-flex align-items-center justify-content-between mt-auto pt-2 card-footer-text" style="font-size: 0.68rem;">
                <span>{{ $paidExtras->count() }} {{ __('items') }}</span>
                <span class="fw-bold text-decoration-underline">{{ __('View Details') }} &rarr;</span>
            </div>
            @php
                $extrasPct = $totalRevenue > 0 ? round(($extrasTotal / $totalRevenue) * 100) : 0;
            @endphp
            <div class="mt-1" style="height: 4px; background: rgba(0,0,0,0.06); border-radius: 10px; overflow: hidden;">
                <div style="width: {{ $extrasPct }}%; height: 100%; background: #f59e0b; border-radius: 10px;"></div>
            </div>
        </div>
    </div>

    {{-- Payment Type Breakdown Cards --}}
    @if($paymentBreakdown->isNotEmpty())
        @foreach($paymentBreakdown as $ptName => $ptAmount)
            @php
                $className = 'payment-card-' . strtolower($ptName);
                if (!in_array(strtolower($ptName), ['cash', 'card', 'online'])) {
                    $className = 'payment-card-default';
                }
                $color   = $ptColors[$ptName] ?? $defaultColor;
                $pct     = $totalRevenue > 0 ? round(($ptAmount / $totalRevenue) * 100) : 0;
            @endphp
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card report-summary-card h-100 p-3 {{ $className }}">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="card-icon" style="font-size: 1.3rem;">{{ $color['icon'] }}</div>
                        <span class="stat-label">{{ $ptName }}</span>
                    </div>
                    <div class="stat-value" style="font-size: 1.35rem;">Rs. {{ number_format($ptAmount) }}</div>
                    <div class="d-flex align-items-center gap-1 mt-auto pt-2 card-footer-text" style="font-size: 0.68rem;">
                        <span>{{ __('Revenue') }}</span>
                        <span class="ms-auto fw-bold">{{ $pct }}%</span>
                    </div>
                    {{-- Mini progress bar --}}
                    <div class="mt-1" style="height: 4px; background: rgba(0,0,0,0.06); border-radius: 10px; overflow: hidden;">
                        <div style="width: {{ $pct }}%; height: 100%; background: {{ $color['dot'] }}; border-radius: 10px; transition: width 0.6s ease;"></div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    {{-- Package Payments Card --}}
    @if($packagePaymentsTotal > 0)
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card report-summary-card h-100 p-3" style="background: #fdf4ff; border: 1.5px solid #f0abfc !important;">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="card-icon" style="background: #fae8ff; font-size: 1.3rem;">🎁</div>
                <span class="stat-label" style="color: #a21caf;">{{ __('Packages') }}</span>
            </div>
            <div class="stat-value" style="color: #86198f; font-size: 1.35rem;">Rs. {{ number_format($packagePaymentsTotal) }}</div>
            <div class="d-flex align-items-center gap-1 mt-auto pt-2" style="font-size: 0.68rem; color: #a21caf;">
                <span>{{ $packagePayments->count() }} {{ __('payments') }}</span>
            </div>
            <div class="mt-1" style="height: 4px; background: rgba(0,0,0,0.06); border-radius: 10px;">
                <div style="width: 100%; height: 100%; background: #d946ef; border-radius: 10px;"></div>
            </div>
        </div>
    </div>
    @endif

    {{-- Grand Total card --}}
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card report-summary-card h-100 p-3" style="background: #0f172a; border: 1.5px solid #334155 !important;">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="card-icon" style="background: rgba(234, 179, 8, 0.15); font-size: 1.3rem;">💰</div>
                <span class="stat-label" style="color: #eab308;">{{ __('Total') }}</span>
            </div>
            <div class="stat-value" style="color: #fef3c7; font-size: 1.35rem;">Rs. {{ number_format($totalRevenue) }}</div>
            <div class="d-flex align-items-center gap-1 mt-auto pt-2" style="font-size: 0.68rem; color: #94a3b8;">
                <span>{{ __('Revenue') }}</span>
                <span class="ms-auto fw-bold" style="color: #eab308;">100%</span>
            </div>
            <div class="mt-1" style="height: 4px; background: #334155; border-radius: 10px;"></div>
        </div>
    </div>
</div>


{{-- Detail Table --}}
<div class="report-table-wrap">
    <div class="report-table-header d-flex justify-content-between align-items-center px-3 py-2 border-bottom no-print">
        <span class="fw-bold report-table-title" style="font-size: 0.85rem;">{{ __('Session Details') }}</span>
        <span class="text-muted" style="font-size: 0.75rem;">{{ $totalSessions }} {{ __('records') }}</span>
    </div>

    @if($records->isEmpty())
        <div class="text-center py-5 text-muted">
            <div style="font-size: 2.5rem;">📭</div>
            <div class="fw-semibold mt-2">{{ __('No completed sessions found for this period.') }}</div>
            <div style="font-size: 0.82rem;">{{ __('Try adjusting the date range.') }}</div>
        </div>
    @else
        <div class="table-responsive">
            <table class="table report-table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Name / Player') }}</th>
                        <th class="text-center">{{ __('Players') }}</th>
                        <th>{{ __('Start') }}</th>
                        <th>{{ __('End') }}</th>
                        <th class="text-center">{{ __('Actual Time') }}</th>
                        <th class="text-end">{{ __('Amount') }}</th>
                        <th class="text-end">{{ __('Extra Amount') }}</th>
                        <th class="text-end">{{ __('Total Amount') }}</th>
                        <th>{{ __('Type') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $i => $rec)
                        @php
                            $isPackage = $rec->player_type === 'package';
                            $displayName = $isPackage
                                ? optional(optional($rec->playerPackage)->player)->name ?? '—'
                                : ($rec->name ?: '—');
                            
                            $rowPaidExtras = $rec->playRecordExtras ? $rec->playRecordExtras->where('status', 'Paid') : collect();
                            $extraAmount = $rowPaidExtras->sum('total_amount');
                            $sessionAmount = $isPackage ? 0 : $rec->amount;
                            $rowTotal = $sessionAmount + $extraAmount;

                            $sessionPt = optional($rec->paymentType)->name;
                            $extraPtNames = $rowPaidExtras->map(fn($e) => optional($e->paymentType)->name)->filter()->unique()->implode(', ');
                        @endphp
                        <tr class="{{ $isPackage ? 'pkg-row' : 'normal-row' }}">
                            <td class="text-muted" style="font-size: 0.75rem;">{{ $i + 1 }}</td>
                            <td class="fw-semibold" style="max-width: 140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $displayName }}</td>
                            <td class="text-center fw-bold">{{ $rec->no_of_players }}</td>
                            <td style="font-size: 0.78rem; white-space: nowrap;">
                                {{ $rec->start_time ? $rec->start_time->format('h:i A') : '—' }}
                            </td>
                            <td style="font-size: 0.78rem; white-space: nowrap;">
                                {{ $rec->end_time ? $rec->end_time->format('h:i A') : '—' }}
                            </td>
                            <td class="fw-semibold text-center">
                                @if($rec->actual_time)
                                    {{ $rec->actual_time >= 60 ? intdiv($rec->actual_time, 60).'h '.($rec->actual_time % 60).'m' : $rec->actual_time.'m' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-end fw-bold {{ $isPackage ? 'text-success' : 'text-dark' }}">
                                @if($isPackage)
                                    <span class="text-muted fw-normal" style="font-size:0.75rem;">—</span>
                                @else
                                    Rs. {{ number_format($rec->amount) }}
                                    @if($sessionPt)
                                        <span class="text-muted fw-normal" style="font-size: 0.75rem;">({{ $sessionPt }})</span>
                                    @endif
                                @endif
                            </td>
                            <td class="text-end fw-semibold">
                                @if($extraAmount > 0)
                                    <span style="color: #d97706;" title="{{ $rowPaidExtras->count() }} extras">
                                        Rs. {{ number_format($extraAmount) }}
                                    </span>
                                    @if(!empty($extraPtNames))
                                        <span class="text-muted fw-normal" style="font-size: 0.75rem;">({{ $extraPtNames }})</span>
                                    @endif
                                @else
                                    <span class="text-muted fw-normal" style="font-size:0.75rem;">—</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-dark">
                                @if($isPackage && $extraAmount == 0)
                                    <span class="text-muted fw-normal" style="font-size:0.75rem;">—</span>
                                @else
                                    Rs. {{ number_format($rowTotal) }}
                                @endif
                            </td>
                            <td>
                                @if($isPackage)
                                    <span class="fw-bold text-success" style="font-size: 0.78rem;">📦 {{ __('Pkg') }}</span>
                                @else
                                    <span class="fw-bold text-primary" style="font-size: 0.78rem;">🎮 {{ __('Normal') }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: #f8fafc; font-size: 0.82rem;">
                        <td colspan="2" class="fw-bold text-end text-dark py-2" style="border-top: 2px solid #e2e8f0;">{{ __('Totals') }}</td>
                        <td class="fw-bold text-center py-2" style="border-top: 2px solid #e2e8f0;">{{ $totalPlayers }}</td>
                        <td colspan="2" class="py-2" style="border-top: 2px solid #e2e8f0;"></td>
                        <td class="fw-bold text-center py-2" style="border-top: 2px solid #e2e8f0;">
                            {{ $totalMinutes >= 60 ? intdiv($totalMinutes, 60).'h '.($totalMinutes % 60).'m' : $totalMinutes.'m' }}
                        </td>
                        <td class="fw-bold text-end text-dark py-2" style="border-top: 2px solid #e2e8f0;">Rs. {{ number_format($playRecordsEarnings) }}</td>
                        <td class="fw-bold text-end py-2" style="color: #d97706; border-top: 2px solid #e2e8f0;">Rs. {{ number_format($extrasTotal) }}</td>
                        <td class="fw-bold text-end text-success py-2" style="border-top: 2px solid #e2e8f0;">Rs. {{ number_format($playRecordsEarnings + $extrasTotal) }}</td>
                        <td class="py-2" style="border-top: 2px solid #e2e8f0;"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</div>

<!-- Extras Earnings Detail Modal -->
<div class="modal fade" id="extrasDetailModal" tabindex="-1" aria-labelledby="extrasDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <span style="font-size: 1.4rem;">🍿</span>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="extrasDetailModalLabel">{{ __('Extras Earnings Details') }}</h5>
                        <div class="text-white-50" style="font-size: 0.75rem;">
                            {{ \Carbon\Carbon::parse($from)->format('M d, Y') }} &mdash; {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                {{-- Quick Summary Stats --}}
                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <div class="summary-pill p-2 rounded text-center border bg-light">
                            <div class="text-muted" style="font-size: 0.68rem; text-transform: uppercase; font-weight: 700;">{{ __('Total Earnings') }}</div>
                            <div class="fw-bold fs-6" style="color: #d97706;">Rs. {{ number_format($extrasTotal) }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="summary-pill p-2 rounded text-center border bg-light">
                            <div class="text-muted" style="font-size: 0.68rem; text-transform: uppercase; font-weight: 700;">{{ __('Items Sold') }}</div>
                            <div class="fw-bold fs-6 text-primary">{{ $paidExtras->sum('quantity') }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="summary-pill p-2 rounded text-center border bg-light">
                            <div class="text-muted" style="font-size: 0.68rem; text-transform: uppercase; font-weight: 700;">{{ __('Transactions') }}</div>
                            <div class="fw-bold fs-6 text-success">{{ $paidExtras->count() }}</div>
                        </div>
                    </div>
                </div>

                @php
                    $extrasByTitle = $paidExtras->groupBy(function($item) {
                        return optional($item->extra)->title ?? __('Unknown');
                    })->map(function($group, $title) {
                        return [
                            'title' => $title,
                            'total_quantity' => $group->sum('quantity'),
                            'total_amount' => $group->sum('total_amount'),
                            'count' => $group->count(),
                        ];
                    })->sortByDesc('total_quantity');
                @endphp

                @if(!$paidExtras->isEmpty() && !$extrasByTitle->isEmpty())
                    {{-- Title-wise Total Quantity Breakdown --}}
                    <div class="mb-3 p-3 rounded border title-summary-card" style="background-color: #f8fafc; border-radius: 12px;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold text-dark text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                📦 {{ __('Extra Item-wise Total Quantity') }}
                            </span>
                            <span class="badge bg-secondary rounded-pill px-2" style="font-size: 0.7rem;">
                                {{ $extrasByTitle->count() }} {{ __('Types') }}
                            </span>
                        </div>
                        <div class="row g-2">
                            @foreach($extrasByTitle as $item)
                                <div class="col-sm-6 col-md-4">
                                    <div class="title-summary-pill p-2.5 rounded border bg-white d-flex align-items-center justify-content-between shadow-sm">
                                        <div class="text-truncate me-2" title="{{ $item['title'] }}">
                                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.88rem;">{{ $item['title'] }}</div>
                                            <small class="text-muted" style="font-size: 0.75rem;">Rs. {{ number_format($item['total_amount']) }}</small>
                                        </div>
                                        <div class="text-end ps-2">
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2.5 py-1 fw-bold fs-6">
                                                {{ $item['total_quantity'] }} <span style="font-size: 0.65rem; font-weight: 500;">{{ __('qty') }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($paidExtras->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <div style="font-size: 2.2rem;">🍿</div>
                        <div class="fw-semibold mt-2">{{ __('No extra earnings recorded for this date range.') }}</div>
                    </div>
                @else
                    <div class="table-responsive" style="max-height: 420px;">
                        <table class="table table-sm align-middle table-hover mb-0" style="font-size: 0.83rem;">
                            <thead class="table-dark sticky-top" style="font-size: 0.72rem; text-transform: uppercase;">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Item / Extra') }}</th>
                                    <th class="text-center">{{ __('Qty') }}</th>
                                    <th class="text-end">{{ __('Rate') }}</th>
                                    <th class="text-end">{{ __('Total') }}</th>
                                    <th>{{ __('Payment') }}</th>
                                    <th>{{ __('Session / Player') }}</th>
                                    <th>{{ __('Date & Time') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paidExtras as $idx => $extra)
                                    @php
                                        $itemName = optional($extra->extra)->title ?? '—';
                                        $unitPrice = optional($extra->extra)->amount ?? ($extra->quantity > 0 ? $extra->total_amount / $extra->quantity : 0);
                                        $rec = $extra->playRecord;
                                        $playerName = $rec ? ($rec->name ?: optional(optional($rec->playerPackage)->player)->name ?? '—') : __('Direct Sale');
                                        $tokenName = optional(optional($rec)->token)->name;
                                    @endphp
                                    <tr>
                                        <td class="text-muted" style="font-size: 0.75rem;">{{ $idx + 1 }}</td>
                                        <td class="fw-bold text-dark">{{ $itemName }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary rounded-pill px-2">{{ $extra->quantity }}</span>
                                        </td>
                                        <td class="text-end text-muted">Rs. {{ number_format($unitPrice) }}</td>
                                        <td class="text-end fw-bold text-dark">Rs. {{ number_format($extra->total_amount) }}</td>
                                        <td>
                                            @php
                                                $ptName = optional($extra->paymentType)->name ?? '—';
                                                $ptBadgeClass = match(strtolower($ptName)) {
                                                    'cash' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
                                                    'card' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',
                                                    'online' => 'bg-purple bg-opacity-10 text-purple border border-purple border-opacity-25',
                                                    default => 'bg-light text-dark border'
                                                };
                                            @endphp
                                            <span class="badge {{ $ptBadgeClass }}" style="font-size: 0.72rem;">{{ $ptName }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">{{ $playerName }}</span>
                                            @if($tokenName)
                                                <small class="badge bg-light text-dark border ms-1">{{ $tokenName }}</small>
                                            @endif
                                        </td>
                                        <td style="font-size: 0.75rem; white-space: nowrap;" class="text-muted">
                                            {{ $extra->created_at ? $extra->created_at->format('M d, h:i A') : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="sticky-bottom bg-light">
                                <tr class="fw-bold" style="font-size: 0.83rem;">
                                    <td colspan="2" class="text-end py-2">{{ __('Total') }}:</td>
                                    <td class="text-center py-2">{{ $paidExtras->sum('quantity') }}</td>
                                    <td class="py-2"></td>
                                    <td class="text-end py-2 text-dark">Rs. {{ number_format($extrasTotal) }}</td>
                                    <td colspan="3" class="py-2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
            <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-between align-items-center">
                <div class="text-muted" style="font-size: 0.78rem;">
                    {{ __('Showing all settled extra earnings for selected period') }}
                </div>
                <button type="button" class="btn btn-secondary btn-sm fw-semibold px-4" style="border-radius: 8px;" data-bs-dismiss="modal">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
