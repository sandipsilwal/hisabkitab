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
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 no-print">
    <div>
        <h4 class="fw-bold mb-0" style="color: #0f172a;">📊 {{ __('Play Records Report') }}</h4>
        <p class="text-muted mb-0" style="font-size: 0.82rem;">{{ __('Showing completed sessions for') }} {{ \Carbon\Carbon::parse($from)->format('M d, Y') }} &mdash; {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</p>
    </div>
    <div class="page-header-actions">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm fw-semibold" style="border-radius: 8px; font-size: 0.8rem;">
            🖨 {{ __('Print') }}
        </button>
    </div>
</div>

{{-- Date Filter Bar --}}
<div class="filter-bar p-3 mb-4 no-print">
    <form method="GET" action="{{ route('skatepark.reports') }}" class="row g-2 align-items-end">
        <div class="col-auto">
            <label class="form-label fw-semibold mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('From') }}</label>
            <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}" style="border-radius: 7px; font-size: 0.85rem; max-width: 160px;">
        </div>
        <div class="col-auto">
            <label class="form-label fw-semibold mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('To') }}</label>
            <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}" style="border-radius: 7px; font-size: 0.85rem; max-width: 160px;">
        </div>
        <div class="col-auto d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm fw-bold" style="border-radius: 7px; font-size: 0.82rem;">
                🔍 {{ __('Apply') }}
            </button>
            <a href="{{ route('skatepark.reports', ['from' => \Carbon\Carbon::today()->toDateString(), 'to' => \Carbon\Carbon::today()->toDateString()]) }}"
               class="btn btn-outline-secondary btn-sm fw-semibold" style="border-radius: 7px; font-size: 0.82rem;">
                {{ __('Today') }}
            </a>
            <a href="{{ route('skatepark.reports', ['from' => \Carbon\Carbon::now()->startOfMonth()->toDateString(), 'to' => \Carbon\Carbon::today()->toDateString()]) }}"
               class="btn btn-outline-secondary btn-sm fw-semibold" style="border-radius: 7px; font-size: 0.82rem;">
                {{ __('This Month') }}
            </a>
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
    {{-- Total Sessions --}}
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card report-summary-card h-100 p-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="card-icon" style="background: #dbeafe;">🎮</div>
                <span class="stat-label">{{ __('Sessions') }}</span>
            </div>
            <div class="stat-value text-primary">{{ $totalSessions }}</div>
            <div class="mt-auto pt-2 d-flex gap-1 flex-wrap" style="font-size: 0.68rem;">
                <span class="badge bg-primary bg-opacity-10 text-primary">{{ $normalCount }} {{ __('Normal') }}</span>
                <span class="badge bg-success bg-opacity-10 text-success">{{ $packageCount }} {{ __('Pkg') }}</span>
            </div>
        </div>
    </div>

    {{-- Total Players --}}
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card report-summary-card h-100 p-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="card-icon" style="background: #ede9fe;">👥</div>
                <span class="stat-label">{{ __('Players') }}</span>
            </div>
            <div class="stat-value text-purple" style="color: #7c3aed;">{{ $totalPlayers }}</div>
            <div class="text-muted mt-auto pt-2" style="font-size: 0.7rem;">{{ __('Total headcount') }}</div>
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
                $count   = $ptCounts[$ptName] ?? 0;
                $pct     = $normalRevenue > 0 ? round(($ptAmount / $normalRevenue) * 100) : 0;
            @endphp
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card report-summary-card h-100 p-3 {{ $className }}">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="card-icon" style="font-size: 1.3rem;">{{ $color['icon'] }}</div>
                        <span class="stat-label">{{ $ptName }}</span>
                    </div>
                    <div class="stat-value" style="font-size: 1.35rem;">Rs. {{ number_format($ptAmount) }}</div>
                    <div class="d-flex align-items-center gap-1 mt-auto pt-2 card-footer-text" style="font-size: 0.68rem;">
                        <span>{{ $count }} {{ __('sessions') }}</span>
                        <span class="ms-auto fw-bold">{{ $pct }}%</span>
                    </div>
                    {{-- Mini progress bar --}}
                    <div class="mt-1" style="height: 4px; background: rgba(0,0,0,0.06); border-radius: 10px; overflow: hidden;">
                        <div style="width: {{ $pct }}%; height: 100%; background: {{ $color['dot'] }}; border-radius: 10px; transition: width 0.6s ease;"></div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Grand Total card --}}
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card report-summary-card h-100 p-3" style="background: #0f172a; border: 1.5px solid #334155 !important;">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="card-icon" style="background: rgba(234, 179, 8, 0.15); font-size: 1.3rem;">💰</div>
                    <span class="stat-label" style="color: #eab308;">{{ __('Total') }}</span>
                </div>
                <div class="stat-value" style="color: #fef3c7; font-size: 1.35rem;">Rs. {{ number_format($normalRevenue) }}</div>
                <div class="d-flex align-items-center gap-1 mt-auto pt-2" style="font-size: 0.68rem; color: #94a3b8;">
                    <span>{{ $normalCount }} {{ __('sessions') }}</span>
                    <span class="ms-auto fw-bold" style="color: #eab308;">100%</span>
                </div>
                <div class="mt-1" style="height: 4px; background: #334155; border-radius: 10px;"></div>
            </div>
        </div>
    @endif
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
                        <th>{{ __('Token') }}</th>
                        <th>{{ __('Players') }}</th>
                        <th>{{ __('Start') }}</th>
                        <th>{{ __('End') }}</th>
                        <th>{{ __('Actual Time') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Payment') }}</th>
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
                        @endphp
                        <tr class="{{ $isPackage ? 'pkg-row' : 'normal-row' }}">
                            <td class="text-muted" style="font-size: 0.75rem;">{{ $i + 1 }}</td>
                            <td class="fw-semibold" style="max-width: 140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $displayName }}</td>
                            <td>
                                <span class="fw-bold text-dark">{{ optional($rec->token)->name ?? '—' }}</span>
                            </td>
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
                            <td class="fw-bold {{ $isPackage ? 'text-success' : 'text-dark' }}">
                                @if($isPackage)
                                    <span class="text-muted fw-normal" style="font-size:0.75rem;">—</span>
                                @else
                                    Rs. {{ number_format($rec->amount) }}
                                @endif
                            </td>
                            <td>
                                @if(!$isPackage)
                                    <span class="fw-semibold text-dark">{{ optional($rec->paymentType)->name ?? '—' }}</span>
                                @else
                                    <span class="text-muted" style="font-size:0.75rem;">—</span>
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
                        <td colspan="3" class="fw-bold text-end text-dark py-2" style="border-top: 2px solid #e2e8f0;">{{ __('Totals') }}</td>
                        <td class="fw-bold text-center py-2" style="border-top: 2px solid #e2e8f0;">{{ $totalPlayers }}</td>
                        <td colspan="2" class="py-2" style="border-top: 2px solid #e2e8f0;"></td>
                        <td class="fw-bold text-center py-2" style="border-top: 2px solid #e2e8f0;">{{ $totalMinutes }}m</td>
                        <td class="fw-bold text-warning py-2" style="border-top: 2px solid #e2e8f0;">Rs. {{ number_format($totalRevenue) }}</td>
                        <td colspan="2" class="py-2" style="border-top: 2px solid #e2e8f0;"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</div>

@endsection
