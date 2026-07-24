@extends('layouts.app')

@section('content')
<!-- Google Fonts: Outfit & Plus Jakarta Sans -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<!-- Custom Styles for Skate Park Command Center Dashboard -->
<style>
    body, .modal, button, input, select, .btn {
        font-family: 'Outfit', 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    }
    .skate-item {
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        padding: 0.75rem 0.5rem;
        transition: all 0.2s ease-in-out;
    }
    .skate-item:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    .timer-display {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace;
        font-size: 1.45rem;
        font-weight: 800;
        letter-spacing: -0.5px;
    }
    .badge-token {
        font-size: 1.15rem;
        font-weight: 800;
        letter-spacing: 0.5px;
    }
    [id^="timer-sub-"] {
        font-size: 0.58rem !important;
        letter-spacing: 0.2px;
        text-transform: uppercase;
    }
    .warning-yellow {
        background-color: rgba(234, 179, 8, 0.04) !important;
        border-bottom: 1px solid #eab308 !important;
        animation: pulse-yellow-tint 2s infinite;
    }
    .skate-item .timer-display.timer-warning-yellow,
    body.dark-mode .skate-item .timer-display.timer-warning-yellow {
        color: #eab308 !important;
    }
    .overtime-red {
        background-color: rgba(239, 68, 68, 0.04) !important;
        border-bottom: 1px solid #ef4444 !important;
        animation: pulse-red-tint 1.5s infinite;
    }
    @media (min-width: 992px) {
        .border-end-desktop {
            border-right: 1px solid rgba(0, 0, 0, 0.08);
        }
        body.dark-mode .border-end-desktop {
            border-right: 1px solid #ffffff !important;
        }
    }
    .btn-step {
        border-radius: 12px;
        padding: 1rem;
        font-weight: 700;
        font-size: 1.05rem;
        transition: all 0.2s ease;
    }
    .btn-step:hover {
        transform: scale(1.03);
    }
    
    @keyframes pulse-yellow-tint {
        0% { background-color: rgba(234, 179, 8, 0.02); }
        50% { background-color: rgba(234, 179, 8, 0.08); }
        100% { background-color: rgba(234, 179, 8, 0.02); }
    }
    @keyframes pulse-red-tint {
        0% { background-color: rgba(239, 68, 68, 0.02); }
        50% { background-color: rgba(239, 68, 68, 0.08); }
        100% { background-color: rgba(239, 68, 68, 0.02); }
    }
    .payment-alert-bg {
        background-color: rgba(245, 158, 11, 0.12) !important;
        border-left: 4px solid #f59e0b !important;
    }
    body.dark-mode .payment-alert-bg {
        background-color: rgba(245, 158, 11, 0.22) !important;
        border-left: 4px solid #fbbf24 !important;
    }
    .speaker-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        background-color: rgba(255, 255, 255, 0.8) !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.08) !important;
    }
    .speaker-btn:hover {
        transform: scale(1.15);
        background-color: #ffffff !important;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .speaker-btn:active {
        transform: scale(0.95);
    }
    /* Scrollbar customizations */
    #playing-list::-webkit-scrollbar, #overtime-list::-webkit-scrollbar {
        width: 5px;
    }
    #playing-list::-webkit-scrollbar-track, #overtime-list::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.02);
        border-radius: 4px;
    }
    #playing-list::-webkit-scrollbar-thumb, #overtime-list::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.12);
        border-radius: 4px;
    }
    #playing-list::-webkit-scrollbar-thumb:hover, #overtime-list::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.25);
    }
    #playing-placeholder, #overtime-placeholder {
        margin: auto 0 !important;
        padding: 3rem 0 !important;
    }
    
    .list-scroll-container {
        height: 530px; 
        overflow-y: auto; 
        padding-right: 8px;
    }

    @media (max-width: 991.98px) {
        .list-scroll-container {
            height: auto !important;
            max-height: none !important;
        }
        #playing-placeholder, #overtime-placeholder {
            padding: 0.5rem 0 !important;
            font-size: 0.85rem;
        }
        #playing-placeholder i, #overtime-placeholder i,
        #playing-placeholder br, #overtime-placeholder br {
            display: none !important;
        }
    }

    body.dark-mode .skate-item {
        border-bottom-color: #ffffff !important;
    }
    body.dark-mode .skate-item .text-dark,
    body.dark-mode .skate-item strong.text-dark {
        color: #ffffff !important;
    }
    body.dark-mode .skate-item .timer-display {
        color: #f8fafc !important;
    }
    body.dark-mode .skate-item .text-secondary {
        color: #cbd5e1 !important;
    }
    body.dark-mode .border-top,
    body.dark-mode .border-bottom {
        border-color: #ffffff !important;
    }
    body.dark-mode .btn-outline-primary {
        color: #38bdf8 !important;
        border-color: #38bdf8 !important;
    }
    body.dark-mode .btn-outline-primary:hover {
        background-color: #38bdf8 !important;
        color: #000000 !important;
    }
    body.dark-mode .btn-outline-success {
        color: #4ade80 !important;
        border-color: #4ade80 !important;
    }
    body.dark-mode .btn-outline-success:hover {
        background-color: #4ade80 !important;
        color: #000000 !important;
    }
    body.dark-mode .btn-outline-warning {
        color: #facc15 !important;
        border-color: #facc15 !important;
    }
    body.dark-mode .btn-outline-warning:hover {
        background-color: #facc15 !important;
        color: #000000 !important;
    }
    body.dark-mode .btn-outline-danger {
        color: #f87171 !important;
        border-color: #f87171 !important;
    }
    body.dark-mode .btn-outline-danger:hover {
        background-color: #f87171 !important;
        color: #000000 !important;
    }
    body.dark-mode .btn-light {
        background-color: #1a1a1a !important;
        color: #f8fafc !important;
        border-color: rgba(255, 255, 255, 0.12) !important;
    }
    body.dark-mode .btn-light:hover {
        background-color: #262626 !important;
    }

    body.dark-mode .token-selector-col button.btn-outline-primary span.text-muted {
        color: #94a3b8 !important;
    }
    body.dark-mode .token-selector-col button.btn-outline-primary:hover span.text-muted {
        color: #1e293b !important;
    }
    body.dark-mode #playing-list::-webkit-scrollbar-thumb,
    body.dark-mode #overtime-list::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.15) !important;
    }
    /* Clean Outline Hover for Token Selector Buttons */
    #add-tokens-container .btn-outline-primary,
    #pkg-tokens-container .btn-outline-success {
        transition: all 0.2s ease-in-out;
    }
    
    #add-tokens-container .btn-outline-primary:hover,
    #add-tokens-container .btn-outline-primary:active,
    #add-tokens-container .btn-outline-primary:focus,
    #pkg-tokens-container .btn-outline-success:hover,
    #pkg-tokens-container .btn-outline-success:active,
    #pkg-tokens-container .btn-outline-success:focus {
        background-color: transparent !important;
        color: #eab308 !important;
        border-color: #eab308 !important;
        box-shadow: 0 0 0 0.25rem rgba(234, 179, 8, 0.15) !important;
    }

    /* Dark Mode specific clean outline hover */
    body.dark-mode #add-tokens-container .btn-outline-primary:hover,
    body.dark-mode #add-tokens-container .btn-outline-primary:active,
    body.dark-mode #add-tokens-container .btn-outline-primary:focus,
    body.dark-mode #pkg-tokens-container .btn-outline-success:hover,
    body.dark-mode #pkg-tokens-container .btn-outline-success:active,
    body.dark-mode #pkg-tokens-container .btn-outline-success:focus {
        background-color: transparent !important;
        color: #eab308 !important;
        border-color: #eab308 !important;
        box-shadow: 0 0 0 0.25rem rgba(234, 179, 8, 0.25) !important;
    }

    /* Nepali datepicker calendar must appear above Bootstrap modals (z-index: 1055) */
    .ndp-container {
        z-index: 1100 !important;
    }

    /* Add Transaction Button Custom Styling & Premium Hover Effects */
    .btn-add-transaction {
        background-color: #000000 !important;
        border: 2px solid #333333 !important;
        color: #ffffff !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    .btn-add-transaction:hover,
    .btn-add-transaction:active,
    .btn-add-transaction:focus {
        background-color: #111111 !important;
        border-color: #6366f1 !important; /* Indigo accent border */
        color: #ffffff !important;
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.3) !important;
        transform: translateY(-2px) !important;
    }

    body.dark-mode .btn-add-transaction {
        border-color: #4b5563 !important;
    }

    body.dark-mode .btn-add-transaction:hover,
    body.dark-mode .btn-add-transaction:active,
    body.dark-mode .btn-add-transaction:focus {
        background-color: #111111 !important;
        border-color: #eab308 !important; /* Matches theme's dark mode yellow/gold accent */
        color: #ffffff !important;
        box-shadow: 0 6px 20px rgba(234, 179, 8, 0.35) !important;
        transform: translateY(-2px) !important;
    }

    /* Mobile Responsive Overrides for smaller fonts and buttons */
    @media (max-width: 575.98px) {
        .timer-display {
            font-size: 1.15rem !important;
        }
        .badge-token {
            font-size: 0.95rem !important;
        }
        h5.fw-bold.text-dark {
            font-size: 1.1rem !important;
        }
        .skate-item {
            padding: 0.5rem 0.25rem;
        }
        .skate-item .btn {
            font-size: 0.75rem !important;
            height: 30px !important;
            padding: 0 0.5rem !important;
        }
        .action-btn-container-0 button, .action-btn-container-0 .btn {
            width: auto !important;
        }
        /* Make top action buttons slightly smaller */
        .row.mb-4.g-2 .btn {
            font-size: 0.9rem !important;
            padding-top: 0.4rem !important;
            padding-bottom: 0.4rem !important;
        }
    }
</style>

<div class="row mb-4 g-2">
    <!-- Action Buttons -->
    <div class="col-6 col-md-4">
        <button class="btn btn-success w-100 px-2 px-md-4 py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-1 gap-md-2" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#packagePlayModal">
            <span>📦</span> {{ __('Package Play') }}
        </button>
    </div>

    {{-- Add Transaction (center) --}}
    <div class="col-6 col-md-4">
        <button class="btn btn-add-transaction w-100 px-2 px-md-4 py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-1 gap-md-2" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
            <span>💳</span> {{ __('Add Transaction') }}
        </button>
    </div>

    <div class="col-12 col-md-4">
        <button class="btn w-100 px-3 px-md-4 py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" style="border-radius: 10px; background-color: #2ecc71 !important; border-color: #2ecc71 !important; color: #ffffff !important;" data-bs-toggle="modal" data-bs-target="#addPlayModal">
            <span>➕</span> {{ __('Add Play') }}
        </button>
    </div>
</div>

<!-- Main Split Board View -->
<div class="row d-flex flex-column-reverse flex-lg-row">
    <!-- Left Column: Playing Players -->
    <div class="col-lg-6 mb-4 border-end-desktop pe-lg-4">
        <div class="d-flex justify-content-center align-items-center gap-2 mb-3 pb-2">
            <h5 class="mb-0 fw-bold text-dark" style="font-size: 1.25rem;">
                {{ __('Playing Players') }}
            </h5>
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 fw-semibold" id="count-playing" style="font-size: 0.8rem;">0 {{ __('Active') }}</span>
        </div>
        <div class="d-flex flex-column gap-2 list-scroll-container" id="playing-list">
            <!-- Inner items will be dynamically appended here -->
            <div class="text-center py-5 text-muted my-auto" id="playing-placeholder">
                <i class="fs-1 d-block mb-2 text-secondary">🛹</i>
                {!! __('No playing players right now.<br>Click "Add Play" or "Package Play" to start!') !!}
            </div>
        </div>
    </div>

    <!-- Right Column: Over Time Players -->
    <div class="col-lg-6 mb-4 ps-lg-4">
        <div class="d-flex justify-content-center align-items-center gap-2 mb-3 pb-2">
            <h5 class="mb-0 fw-bold text-dark" style="font-size: 1.25rem;">
                {{ __('Over Time Players') }}
            </h5>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5 fw-semibold" id="count-overtime" style="font-size: 0.8rem;">0 {{ __('Expired') }}</span>
        </div>
        <div class="d-flex flex-column gap-2 list-scroll-container" id="overtime-list">
            <!-- Inner items will be dynamically appended here -->
            <div class="text-center py-5 text-muted my-auto" id="overtime-placeholder">
                <i class="fs-1 d-block mb-2 text-secondary">🔔</i>
                {!! __('No players in overtime.<br>Players whose duration expires will appear here.') !!}
            </div>
        </div>
    </div>
</div>

<!-- ==============================================
     MODAL: ADD NORMAL PLAY (Multi-Step popup)
     ============================================== -->
<div class="modal fade" id="addPlayModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg mt-5 mt-md-4">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold">{{ __('Add Play Session') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Progress Stepper indicator -->
                <div class="d-flex justify-content-between mb-4 text-center">
                    <div class="flex-grow-1 border-bottom border-primary border-3 pb-2 text-primary fw-bold" id="step-ind-1">{{ __('1. Token') }}</div>
                    <div class="flex-grow-1 border-bottom pb-2 text-muted fw-semibold" id="step-ind-2">{{ __('2. Duration') }}</div>
                    <div class="flex-grow-1 border-bottom pb-2 text-muted fw-semibold" id="step-ind-3">{{ __('3. Players') }}</div>
                    <div class="flex-grow-1 border-bottom pb-2 text-muted fw-semibold" id="step-ind-4">{{ __('4. Save') }}</div>
                </div>

                <!-- STEP 1: TOKEN SELECTION -->
                <div id="add-step-1">

                    <!-- Player Name Entry (name-based path) -->
                    <div class="mb-3 p-3 border rounded" style="border-radius: 10px !important; background: linear-gradient(135deg, rgba(99,102,241,0.05), rgba(168,85,247,0.05)); border-color: rgba(99,102,241,0.2) !important;">
                        <label for="add-player-name" class="form-label fw-bold mb-1" style="font-size: 0.85rem; color: #6366f1;">🎮 {{ __('Player Name') }}</label>
                        <p class="text-muted mb-2" style="font-size: 0.75rem;">{{ __('Enter the player\'s name and proceed — or skip directly by selecting a token below.') }}</p>
                        <div class="input-group">
                            <input type="text" class="form-control" id="add-player-name" placeholder="{{ __('e.g. Sandip, Team Alpha...') }}" style="border-radius: 8px 0 0 8px; font-size: 0.95rem;" autocomplete="off">
                            <button type="button" class="btn btn-primary fw-bold px-4" onclick="submitPlayerNameAndProceed()" style="border-radius: 0 8px 8px 0;">{{ __('Proceed') }} →</button>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <hr class="flex-grow-1 m-0">
                        <span class="text-muted fw-semibold" style="font-size: 0.78rem; white-space: nowrap;">{{ __('OR SELECT A TOKEN') }}</span>
                        <hr class="flex-grow-1 m-0">
                    </div>

                    <h5 class="fw-bold mb-3 text-dark">{{ __('Select Token') }}</h5>
                    <div class="row g-3" id="add-tokens-container">
                        @forelse($tokens as $index => $t)
                            <div class="{{ $index < 2 ? 'col-md-6 col-6' : 'col-md-2 col-4' }} token-selector-col mb-2" data-token-id="{{ $t->id }}">
                                <button type="button" class="btn btn-outline-primary w-100 py-1 px-0.5 fw-bold" onclick="selectToken({{ $t->id }}, '{{ $t->name }}', {{ $t->game_type_id }}, '{{ $t->gameType->game_name }}')" style="border-radius: 8px; font-size: 1.15rem; min-height: 36px; display: flex; align-items: center; justify-content: center; padding-top: 0.15rem; padding-bottom: 0.15rem;">
                                    {{ $t->name }}
                                </button>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted col-12">{{ __('No active tokens available. Make sure tokens are registered in masters.') }}</div>
                        @endforelse
                        <div class="text-center py-4 text-muted col-12 d-none" id="add-no-tokens-alert">
                            {{ __('No active tokens available (all tokens are currently in use!).') }}
                        </div>
                    </div>
                </div>

                <!-- STEP 2: DURATION SELECTION -->
                <div id="add-step-2" class="d-none">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-dark">{{ __('Select Duration') }}</h5>
                        <button type="button" class="btn btn-dark fw-bold px-3 py-2" onclick="selectDuration(0, 'No Limit')" style="border-radius: 8px; font-size: 0.9rem;">{{ __('No Limit') }}</button>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        @foreach($defaultTimes as $dt)
                            <div class="col-md-4 col-6 mb-2">
                                <button type="button" class="btn btn-outline-success w-100 py-2 px-1 fw-bold" onclick="selectDuration({{ $dt->minutes }}, '{{ $dt->label }}', {{ $dt->id }})" style="border-radius: 8px; font-size: 1.15rem; min-height: 40px; display: flex; align-items: center; justify-content: center;">
                                    {{ $dt->label }}
                                </button>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="p-3 bg-light rounded" style="border-radius: 12px;">
                        <h6 class="fw-bold mb-2">{{ __('Custom Time Entry (Minutes)') }}</h6>
                        <div class="input-group">
                            <input type="number" class="form-control" id="custom-minutes" placeholder="{{ __('Enter custom minutes') }}" min="1">
                            <button type="button" class="btn btn-success fw-bold px-4" onclick="submitCustomDuration()">{{ __('Next Step') }}</button>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: NUMBER OF PLAYERS -->
                <div id="add-step-3" class="d-none">
                    <h5 class="fw-bold mb-3 text-dark">{{ __('No. of Players') }}</h5>
                    <div class="row g-2 mb-4">
                        @for($i = 1; $i <= 12; $i++)
                            <div class="col-md-2 col-3 mb-2">
                                <button type="button" class="btn btn-outline-warning w-100 py-2 px-1 fw-bold" onclick="selectPlayerCount({{ $i }})" style="border-radius: 8px; font-size: 1.25rem; min-height: 42px; display: flex; align-items: center; justify-content: center;">
                                    {{ $i }}
                                </button>
                            </div>
                        @endfor
                    </div>

                    <div class="p-3 bg-light rounded" style="border-radius: 12px;">
                        <h6 class="fw-bold mb-2">{{ __('Custom Player Entry') }}</h6>
                        <div class="input-group">
                            <input type="number" class="form-control" id="custom-players" placeholder="{{ __('Number of players') }}" min="1" value="1">
                            <button type="button" class="btn btn-warning fw-bold px-4 text-dark" onclick="submitCustomPlayers()">{{ __('Next Step') }}</button>
                        </div>
                    </div>
                </div>

                <!-- STEP 4: SUMMARY & AMOUNT (SAVE) -->
                <div id="add-step-4" class="d-none">

                    <!-- TOP ROW: Amount (left) + Save & Launch (right) — aligned from top -->
                    <div class="row g-2 mb-3 align-items-start">
                        <div class="col-12 col-sm-6">
                            <label for="play-amount" class="form-label fw-bold mb-1" style="font-size: 0.85rem;">{{ __('Charge Amount (Rs.)') }}</label>
                            <input type="number" class="form-control border-2 border-primary" id="play-amount" required min="0" onclick="this.select()" style="border-radius: 8px; font-size: 1.6rem; font-weight: 800; height: 52px; padding: 0.4rem 0.75rem; letter-spacing: -0.5px;">
                            <div class="form-text text-muted" style="font-size: 0.67rem;">{{ __('Auto-calculated. Can override.') }}</div>
                        </div>
                        <div class="col-12 col-sm-6 d-flex flex-column mt-sm-0 mt-2">
                            <label class="form-label fw-bold mb-1 d-none d-sm-block" style="font-size: 0.85rem; visibility: hidden;">{{ __('Action') }}</label>
                            <button type="button" class="btn fw-bold d-none w-100" id="btn-add-submit" onclick="submitPlayRecord()" style="border-radius: 10px; background-color: #15803d !important; border-color: #15803d !important; color: #ffffff !important; font-size: 1.05rem; height: 52px; letter-spacing: 0.3px; box-shadow: 0 4px 16px rgba(21,128,61,0.25); transition: all 0.2s ease; margin-top: 3px;">🚀 {{ __('Save & Launch') }}</button>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-2 text-dark">{{ __('Session Summary') }}</h6>
                    <div class="row mb-2 px-2 py-2 bg-light rounded align-items-center" style="border-radius: 8px;">
                        <div class="col-4 mb-1">
                            <span class="text-muted d-block text-uppercase" style="font-size: 0.6rem; letter-spacing: 0.5px;">{{ __('Token / Player') }}</span>
                            <span class="fw-bold text-dark" style="font-size: 0.88rem;" id="summary-token">-</span>
                        </div>
                        <div class="col-4 mb-1">
                            <span class="text-muted d-block text-uppercase" style="font-size: 0.6rem; letter-spacing: 0.5px;">{{ __('Time Limit') }}</span>
                            <span class="fw-bold text-dark" style="font-size: 0.88rem;" id="summary-duration">-</span>
                        </div>
                        <div class="col-4 mb-1">
                            <span class="text-muted d-block text-uppercase" style="font-size: 0.6rem; letter-spacing: 0.5px;">{{ __('Players') }}</span>
                            <span class="fw-bold text-dark" style="font-size: 0.88rem;" id="summary-players">-</span>
                        </div>
                    </div>

                    <!-- Payment Type (Player Name moved to Step 1) -->
                    <div class="row g-2">
                        <div class="col-12">
                            <label for="play-payment" class="form-label fw-semibold mb-1" style="font-size: 0.8rem;">{{ __('Payment Type') }}</label>
                            <select class="form-select" id="play-payment" required style="border-radius: 6px; font-size: 0.85rem; height: 32px; padding: 0.2rem 0.5rem;">
                                @foreach($paymentTypes as $pt)
                                    <option value="{{ $pt->id }}" {{ $defaultPaymentType && $defaultPaymentType->id == $pt->id ? 'selected' : '' }}>{{ $pt->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary fw-semibold px-4" style="border-radius: 8px;" id="btn-add-back" onclick="navigateAddBack()" disabled>{{ __('Back') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================
     MODAL: ADD PACKAGE PLAY (Multi-Step popup)
     ============================================== -->
<div class="modal fade" id="packagePlayModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg mt-5 mt-md-4">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title fw-bold">{{ __('Package Play Session') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Stepper -->
                <div class="d-flex justify-content-between mb-4 text-center">
                    <div class="flex-grow-1 border-bottom border-success border-3 pb-2 text-success fw-bold" id="pkg-step-ind-1">{{ __('1. Player Pkg') }}</div>
                    <div class="flex-grow-1 border-bottom pb-2 text-muted fw-semibold" id="pkg-step-ind-2">{{ __('2. Duration') }}</div>
                    <div class="flex-grow-1 border-bottom pb-2 text-muted fw-semibold" id="pkg-step-ind-3">{{ __('3. Players') }}</div>
                    <div class="flex-grow-1 border-bottom pb-2 text-muted fw-semibold" id="pkg-step-ind-4">{{ __('4. Save') }}</div>
                </div>

                <!-- STEP 1: ACTIVE PLAYER SELECTION -->
                <div id="pkg-step-1">
                    <h5 class="fw-bold mb-3 text-dark">{{ __('Select Player Package') }}</h5>
                    <div class="row g-3" style="max-height: 350px; overflow-y: auto;">
                        @forelse($playerPackages as $pp)
                            <div class="col-md-6">
                                <button type="button" class="btn btn-outline-success btn-step w-100 py-3 text-start d-flex justify-content-between align-items-center" onclick="selectPlayerPackage({{ $pp->id }}, '{{ $pp->player->name }}', {{ $pp->package->game_type_id }}, '{{ $pp->package->gameType->game_name }}')">
                                    <div>
                                        <strong>👤 {{ $pp->player->name }}</strong>
                                        <span class="d-block small text-muted">{{ $pp->package->gameType->game_name }}</span>
                                        <span class="d-block small text-muted font-monospace" style="font-size: 0.75rem;">{{ __('Time per day') }}: {{ $pp->package->time_per_day }} {{ __('min') }} ({{ $pp->package->no_of_days }} {{ __('days') }})</span>
                                    </div>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">{{ __('Active') }}</span>
                                </button>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted col-12">
                                <i class="fs-1 d-block mb-2">👤</i>
                                {!! __('No active (uncompleted) player packages found.<br>Create a player package under Players & Packages profile view first.') !!}
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- STEP 2: DURATION SELECTION -->
                <div id="pkg-step-2" class="d-none">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-dark">{{ __('Select Duration') }}</h5>
                        <button type="button" class="btn btn-dark fw-bold px-3 py-2" onclick="selectPkgDuration(0, 'No Limit')" style="border-radius: 8px; font-size: 0.9rem;">{{ __('No Limit') }}</button>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        @foreach($defaultTimes as $dt)
                            <div class="col-md-4 col-6 mb-2">
                                <button type="button" class="btn btn-outline-success w-100 py-2 px-1 fw-bold" onclick="selectPkgDuration({{ $dt->minutes }}, '{{ $dt->label }}')" style="border-radius: 8px; font-size: 1.15rem; min-height: 40px; display: flex; align-items: center; justify-content: center;">
                                    {{ $dt->label }}
                                </button>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="p-3 bg-light rounded" style="border-radius: 12px;">
                        <h6 class="fw-bold mb-2">{{ __('Custom Time Entry (Minutes)') }}</h6>
                        <div class="input-group">
                            <input type="number" class="form-control" id="pkg-custom-minutes" placeholder="{{ __('Enter custom minutes') }}" min="1">
                            <button type="button" class="btn btn-success fw-bold px-4" onclick="submitPkgCustomDuration()">{{ __('Next Step') }}</button>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: PLAYER COUNT SELECTION -->
                <div id="pkg-step-3" class="d-none">
                    <h5 class="fw-bold mb-3 text-dark">{{ __('No. of Players') }}</h5>
                    <div class="row g-2 mb-4">
                        @for($i = 1; $i <= 12; $i++)
                            <div class="col-md-2 col-3 mb-2">
                                <button type="button" class="btn btn-outline-warning w-100 py-2 px-1 fw-bold" onclick="selectPkgPlayerCount({{ $i }})" style="border-radius: 8px; font-size: 1.25rem; min-height: 42px; display: flex; align-items: center; justify-content: center;">
                                    {{ $i }}
                                </button>
                            </div>
                        @endfor
                    </div>

                    <div class="p-3 bg-light rounded" style="border-radius: 12px;">
                        <h6 class="fw-bold mb-2">{{ __('Custom Player Entry') }}</h6>
                        <div class="input-group">
                            <input type="number" class="form-control" id="pkg-custom-players" placeholder="{{ __('Number of players') }}" min="1" value="1">
                            <button type="button" class="btn btn-warning fw-bold px-4 text-dark" onclick="submitPkgCustomPlayers()">{{ __('Next Step') }}</button>
                        </div>
                    </div>
                </div>

                <!-- STEP 4: SUMMARY & SAVE -->
                <div id="pkg-step-4" class="d-none">
                    <h6 class="fw-bold mb-2 text-dark">{{ __('Package Session Summary') }}</h6>
                    <div class="row mb-2 px-2 py-2 bg-light rounded" style="border-radius: 8px;">
                        <div class="col-6 col-md-4 mb-2">
                            <span class="text-muted d-block text-uppercase" style="font-size: 0.6rem; letter-spacing: 0.5px;">{{ __('Player / Package') }}</span>
                            <span class="fw-bold text-success" style="font-size: 0.88rem;" id="pkg-summary-player">-</span>
                        </div>
                        <div class="col-6 col-md-4 mb-2">
                            <span class="text-muted d-block text-uppercase" style="font-size: 0.6rem; letter-spacing: 0.5px;">{{ __('Time Limit') }}</span>
                            <span class="fw-bold text-dark" style="font-size: 0.88rem;" id="pkg-summary-duration">-</span>
                        </div>
                        <div class="col-6 col-md-4 mb-2">
                            <span class="text-muted d-block text-uppercase" style="font-size: 0.6rem; letter-spacing: 0.5px;">{{ __('Players') }}</span>
                            <span class="fw-bold text-dark" style="font-size: 0.88rem;" id="pkg-summary-players">-</span>
                        </div>
                    </div>
                    
                    <div class="alert alert-info border-0 d-flex align-items-center mb-0 py-2 px-3" role="alert" style="font-size: 0.82rem;">
                        <span class="me-2">💰</span>
                        <div><strong>{{ __('Package Play Session amount is Rs. 0') }}</strong>. {{ __('Payment was billed when the package subscription was purchased.') }}</div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary fw-semibold px-4" style="border-radius: 8px;" id="btn-pkg-back" onclick="navigatePkgBack()">{{ __('Back') }}</button>
                <button type="button" class="btn btn-success fw-semibold px-4 d-none" style="border-radius: 8px;" id="btn-pkg-submit" onclick="submitPkgPlayRecord()">{{ __('Save & Launch') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================
     MODAL: SINGLE STEP EDIT FORM (Direct edit)
     ============================================== -->
<div class="modal fade" id="editPlayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog mt-5 mt-md-4" style="max-width: 600px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold">{{ __('Edit Play Details') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-session-form" onsubmit="submitEditSession(event)">
                <input type="hidden" id="edit-record-id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Column 1: Payment Method -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold d-block mb-2">{{ __('Payment Method') }}</label>
                            <div class="d-flex flex-wrap gap-2" id="payment-buttons-container">
                                @foreach($paymentTypes as $pt)
                                    <button type="button" class="btn btn-outline-primary btn-payment-select px-3 py-2 fw-semibold" data-id="{{ $pt->id }}" onclick="selectEditPayment({{ $pt->id }})" style="border-radius: 8px; font-size: 0.85rem;">
                                        {{ $pt->name }}
                                    </button>
                                @endforeach
                            </div>
                            <input type="hidden" id="edit-payment" name="payment_type_id" required>
                        </div>

                        <!-- Column 2: Player Name -->
                        <div class="col-md-6" id="edit-name-section">
                            <label for="edit-name" class="form-label fw-bold">{{ __('Player Name') }}</label>
                            <input type="text" class="form-control form-control-lg" id="edit-name" placeholder="{{ __('Enter player name') }}" style="border-radius: 8px; font-size: 0.9rem; height: 42px;">
                        </div>

                        <!-- Column 2: Select Token -->
                        <div class="col-md-6">
                            <label for="edit-token-id" class="form-label fw-bold">{{ __('Select Token') }}</label>
                            <select class="form-select form-select-lg" id="edit-token-id" style="border-radius: 8px; font-size: 0.9rem; height: 42px;">
                                <option value="">-- {{ __('No Token (Name Only)') }} --</option>
                                @foreach($tokens as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->gameType->game_name }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Column 3: Time Limit (Minutes) -->
                        <div class="col-md-6">
                            <label for="edit-default-time" class="form-label fw-bold">{{ __('Time Limit (Minutes)') }}</label>
                            <input type="number" class="form-control form-control-lg" id="edit-default-time" placeholder="{{ __('Enter minutes (0 for no limit)') }}" min="0" required style="border-radius: 8px; font-size: 0.9rem; height: 42px;">
                            <div class="form-text text-muted" style="font-size: 0.72rem;">{{ __('Use 0 for "No Limit" countdown.') }}</div>
                        </div>

                        <!-- Column 4: Number of Players -->
                        <div class="col-md-6">
                            <label for="edit-players" class="form-label fw-bold">{{ __('Number of Players') }}</label>
                            <input type="number" class="form-control form-control-lg" id="edit-players" required min="1" style="border-radius: 8px; font-size: 0.9rem; height: 42px;">
                        </div>

                        <!-- Column 5: Charge Amount (Rs.) -->
                        <div class="col-md-6" id="edit-amount-section">
                            <label for="edit-amount" class="form-label fw-bold">{{ __('Charge Amount (Rs.)') }}</label>
                            <input type="number" class="form-control form-control-lg" id="edit-amount" required min="0" style="border-radius: 8px; font-size: 0.9rem; height: 42px;">
                        </div>

                        <!-- Column 6: Session Start Time -->
                        <div class="col-md-6">
                            <label for="edit-start-time" class="form-label fw-bold mb-1" style="font-size: 0.85rem; color: #64748b;">{{ __('Session Start Time (Manual)') }}</label>
                            <input type="datetime-local" class="form-control form-control-lg" id="edit-start-time" step="1" style="border-radius: 8px; font-size: 0.85rem; height: 42px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary fw-semibold" style="border-radius: 8px;" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary fw-semibold" style="border-radius: 8px;">✓ {{ __('Save Changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ==============================================
     MODAL: BEAUTIFUL BOOTSTRAP CONFIRMATION DIALOG
     ============================================== -->
<div class="modal fade" id="dynamicConfirmModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered mt-5 mt-md-4" style="max-width: 450px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold" id="confirm-modal-title">{{ __('Confirm Action') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-start gap-3">
                    <span class="fs-2" id="confirm-modal-icon">⚠️</span>
                    <div class="flex-grow-1">
                        <p class="mb-0 text-dark fw-medium" id="confirm-modal-message" style="font-size: 0.95rem;"></p>
                        
                        <!-- Dynamic custom amount input field for stop-zero workflow -->
                        <div class="mt-3 d-none" id="confirm-amount-field">
                            <label for="confirm-custom-amount" class="form-label fw-bold text-dark mb-1" style="font-size: 0.85rem;">{{ __('Enter Charge Amount (Rs.)') }}</label>
                            <input type="number" class="form-control border-2 border-primary" id="confirm-custom-amount" min="0" value="0" style="border-radius: 8px;">
                            <div class="form-text text-muted" style="font-size: 0.75rem;">{{ __('This play session currently has a Rs. 0 charge amount. Please enter the final amount.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top-0 py-2.5">
                <button type="button" class="btn btn-sm fw-semibold px-3 py-1.5" style="border-radius: 8px;" id="confirm-modal-action-btn"></button>
            </div>
        </div>
    </div>
</div>


<!-- ==============================================
     MODAL: ADD TRANSACTION (Quick transaction entry)
     ============================================== -->
<div class="modal fade" id="addTransactionModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg mt-5 mt-md-4">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header text-white py-3" style="background-color: #6366f1; border-radius: 16px 16px 0 0;">
                <h5 class="modal-title fw-bold">💳 {{ __('Add Transaction') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Total display -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">{{ __('Transaction Rows') }}</h6>
                    <div class="fw-bold" style="font-size: 0.9rem; color: #6366f1;">
                        {{ __('Total') }}: Rs. <span id="txn-total-display">0.00</span>
                    </div>
                </div>

                <!-- Repeater container -->
                <div id="txn-repeater-container"></div>

                <!-- Add Row button -->
                <div class="mb-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold" id="txn-add-row-btn" style="border-radius: 8px;">
                        ＋ {{ __('Add Row') }}
                    </button>
                </div>

                <hr class="text-muted my-3">

                <!-- Date & Action Buttons Row -->
                <div class="row align-items-end mb-2">
                    <!-- Left Half: Date (BS) -->
                    <div class="col-md-6 col-12 mb-3 mb-md-0">
                        <label for="txn-date-bs" class="form-label fw-bold mb-1" style="font-size: 0.85rem;">{{ __('Date (BS)') }}</label>
                        <input type="text" class="form-control nepali-datepicker default-today-date" id="txn-date-bs" name="date_bs"
                               placeholder="YYYY-MM-DD" autocomplete="off"
                               style="border-radius: 8px; font-size: 0.95rem; height: 38px;" required>
                    </div>
                    <!-- Right Half: Action Buttons -->
                    <div class="col-md-6 col-12 d-flex justify-content-md-end justify-content-start gap-2">
                        <button type="button" class="btn btn-secondary fw-semibold px-4" style="border-radius: 8px; height: 38px;" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="button" class="btn fw-bold px-4" id="txn-submit-btn"
                                style="border-radius: 8px; background-color: #6366f1; border-color: #6366f1; color: #fff; height: 38px; font-size: 0.95rem;">
                            ✓ {{ __('Save Transaction') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================
     DASHBOARD CONTROLLER & REAL-TIME TIMER ENGINE (JS)
     ============================================== -->
<script>
    // Localization Translations Object
    const translations = {
        active: "{{ __('Active') }}",
        expired: "{{ __('Expired') }}",
        playRecordDeleteConfirm: "{{ __('Are you sure you want to permanently delete this play record? This action cannot be undone.') }}",
        playSessionStopConfirm: "{{ __('Are you sure you want to stop this play session? Actual playing time will be logged and the session will close.') }}",
        playerCountText: "{{ __('No of Player') }}",
        limitText: "{{ __('Time') }}",
        startText: "{{ __('Start') }}",
        stopText: "{{ __('Stop') }}",
        editText: "{{ __('Edit') }}",
        deleteText: "{{ __('Delete') }}",
        minutesText: "{{ __('Minutes') }}",
        countDownActive: "{{ __('COUNTDOWN ACTIVE') }}",
        countUpNoLimit: "{{ __('COUNT UP (NO LIMIT)') }}",
        notStarted: "{{ __('NOT STARTED') }}",
        overTimeExpiredAlert: "{{ __('OVER TIME EXPIRED!') }}",
        darkMode: "{{ __('Dark Mode') }}",
        lightMode: "{{ __('Light Mode') }}"
    };

    // State Variables
    let activeRecords = [];
    let serverTimeOffset = 0; // Milliseconds offset between server time and local client time
    let timerInterval = null;
    const spokenRecords = new Set(); // To prevent repeating Speech Synthesis announcements
    const prefetchedAudio = new Map(); // Pre-buffered audio keyed by tokenName

    const defaultGameType = @json($defaultGameType);

    // Add Play Modal Stepper States
    let addStep = 1;
    let addData = {
        name: '',
        player_type: 'normal',
        player_package_id: null,
        token_id: null,
        token_name: '',
        game_type_id: defaultGameType ? defaultGameType.id : null,
        game_name: defaultGameType ? defaultGameType.game_name : '',
        default_time: null, // minutes
        default_time_label: '',
        no_of_players: 1,
        amount: 0,
        payment_type_id: {{ $defaultPaymentType ? $defaultPaymentType->id : 'null' }}
    };

    // Package Play Modal Stepper States
    let pkgStep = 1;
    let pkgData = {
        player_type: 'package',
        player_package_id: null,
        player_name: '',
        token_id: null,
        token_name: '',
        game_type_id: null,
        game_name: '',
        default_time: null, // minutes
        default_time_label: '',
        no_of_players: 1,
        amount: 0,
        payment_type_id: {{ $defaultPaymentType ? $defaultPaymentType->id : 'null' }}
    };

    // Master List of Tokens (available from PHP variable)
    const masterTokens = @json($tokens);

    // Document Ready Initializer
    document.addEventListener('DOMContentLoaded', function() {
        // Run initial fetch
        refreshSessionList();
        
        // Start high-precision 1-second dynamic countdown interval
        timerInterval = setInterval(updateTimers, 1000);

        // Auto-focus player name when Add Play modal opens
        $('#addPlayModal').on('shown.bs.modal', function () {
            $('#add-player-name').focus();
        });

        // Reset Add Play modal steps on closing
        $('#addPlayModal').on('hidden.bs.modal', function () {
            resetAddModal();
        });

        // Reset Package Play modal steps on closing
        $('#packagePlayModal').on('hidden.bs.modal', function () {
            resetPkgModal();
        });
    });



    // Helper: Show Beautiful Bootstrap Confirmation Modal
    function showConfirmModal(options) {
        const modal = $('#dynamicConfirmModal');
        const modalHeader = modal.find('.modal-header');
        const modalContent = modal.find('.modal-content');
        const closeBtn = modalHeader.find('.btn-close');

        // Dynamic theme styling
        if (options.theme === 'yellow') {
            if ($('body').hasClass('dark-mode')) {
                modalHeader.attr('style', 'background-color: #d97706 !important; color: #ffffff !important;');
                modalContent.attr('style', 'border-radius: 16px; background-color: #1e1912 !important; border: 2px solid #f59e0b !important;');
                closeBtn.addClass('btn-close-white');
            } else {
                modalHeader.attr('style', 'background-color: #f59e0b !important; color: #000000 !important;');
                modalContent.attr('style', 'border-radius: 16px; background-color: #fffbeb !important; border: 2px solid #f59e0b !important;');
                closeBtn.removeClass('btn-close-white');
            }
        } else {
            modalHeader.attr('style', '');
            modalHeader.addClass('bg-dark text-white');
            modalContent.attr('style', 'border-radius: 16px;');
            closeBtn.addClass('btn-close-white');
        }
        
        // Set dynamic texts & icons
        $('#confirm-modal-title').text(options.title || "{{ __('Confirm Action') }}");
        $('#confirm-modal-message').text(options.message || '');
        $('#confirm-modal-icon').text(options.icon || '⚠️');
        
        // Configure action button
        const actionBtn = $('#confirm-modal-action-btn');
        actionBtn.text(options.confirmText || "{{ __('Confirm') }}");
        
        // Apply class (e.g. btn-danger, btn-warning, btn-success)
        actionBtn.removeClass('btn-danger btn-warning btn-success btn-primary btn-secondary')
                 .addClass(options.confirmClass || 'btn-danger');

        // Capture elements for amount input
        const amountField = $('#confirm-amount-field');
        const amountInput = $('#confirm-custom-amount');

        if (options.showAmountField) {
            amountField.removeClass('d-none');
            amountInput.val(options.defaultAmount || 0);
            
            // Auto focus and select the input
            modal.one('shown.bs.modal', function() {
                amountInput.focus().select();
            });
        } else {
            amountField.addClass('d-none');
            amountInput.val(0);
        }

        // Unbind any existing click events on the action button
        actionBtn.off('click');

        // Bind new click handler
        actionBtn.on('click', function() {
            let customAmount = null;
            if (options.showAmountField) {
                const enteredVal = parseInt(amountInput.val());
                if (isNaN(enteredVal) || enteredVal < 0) {
                    alert("{{ __('Please enter a valid amount.') }}");
                    return;
                }
                customAmount = enteredVal;
            }

            modal.modal('hide');
            
            if (typeof options.onConfirm === 'function') {
                options.onConfirm(customAmount);
            }
        });

        // Show the modal
        modal.modal('show');
    }

    // Helper: Speak overtime warning
    // Tier 1: Native Nepali voice (Edge with Hemanta/Swaroop)
    // Tier 2: Google Translate TTS audio fetch (Chrome — real human Nepali sound)
    // Tier 3: Best English Web Speech voice (universal fallback)

    // Build the spoken label for a record:
    //   • Pure number token  → "टोकन नम्बर 3"  (prefix added)
    //   • Non-numeric token  → "SB-01" or "Sandip" (just the value, no prefix)
    //   • No token, has name → player name (no prefix)
    function buildTtsLabel(record) {
        // Resolve player name if present (either custom name or package player name)
        let displayName = '';
        if (record.name) {
            displayName = record.name;
        } else if (record.player_package && record.player_package.player) {
            displayName = record.player_package.player.name;
        }

        if (displayName) {
            // When reading player name, do NOT say "टोकन नम्बर" / "Token" before the name
            return { label: displayName, prefix: false };
        }

        // Fallback to token if name is null
        const tokenName = (record.token && record.token.name) ? record.token.name : null;
        if (!tokenName) return null;

        // If the token is purely numeric, add the Nepali/English prefix
        if (/^\d+$/.test(String(tokenName).trim())) {
            return { label: tokenName, prefix: true };
        }
        return { label: tokenName, prefix: false };
    }

    function speakOvertimeForRecord(id) {
        const record = activeRecords.find(r => r.id === id);
        if (record) {
            const ttsInfo = buildTtsLabel(record);
            if (ttsInfo) speakOvertime(ttsInfo);
        }
    }

    function speakOvertime(ttsInfo) {
        // ttsInfo can be { label, prefix } from buildTtsLabel, or a plain string (legacy)
        if (typeof ttsInfo === 'string') {
            ttsInfo = /^\d+$/.test(ttsInfo.trim())
                ? { label: ttsInfo, prefix: true }
                : { label: ttsInfo, prefix: false };
        }
        if (!ttsInfo) return;

        const tokenName = ttsInfo.label;
        const spoken   = ttsInfo.prefix ? "टोकन नम्बर " + ttsInfo.label : ttsInfo.label;
        const nepaliPhrase1 = spoken + ", तपाईंको समय सकियो।";
        const nepaliPhrase2 = "फेरि सुन्नुहोस्, " + spoken + ", तपाईंको समय सकियो।";
        const nepaliText    = nepaliPhrase1 + " " + nepaliPhrase2;
        const cacheKey      = ttsInfo.label;

        // ── Tier 2: Server-side TTS proxy (avoids CORS in Chrome) ────────────
        // Plays from pre-fetched cache if available, otherwise fetches now
        function speakViaGoogleTranslate() {
            const cached = prefetchedAudio.get(cacheKey);
            if (cached && cached.audio1 && cached.audio2) {
                // Instant play from pre-buffered audio — no network wait
                cached.audio1.currentTime = 0;
                cached.audio1.play().then(() => {
                    cached.audio1.addEventListener('ended', function onEnd() {
                        cached.audio1.removeEventListener('ended', onEnd);
                        setTimeout(() => cached.audio2.play().catch(e => console.warn('TTS part 2 failed:', e)), 400);
                    });
                }).catch(err => {
                    console.warn("Pre-fetched TTS play failed, falling back to English:", err);
                    speakEnglishFallback();
                });
                return;
            }

            // Not pre-fetched — fetch now (slight delay expected)
            const proxyBase = "{{ route('skatepark.api.tts') }}";
            const url1 = proxyBase + "?lang=ne&text=" + encodeURIComponent(nepaliPhrase1);
            const url2 = proxyBase + "?lang=ne&text=" + encodeURIComponent(nepaliPhrase2);

            const audio1 = new Audio(url1);
            const audio2 = new Audio(url2);
            audio1.volume = 1.0;
            audio2.volume = 1.0;

            audio1.play().then(() => {
                audio1.addEventListener('ended', () => {
                    setTimeout(() => audio2.play().catch(e => console.warn('TTS part 2 failed:', e)), 400);
                });
            }).catch(err => {
                console.warn("Nepali TTS proxy failed, falling back to English speech:", err);
                speakEnglishFallback();
            });
        }

        // ── Tier 3: Best English Web Speech voice (universal fallback) ────────
        function speakEnglishFallback() {
            if (!('speechSynthesis' in window)) return;
            window.speechSynthesis.cancel();

            const englishSpoken = ttsInfo.prefix ? "Token " + tokenName : tokenName;
            const englishText = englishSpoken + " — your time is over. I repeat — " + englishSpoken + " — your time is over.";
            const utterance = new SpeechSynthesisUtterance(englishText);

            const voices = window.speechSynthesis.getVoices();
            const preferredEnglish = [
                'Google UK English Female', 'Google UK English Male', 'Google US English',
                'Microsoft Aria Online (Natural) - English (United States)',
                'Microsoft Jenny Online (Natural) - English (United States)',
                'Microsoft Zira - English (United States)',
                'Microsoft David - English (United States)',
                'Samantha', 'Karen', 'Daniel', 'Alex',
            ];

            let selectedVoice = null;
            for (const name of preferredEnglish) {
                const found = voices.find(v => v.name === name);
                if (found) { selectedVoice = found; break; }
            }
            if (!selectedVoice) {
                selectedVoice = voices.find(v =>
                    v.lang.startsWith('en') &&
                    !v.name.toLowerCase().includes('espeak') &&
                    !v.name.toLowerCase().includes('festival')
                ) || null;
            }

            if (selectedVoice) utterance.voice = selectedVoice;
            utterance.rate   = 0.88;
            utterance.pitch  = 1.05;
            utterance.volume = 1.0;
            window.speechSynthesis.speak(utterance);
        }

        // ── Tier 1: Try native Nepali Web Speech voice (Edge) ────────────────
        function tryNativeNepali() {
            if (!('speechSynthesis' in window)) {
                speakViaGoogleTranslate();
                return;
            }

            const voices = window.speechSynthesis.getVoices();
            const nepaliVoiceNames = [
                'Microsoft Hemanta Online (Natural) - Nepali (Nepal)',
                'Microsoft Swaroop Online (Natural) - Nepali (Nepal)',
                'Microsoft Hemanta - Nepali (Nepal)',
                'Microsoft Swaroop - Nepali (Nepal)',
            ];

            let nepaliVoice = null;
            for (const name of nepaliVoiceNames) {
                const found = voices.find(v => v.name === name);
                if (found) { nepaliVoice = found; break; }
            }
            if (!nepaliVoice) {
                nepaliVoice = voices.find(v => v.lang === 'ne-NP' || v.lang === 'ne') || null;
            }

            if (nepaliVoice) {
                // Edge: use native Nepali neural voice
                window.speechSynthesis.cancel();
                const utterance = new SpeechSynthesisUtterance(nepaliText);
                utterance.voice  = nepaliVoice;
                utterance.lang   = 'ne-NP';
                utterance.rate   = 0.85;
                utterance.pitch  = 1.05;
                utterance.volume = 1.0;
                window.speechSynthesis.speak(utterance);
            } else {
                // Chrome: no native Nepali voice — use Google Translate audio
                speakViaGoogleTranslate();
            }
        }

        // Wait for voices to load if needed, then run
        if ('speechSynthesis' in window) {
            const voices = window.speechSynthesis.getVoices();
            if (voices.length > 0) {
                tryNativeNepali();
            } else {
                window.speechSynthesis.addEventListener('voiceschanged', function handler() {
                    window.speechSynthesis.removeEventListener('voiceschanged', handler);
                    tryNativeNepali();
                });
                // If voices never load (e.g. unsupported), go straight to Google TTS
                setTimeout(() => {
                    if (window.speechSynthesis.getVoices().length === 0) {
                        speakViaGoogleTranslate();
                    }
                }, 1500);
            }
        } else {
            speakViaGoogleTranslate();
        }
    }

    // Refresh Active Session Data from Server
    function refreshSessionList() {
        $.ajax({
            url: "{{ route('skatepark.api.session-data') }}",
            type: "GET",
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    activeRecords = res.data;
                    
                    // Calculate offset between server clock and browser clock
                    const serverTime = new Date(res.server_time);
                    const clientTime = new Date();
                    serverTimeOffset = serverTime.getTime() - clientTime.getTime();

                    // Re-render
                    renderDashboard();
                }
            },
            error: function(err) {
                console.error("Failed to load active sessions:", err);
            }
        });
    }

    // Render the Dashboard Cards from `activeRecords` State Array
    function renderDashboard() {
        const playingList = $('#playing-list');
        const overtimeList = $('#overtime-list');
        
        // Remove prior items
        playingList.find('.skate-item').remove();
        overtimeList.find('.skate-item').remove();

        let countPlaying = 0;
        let countOvertime = 0;

        // Iterate through records and build HTML cards
        activeRecords.forEach(function(record) {
            const isOvertime = checkRecordOvertime(record);
            
            // If the record is already in overtime when rendering, mark it as spoken
            // to prevent triggering the voice alert or dynamic movement on load/refresh.
            if (isOvertime) {
                spokenRecords.add(record.id);
            }

            const cardHtml = buildCardHtml(record, isOvertime);

            if (isOvertime) {
                overtimeList.append(cardHtml);
                countOvertime++;
            } else {
                playingList.append(cardHtml);
                countPlaying++;
            }
        });

        // Toggle Placeholders
        if (countPlaying > 0) {
            $('#playing-placeholder').addClass('d-none');
        } else {
            $('#playing-placeholder').removeClass('d-none');
        }

        if (countOvertime > 0) {
            $('#overtime-placeholder').addClass('d-none');
        } else {
            $('#overtime-placeholder').removeClass('d-none');
        }

        // Update Statistics Badges
        $('#count-playing').text(countPlaying + " " + translations.active);
        $('#count-overtime').text(countOvertime + " " + translations.expired);
        $('#stat-active').text(countPlaying);
        $('#stat-overtime').text(countOvertime);
        $('#stat-total').text(activeRecords.length);

        // Filter out tokens currently in use from the normal play modal
        filterAddPlayTokens();

        // Run immediate timer calculation
        updateTimers();
    }

    // Helper: Filter out active tokens that are currently in use
    function filterAddPlayTokens() {
        const usedTokenIds = new Set(activeRecords.map(r => r.token_id));
        let visibleCount = 0;
        
        $('.token-selector-col').each(function() {
            const tokenId = parseInt($(this).data('token-id'));
            if (usedTokenIds.has(tokenId)) {
                $(this).addClass('d-none');
            } else {
                $(this).removeClass('d-none');
                visibleCount++;
            }
        });

        if (visibleCount === 0 && masterTokens.length > 0) {
            $('#add-no-tokens-alert').removeClass('d-none');
        } else {
            $('#add-no-tokens-alert').addClass('d-none');
        }
    }

    // Helper: Determine if a specific record is in overtime
    function checkRecordOvertime(record) {
        if (!record.start_time || !record.default_time) {
            return false; // Not started or No Limit
        }

        const now = getSyncedCurrentTime();
        const start = new Date(record.start_time);
        const elapsedSecs = Math.floor((now.getTime() - start.getTime()) / 1000);
        const allowedSecs = record.default_time * 60;
        
        return (allowedSecs - elapsedSecs) <= 0;
    }

    // Fetch the client-synced server time
    function getSyncedCurrentTime() {
        const localTime = new Date();
        return new Date(localTime.getTime() + serverTimeOffset);
    }

    // Generate Card Template HTML
    function buildCardHtml(record, isOvertime) {
        const tokenName = record.token ? record.token.name : 'Unknown';

        let startTimeHtml = '';
        let bigActionBtnHtml = '';
        let showEdit = true;

        if (record.start_time) {
            const startDate = new Date(record.start_time);
            let hours = startDate.getHours();
            const minutes = String(startDate.getMinutes()).padStart(2, '0');
            hours = hours % 12;
            hours = hours ? hours : 12;
            startTimeHtml = `<span class="ms-2 text-secondary font-monospace" style="font-size: 0.82rem; font-weight: 500; letter-spacing: 0;">(${hours}:${minutes})</span>`;

            // Calculate elapsed time on initial load
            const now = getSyncedCurrentTime();
            const elapsedSecs = Math.floor((now.getTime() - startDate.getTime()) / 1000);
            if (elapsedSecs > 300) {
                showEdit = false;
            }

            // Big Stop button covering both rows, plus Speaker button if overtime
            bigActionBtnHtml = `
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-light border-0 rounded-circle speaker-btn ${isOvertime ? '' : 'd-none'}" onclick="speakOvertimeForRecord(${record.id})" title="Replay Alert" style="width: 28px; height: 28px; padding: 0; line-height: 28px; font-size: 0.9rem; vertical-align: middle;">🔊</button>
                    <button class="btn btn-danger fw-bold d-flex align-items-center justify-content-center" onclick="stopTimer(${record.id})" style="font-size: 0.85rem; border-radius: 6px; height: 36px; width: 58px; padding: 0;">${translations.stopText}</button>
                </div>
            `;
        } else {
            // Not started state: big Start button covering both rows
            startTimeHtml = '';
            bigActionBtnHtml = `
                <button class="btn btn-success fw-bold d-flex align-items-center justify-content-center" onclick="startTimer(${record.id})" style="font-size: 0.85rem; border-radius: 6px; height: 36px; width: 58px; padding: 0;">${translations.startText}</button>
            `;
        }

        let displayName = '';
        if (record.name) {
            displayName = record.name;
        } else if (record.player_package && record.player_package.player) {
            displayName = record.player_package.player.name;
        }

        const badgeText = displayName ? displayName : tokenName;

        // Calculate initial timer display for not-started sessions based on pre-allocated timeframe
        let initialTimerText = '00:00:00';
        if (!record.start_time && record.default_time) {
            initialTimerText = formatSeconds(record.default_time * 60);
        }

        const isPaymentAlert = record.payment_type && (record.payment_type.is_alert || record.payment_type.is_alert === 1 || record.payment_type.is_alert === true);
        const alertClass = isPaymentAlert ? 'payment-alert-bg' : '';
        const paymentLabel = (isPaymentAlert ? '⚠️ ' : '') + (record.payment_type ? record.payment_type.name : 'Unknown');

        // Build Edit/Delete vertical stack HTML
        let editDeleteStackHtml = '';
        if (showEdit) {
            editDeleteStackHtml = `
                <div class="d-flex flex-column edit-delete-stack-${record.id}" style="width: 68px; gap: 4px;">
                    <button class="btn btn-outline-warning btn-sm fw-bold d-flex align-items-center justify-content-center" onclick="openEditModal(${record.id})" style="font-size: 0.75rem; border-radius: 6px; height: 26px; padding: 0; line-height: 1;">${translations.editText}</button>
                    <button class="btn btn-outline-danger btn-sm fw-bold d-flex align-items-center justify-content-center" onclick="deletePlayRecord(${record.id})" style="font-size: 0.75rem; border-radius: 6px; height: 26px; padding: 0; line-height: 1;">${translations.deleteText}</button>
                </div>
            `;
        } else {
            editDeleteStackHtml = `<div style="width: 68px;"></div>`;
        }

        return `
            <div class="skate-item d-flex align-items-center justify-content-between gap-1 ${alertClass}" id="card-${record.id}" data-id="${record.id}" style="min-height: 74px; padding: 0.65rem 0.25rem;">
                <!-- Left: Content Columns (Row 1 + Row 2) -->
                <div class="flex-grow-1 d-flex flex-column" style="min-width: 0;">
                    <!-- Row 1: Token, Counter -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center w-100 gap-1">
                        <!-- Left: Token / Player Name -->
                        <div class="d-flex align-items-center gap-1" style="flex-shrink: 0; min-width: 60px; max-width: 100%; word-break: break-word;">
                            <span class="badge-token text-dark" style="font-size: 1.15rem; font-weight: 800;" title="Token: ${tokenName}">${badgeText}</span>
                        </div>
                        
                        <!-- Middle: Timer Counter -->
                        <div class="d-flex flex-wrap align-items-baseline justify-content-start justify-content-sm-center flex-grow-1 mx-0 mx-sm-2" style="min-width: 0;">
                            <span class="timer-display text-dark" id="timer-${record.id}" style="font-size: 1.45rem; line-height: 1; font-weight: 800; letter-spacing: -0.5px;">${initialTimerText}</span>
                            ${startTimeHtml}
                        </div>
                    </div>

                    <!-- Row 2: Horizontal Session Details -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center pt-2 mt-2 text-secondary font-monospace gap-1" style="font-size: 0.82rem;">
                        <span><strong class="text-dark">${record.no_of_players} ${translations.playerCountText}</strong></span>
                        <span>Rs${record.amount}/${paymentLabel}</span>
                        <span>${translations.limitText}:<strong>${record.default_time ? record.default_time + 'm' : '∞'}</strong></span>
                    </div>
                </div>

                <!-- Right: Action Buttons (Edit/Delete vertical stack + Big Button) -->
                <div class="d-flex align-items-center justify-content-end gap-1 ms-1 ms-sm-2" style="flex-shrink: 0;">
                    ${editDeleteStackHtml}
                    <div class="action-btn-container-${record.id}">
                        ${bigActionBtnHtml}
                    </div>
                </div>
            </div>
        `;
    }

    // High Precision 1-Second Timer Updating Loop
    function updateTimers() {
        const now = getSyncedCurrentTime();

        activeRecords.forEach(function(record) {
            const cardEl = $(`#card-${record.id}`);
            const timerEl = $(`#timer-${record.id}`);
            
            if (!cardEl.length || !timerEl.length) return;

            // 1. Not Started State
            if (!record.start_time) {
                let initialTimerText = '00:00:00';
                if (record.default_time) {
                    initialTimerText = formatSeconds(record.default_time * 60);
                }
                timerEl.text(initialTimerText).removeClass('timer-warning-yellow');
                cardEl.removeClass('warning-yellow overtime-red');
                return;
            }

            const start = new Date(record.start_time);
            const elapsedSeconds = Math.floor((now.getTime() - start.getTime()) / 1000);

            // Dynamically hide Edit & Delete buttons after 5 minutes (300 seconds)
            if (elapsedSeconds > 300) {
                const stackCol = cardEl.find(`.edit-delete-stack-${record.id}`);
                if (stackCol.length) {
                    stackCol.empty();
                }
            }

            // 2. Count UP timer (No Limit / Custom Unlimited)
            if (!record.default_time || record.default_time === 0) {
                timerEl.text(formatSeconds(elapsedSeconds)).removeClass('timer-warning-yellow');
                cardEl.removeClass('warning-yellow overtime-red');
                return;
            }

            // 3. Count DOWN timer with Overtime transitions
            const allowedSeconds = record.default_time * 60;
            const remainingSeconds = allowedSeconds - elapsedSeconds;

            if (remainingSeconds > 0) {
                // Counting down
                timerEl.text(formatSeconds(remainingSeconds));
                
                // Yellow text threshold: Less than 5 minutes (300 seconds)
                if (remainingSeconds < 300) {
                    cardEl.removeClass('warning-yellow overtime-red');
                    timerEl.addClass('timer-warning-yellow');
                } else {
                    cardEl.removeClass('warning-yellow overtime-red');
                    timerEl.removeClass('timer-warning-yellow');
                }
                
                // Ensure speaker button is hidden
                cardEl.find('.speaker-btn').addClass('d-none');

                // Pre-fetch audio 30 seconds before expiry so it's ready instantly
                if (remainingSeconds <= 30) {
                    const ttsInfo = buildTtsLabel(record);
                    if (ttsInfo && !prefetchedAudio.has(ttsInfo.label)) {
                        prefetchedAudio.set(ttsInfo.label, { audio1: null, audio2: null });
                        const proxyBase = "{{ route('skatepark.api.tts') }}";
                        const spoken = ttsInfo.prefix ? "टोकन नम्बर " + ttsInfo.label : ttsInfo.label;
                        const p1 = spoken + ", तपाईंको समय सकियो।";
                        const p2 = "फेरि सुन्नुहोस्, " + spoken + ", तपाईंको समय सकियो।";
                        const a1 = new Audio(proxyBase + "?lang=ne&text=" + encodeURIComponent(p1));
                        const a2 = new Audio(proxyBase + "?lang=ne&text=" + encodeURIComponent(p2));
                        a1.preload = 'auto'; a2.preload = 'auto';
                        a1.volume  = 1.0;   a2.volume  = 1.0;
                        a1.load(); a2.load();
                        prefetchedAudio.set(ttsInfo.label, { audio1: a1, audio2: a2 });
                    }
                }
            } else {
                // Overtime has occurred!
                const overtimeSeconds = Math.abs(remainingSeconds);
                timerEl.text(formatSeconds(overtimeSeconds)).removeClass('timer-warning-yellow');

                // Apply pulsating red background and display speaker button
                cardEl.removeClass('warning-yellow').addClass('overtime-red');
                cardEl.find('.speaker-btn').removeClass('d-none');

                // Trigger Text-to-Speech Warning ONCE
                if (!spokenRecords.has(record.id)) {
                    spokenRecords.add(record.id);
                    const ttsInfo = buildTtsLabel(record);
                    if (ttsInfo) speakOvertime(ttsInfo);
                    
                    // Dynamically move card container from playing column to overtime column
                    setTimeout(function() {
                        const cardClone = cardEl.detach();
                        $('#overtime-placeholder').addClass('d-none');
                        $('#overtime-list').append(cardClone);
                        
                        // Recalculate column counters
                        let countPlaying = $('#playing-list').find('.skate-item').length;
                        let countOvertime = $('#overtime-list').find('.skate-item').length;
                        $('#count-playing').text(countPlaying + " " + translations.active);
                        $('#count-overtime').text(countOvertime + " " + translations.expired);
                        $('#stat-active').text(countPlaying);
                        $('#stat-overtime').text(countOvertime);

                        if (countPlaying === 0) {
                            $('#playing-placeholder').removeClass('d-none');
                        }
                    }, 500);
                }
            }
        });
    }

    // Formatter: Convert seconds to HH:MM:SS format
    function formatSeconds(totalSeconds) {
        const hrs = Math.floor(totalSeconds / 3600);
        const mins = Math.floor((totalSeconds % 3600) / 60);
        const secs = totalSeconds % 60;
        
        const pad = (num) => String(num).padStart(2, '0');
        return `${pad(hrs)}:${pad(mins)}:${pad(secs)}`;
    }


    // ==============================================
    // AJAX ACTIONS: START / STOP SESSION
    // ==============================================
    function startTimer(recordId) {
        $.ajax({
            url: `/skatepark/api/play-records/${recordId}/start`,
            type: "POST",
            data: { _token: "{{ csrf_token() }}" },
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    refreshSessionList();
                }
            },
            error: function(err) {
                alert(err.responseJSON ? err.responseJSON.message : "Error starting session");
            }
        });
    }

    function stopTimer(recordId) {
        const record = activeRecords.find(r => r.id === parseInt(recordId));
        if (!record) return;

        const isPaymentAlert = record.payment_type && (record.payment_type.is_alert || record.payment_type.is_alert === 1 || record.payment_type.is_alert === true);

        function proceedToStopModal() {
            const isAmountZero = (parseInt(record.amount) === 0);

            showConfirmModal({
                title: "{{ __('Stop Session') }}",
                message: translations.playSessionStopConfirm,
                icon: '🛑',
                confirmText: "{{ __('Stop') }}",
                confirmClass: 'btn-danger',
                showAmountField: isAmountZero,
                defaultAmount: 0,
                onConfirm: function(enteredAmount) {
                    // Optimistic UI: Immediately fade out and remove card from DOM
                    const cardEl = $(`#card-${recordId}`);
                    cardEl.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Recalculate column counters immediately
                        let countPlaying = $('#playing-list').find('.skate-item').length;
                        let countOvertime = $('#overtime-list').find('.skate-item').length;
                        $('#count-playing').text(countPlaying + " " + translations.active);
                        $('#count-overtime').text(countOvertime + " " + translations.expired);
                        $('#stat-active').text(countPlaying);
                        $('#stat-overtime').text(countOvertime);
                        
                        if (countPlaying === 0) $('#playing-placeholder').removeClass('d-none');
                        if (countOvertime === 0) $('#overtime-placeholder').removeClass('d-none');
                    });

                    const postData = { _token: "{{ csrf_token() }}" };
                    if (enteredAmount !== null) {
                        postData.amount = enteredAmount;
                    }

                    $.ajax({
                        url: `/skatepark/api/play-records/${recordId}/stop`,
                        type: "POST",
                        data: postData,
                        dataType: "json",
                        success: function(res) {
                            if (res.success) {
                                // Update local activeRecords array state immediately
                                activeRecords = activeRecords.filter(r => r.id !== parseInt(recordId));
                                refreshSessionList();
                            }
                        },
                        error: function(err) {
                            alert(err.responseJSON ? err.responseJSON.message : "Error stopping session");
                            // If the AJAX call fails, refresh the list to restore the card
                            refreshSessionList();
                        }
                    });
                }
            });
        }

        if (isPaymentAlert) {
            const paymentName = record.payment_type ? record.payment_type.name : '';
            showConfirmModal({
                title: "{{ __('Unpaid / Alert Payment Check') }}",
                message: `Rs. ${record.amount} {{ __('is unpaid') }} (${paymentName}). {{ __('Did they pay?') }}`,
                icon: '⚠️',
                theme: 'yellow',
                confirmText: "{{ __('Yes, Paid') }}",
                confirmClass: 'btn-warning text-dark fw-bold',
                showAmountField: false,
                onConfirm: function() {
                    setTimeout(proceedToStopModal, 300);
                }
            });
        } else {
            proceedToStopModal();
        }
    }

    function deletePlayRecord(recordId) {
        showConfirmModal({
            title: "{{ __('Delete Record') }}",
            message: translations.playRecordDeleteConfirm,
            icon: '🗑️',
            confirmText: "{{ __('Delete') }}",
            confirmClass: 'btn-danger',
            showAmountField: false,
            onConfirm: function() {
                // Optimistic UI: Immediately fade out and remove card from DOM
                const cardEl = $(`#card-${recordId}`);
                cardEl.fadeOut(300, function() {
                    $(this).remove();
                    
                    // Recalculate column counters immediately
                    let countPlaying = $('#playing-list').find('.skate-item').length;
                    let countOvertime = $('#overtime-list').find('.skate-item').length;
                    $('#count-playing').text(countPlaying + " " + translations.active);
                    $('#count-overtime').text(countOvertime + " " + translations.expired);
                    $('#stat-active').text(countPlaying);
                    $('#stat-overtime').text(countOvertime);
                    
                    if (countPlaying === 0) $('#playing-placeholder').removeClass('d-none');
                    if (countOvertime === 0) $('#overtime-placeholder').removeClass('d-none');
                });

                $.ajax({
                    url: `/skatepark/api/play-records/${recordId}/delete`,
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}" },
                    dataType: "json",
                    success: function(res) {
                        if (res.success) {
                            activeRecords = activeRecords.filter(r => r.id !== parseInt(recordId));
                            refreshSessionList();
                        }
                    },
                    error: function(err) {
                        alert(err.responseJSON ? err.responseJSON.message : "Error deleting record");
                        refreshSessionList();
                    }
                });
            }
        });
    }


    // ==============================================
    // MULTI-STEP MODAL: ADD PLAY ENGINE
    // ==============================================
    function selectToken(id, name, gameTypeId, gameName) {
        addData.token_id = id;
        addData.token_name = name;
        addData.game_type_id = gameTypeId;
        addData.game_name = gameName;
        // When selecting a token, player name is null
        addData.name = null;

        // Progress to Step 2
        addStep = 2;
        $('#add-step-1').addClass('d-none');
        $('#add-step-2').removeClass('d-none');
        $('#step-ind-1').removeClass('text-primary border-primary border-3 fw-bold').addClass('text-muted fw-semibold');
        $('#step-ind-2').addClass('text-primary border-primary border-3 fw-bold').removeClass('text-muted fw-semibold');
        $('#btn-add-back').removeAttr('disabled');
    }

    function submitPlayerNameAndProceed() {
        const playerName = $('#add-player-name').val().trim();
        if (!playerName) {
            $('#add-player-name').focus();
            $('#add-player-name').addClass('is-invalid');
            setTimeout(() => $('#add-player-name').removeClass('is-invalid'), 2000);
            return;
        }
        // When proceeding by name, token is null
        addData.name = playerName;
        addData.token_id = null;
        addData.token_name = null;
        if (defaultGameType) {
            addData.game_type_id = defaultGameType.id;
            addData.game_name = defaultGameType.game_name;
        } else {
            addData.game_type_id = null;
            addData.game_name = '';
        }

        // Progress to Step 2
        addStep = 2;
        $('#add-step-1').addClass('d-none');
        $('#add-step-2').removeClass('d-none');
        $('#step-ind-1').removeClass('text-primary border-primary border-3 fw-bold').addClass('text-muted fw-semibold');
        $('#step-ind-2').addClass('text-primary border-primary border-3 fw-bold').removeClass('text-muted fw-semibold');
        $('#btn-add-back').removeAttr('disabled');
    }

    function selectDuration(minutes, label, defaultTimeId = null) {
        addData.default_time = minutes;
        addData.default_time_label = label;
        addData.default_time_id = defaultTimeId;

        // Progress to Step 3
        addStep = 3;
        $('#add-step-2').addClass('d-none');
        $('#add-step-3').removeClass('d-none');
        $('#step-ind-2').removeClass('text-primary border-primary border-3 fw-bold').addClass('text-muted fw-semibold');
        $('#step-ind-3').addClass('text-primary border-primary border-3 fw-bold').removeClass('text-muted fw-semibold');
    }

    function submitCustomDuration() {
        const val = parseInt($('#custom-minutes').val());
        if (isNaN(val) || val < 1) {
            alert("Please enter a valid number of minutes.");
            return;
        }
        selectDuration(val, val + " Min");
    }

    function selectPlayerCount(count) {
        addData.no_of_players = count;
        proceedToSummary();
    }

    function submitCustomPlayers() {
        const val = parseInt($('#custom-players').val());
        if (isNaN(val) || val < 1) {
            alert("Please enter a valid player count.");
            return;
        }
        selectPlayerCount(val);
    }

    function proceedToSummary() {
        // Renders details to summary step
        // Show player name (name-path) or token name (token-path) in summary
        const tokenLabel = addData.token_name ? addData.token_name : (addData.name ? '👤 ' + addData.name : '-');
        $('#summary-token').text(tokenLabel);
        $('#summary-duration').text(addData.default_time === 0 ? "{{ __('Unlimited (Count Up)') }}" : addData.default_time + ' ' + "{{ __('Minutes') }}");
        $('#summary-players').text(addData.no_of_players + ' ' + "{{ __('Players') }}");

        // AJAX Lookup for Pricing Amount
        $.ajax({
            url: "{{ route('skatepark.api.lookup-rate') }}",
            type: "GET",
            data: {
                game_type_id: addData.game_type_id,
                default_time_id: addData.default_time_id
            },
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    const baseRate = res.rate;
                    addData.amount = baseRate * addData.no_of_players;
                    $('#play-amount').val(addData.amount);
                }
            },
            error: function() {
                // Fallback to 0 if lookup fails
                $('#play-amount').val(0);
            }
        });

        // Progress to Step 4
        addStep = 4;
        $('#add-step-3').addClass('d-none');
        $('#add-step-4').removeClass('d-none');
        $('#step-ind-3').removeClass('text-primary border-primary border-3 fw-bold').addClass('text-muted fw-semibold');
        $('#step-ind-4').addClass('text-primary border-primary border-3 fw-bold').removeClass('text-muted fw-semibold');
        $('#btn-add-submit').removeClass('d-none');
    }

    function navigateAddBack() {
        if (addStep === 2) {
            addStep = 1;
            $('#add-step-2').addClass('d-none');
            $('#add-step-1').removeClass('d-none');
            $('#step-ind-2').removeClass('text-primary border-primary border-3 fw-bold').addClass('text-muted fw-semibold');
            $('#step-ind-1').addClass('text-primary border-primary border-3 fw-bold').removeClass('text-muted fw-semibold');
            $('#btn-add-back').attr('disabled', 'disabled');
        } else if (addStep === 3) {
            addStep = 2;
            $('#add-step-3').addClass('d-none');
            $('#add-step-2').removeClass('d-none');
            $('#step-ind-3').removeClass('text-primary border-primary border-3 fw-bold').addClass('text-muted fw-semibold');
            $('#step-ind-2').addClass('text-primary border-primary border-3 fw-bold').removeClass('text-muted fw-semibold');
        } else if (addStep === 4) {
            addStep = 3;
            $('#add-step-4').addClass('d-none');
            $('#add-step-3').removeClass('d-none');
            $('#step-ind-4').removeClass('text-primary border-primary border-3 fw-bold').addClass('text-muted fw-semibold');
            $('#step-ind-3').addClass('text-primary border-primary border-3 fw-bold').removeClass('text-muted fw-semibold');
            $('#btn-add-submit').addClass('d-none');
        }
    }

    function submitPlayRecord() {
        // Collect custom overridden amount & payment (name was captured in Step 1)
        // addData.name is already set in Step 1 (either from name-input or null from token selection)
        addData.amount = parseInt($('#play-amount').val()) || 0;
        addData.payment_type_id = $('#play-payment').val();

        $.ajax({
            url: "{{ route('skatepark.api.store-play-record') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                name: addData.name,
                player_type: addData.player_type,
                player_package_id: addData.player_package_id,
                token_id: addData.token_id,
                game_type_id: addData.game_type_id,
                default_time: addData.default_time,
                no_of_players: addData.no_of_players,
                amount: addData.amount,
                payment_type_id: addData.payment_type_id
            },
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    $('#addPlayModal').modal('hide');
                    resetAddModal();
                    refreshSessionList();
                }
            },
            error: function(err) {
                alert(err.responseJSON ? err.responseJSON.message : "Failed to store session.");
            }
        });
    }

    function resetAddModal() {
        addStep = 1;
        $('#add-step-1').removeClass('d-none');
        $('#add-step-2').addClass('d-none');
        $('#add-step-3').addClass('d-none');
        $('#add-step-4').addClass('d-none');
        
        $('#step-ind-1').addClass('text-primary border-primary border-3 fw-bold').removeClass('text-muted fw-semibold');
        $('#step-ind-2').removeClass('text-primary border-primary border-3 fw-bold').addClass('text-muted fw-semibold');
        $('#step-ind-3').removeClass('text-primary border-primary border-3 fw-bold').addClass('text-muted fw-semibold');
        $('#step-ind-4').removeClass('text-primary border-primary border-3 fw-bold').addClass('text-muted fw-semibold');
        
        $('#btn-add-back').attr('disabled', 'disabled');
        $('#btn-add-submit').addClass('d-none');

        $('#custom-minutes').val('');
        $('#custom-players').val('1');
        $('#add-player-name').val('').removeClass('is-invalid');
        
        addData = {
            name: '',
            player_type: 'normal',
            player_package_id: null,
            token_id: null,
            token_name: '',
            game_type_id: defaultGameType ? defaultGameType.id : null,
            game_name: defaultGameType ? defaultGameType.game_name : '',
            default_time: null,
            default_time_label: '',
            no_of_players: 1,
            amount: 0,
            payment_type_id: {{ $defaultPaymentType ? $defaultPaymentType->id : 'null' }}
        };
    }


    // ==============================================
    // MULTI-STEP MODAL: PACKAGE PLAY ENGINE
    // ==============================================
    function selectPlayerPackage(id, playerName, gameTypeId, gameName) {
        pkgData.player_package_id = id;
        pkgData.player_name = playerName;
        pkgData.game_type_id = gameTypeId;
        pkgData.game_name = gameName;
        pkgData.token_id = null;
        pkgData.token_name = '';

        // Progress directly to Step 2 (Duration)
        pkgStep = 2;
        $('#pkg-step-1').addClass('d-none');
        $('#pkg-step-2').removeClass('d-none');
        $('#pkg-step-ind-1').removeClass('text-success border-success border-3 fw-bold').addClass('text-muted fw-semibold');
        $('#pkg-step-ind-2').addClass('text-success border-success border-3 fw-bold').removeClass('text-muted fw-semibold');
        $('#btn-pkg-back').removeAttr('disabled');
    }

    function selectPkgDuration(minutes, label) {
        pkgData.default_time = minutes;
        pkgData.default_time_label = label;

        // Progress to Step 3 (Players)
        pkgStep = 3;
        $('#pkg-step-2').addClass('d-none');
        $('#pkg-step-3').removeClass('d-none');
        $('#pkg-step-ind-2').removeClass('text-success border-success border-3 fw-bold').addClass('text-muted fw-semibold');
        $('#pkg-step-ind-3').addClass('text-success border-success border-3 fw-bold').removeClass('text-muted fw-semibold');
    }

    function submitPkgCustomDuration() {
        const val = parseInt($('#pkg-custom-minutes').val());
        if (isNaN(val) || val < 1) {
            alert("Please enter a valid number of minutes.");
            return;
        }
        selectPkgDuration(val, val + " Min");
    }

    function selectPkgPlayerCount(count) {
        pkgData.no_of_players = count;
        proceedToPkgSummary();
    }

    function submitPkgCustomPlayers() {
        const val = parseInt($('#pkg-custom-players').val());
        if (isNaN(val) || val < 1) {
            alert("Please enter a valid player count.");
            return;
        }
        selectPkgPlayerCount(val);
    }

    function proceedToPkgSummary() {
        $('#pkg-summary-player').text(pkgData.player_name + " (" + pkgData.game_name + ")");
        $('#pkg-summary-duration').text(pkgData.default_time === 0 ? "{{ __('Unlimited (Count Up)') }}" : pkgData.default_time + ' ' + "{{ __('Minutes') }}");
        $('#pkg-summary-players').text(pkgData.no_of_players + ' ' + "{{ __('Players') }}");

        // Amount is locked to 0 for Packages
        pkgData.amount = 0;

        // Progress to Step 4 (Save)
        pkgStep = 4;
        $('#pkg-step-3').addClass('d-none');
        $('#pkg-step-4').removeClass('d-none');
        $('#pkg-step-ind-3').removeClass('text-success border-success border-3 fw-bold').addClass('text-muted fw-semibold');
        $('#pkg-step-ind-4').addClass('text-success border-success border-3 fw-bold').removeClass('text-muted fw-semibold');
        $('#btn-pkg-submit').removeClass('d-none');
    }

    function navigatePkgBack() {
        if (pkgStep === 2) {
            pkgStep = 1;
            $('#pkg-step-2').addClass('d-none');
            $('#pkg-step-1').removeClass('d-none');
            $('#pkg-step-ind-2').removeClass('text-success border-success border-3 fw-bold').addClass('text-muted fw-semibold');
            $('#pkg-step-ind-1').addClass('text-success border-success border-3 fw-bold').removeClass('text-muted fw-semibold');
            $('#btn-pkg-back').attr('disabled', 'disabled');
        } else if (pkgStep === 3) {
            pkgStep = 2;
            $('#pkg-step-3').addClass('d-none');
            $('#pkg-step-2').removeClass('d-none');
            $('#pkg-step-ind-3').removeClass('text-success border-success border-3 fw-bold').addClass('text-muted fw-semibold');
            $('#pkg-step-ind-2').addClass('text-success border-success border-3 fw-bold').removeClass('text-muted fw-semibold');
        } else if (pkgStep === 4) {
            pkgStep = 3;
            $('#pkg-step-4').addClass('d-none');
            $('#pkg-step-3').removeClass('d-none');
            $('#pkg-step-ind-4').removeClass('text-success border-success border-3 fw-bold').addClass('text-muted fw-semibold');
            $('#pkg-step-ind-3').addClass('text-success border-success border-3 fw-bold').removeClass('text-muted fw-semibold');
            $('#btn-pkg-submit').addClass('d-none');
        }
    }

    function submitPkgPlayRecord() {
        $.ajax({
            url: "{{ route('skatepark.api.store-play-record') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                player_type: pkgData.player_type,
                player_package_id: pkgData.player_package_id,
                token_id: null,
                game_type_id: pkgData.game_type_id,
                default_time: pkgData.default_time,
                no_of_players: pkgData.no_of_players,
                amount: 0, // Enforced 0
                payment_type_id: pkgData.payment_type_id
            },
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    $('#packagePlayModal').modal('hide');
                    resetPkgModal();
                    refreshSessionList();
                }
            },
            error: function(err) {
                alert(err.responseJSON ? err.responseJSON.message : "Failed to store package play session.");
            }
        });
    }

    function resetPkgModal() {
        pkgStep = 1;
        $('#pkg-step-1').removeClass('d-none');
        $('#pkg-step-2').addClass('d-none');
        $('#pkg-step-3').addClass('d-none');
        $('#pkg-step-4').addClass('d-none');
        
        $('#pkg-step-ind-1').addClass('text-success border-success border-3 fw-bold').removeClass('text-muted fw-semibold');
        $('#pkg-step-ind-2').removeClass('text-success border-success border-3 fw-bold').addClass('text-muted fw-semibold');
        $('#pkg-step-ind-3').removeClass('text-success border-success border-3 fw-bold').addClass('text-muted fw-semibold');
        $('#pkg-step-ind-4').removeClass('text-success border-success border-3 fw-bold').addClass('text-muted fw-semibold');
        
        $('#btn-pkg-back').attr('disabled', 'disabled');
        $('#btn-pkg-submit').addClass('d-none');

        $('#pkg-custom-minutes').val('');
        $('#pkg-custom-players').val('1');
        
        pkgData = {
            player_type: 'package',
            player_package_id: null,
            player_name: '',
            token_id: null,
            token_name: '',
            game_type_id: null,
            game_name: '',
            default_time: null,
            default_time_label: '',
            no_of_players: 1,
            amount: 0,
            payment_type_id: {{ $defaultPaymentType ? $defaultPaymentType->id : 'null' }}
        };
    }


    function selectEditPayment(id) {
        $('#edit-payment').val(id);
        $('#payment-buttons-container .btn-payment-select').each(function() {
            const btnId = parseInt($(this).data('id'));
            if (btnId === parseInt(id)) {
                $(this).removeClass('btn-outline-primary').addClass('btn-primary text-white');
            } else {
                $(this).addClass('btn-outline-primary').removeClass('btn-primary text-white');
            }
        });
    }

    // ==============================================
    // SINGLE STEP DIRECT EDIT MODAL LOGIC
    // ==============================================
    function openEditModal(recordId) {
        const record = activeRecords.find(r => r.id === recordId);
        if (!record) return;

        $('#edit-record-id').val(record.id);
        $('#edit-token-id').val(record.token_id || '');
        $('#edit-default-time').val(record.default_time || 0);
        $('#edit-players').val(record.no_of_players);
        selectEditPayment(record.payment_type_id);
        
        // Handle name input and show/hide state
        $('#edit-name').val(record.name || '');
        if (record.player_type === 'package') {
            $('#edit-name-section').addClass('d-none');
            $('#edit-amount-section').addClass('d-none');
            $('#edit-amount').val(0);
        } else {
            $('#edit-name-section').removeClass('d-none');
            $('#edit-amount-section').removeClass('d-none');
            $('#edit-amount').val(record.amount);
        }

        // Format start_time for datetime-local picker if it exists
        if (record.start_time) {
            const dateStr = new Date(record.start_time).toISOString().slice(0, 19);
            // Convert to local timezone format (YYYY-MM-DDTHH:MM:SS)
            const localDate = new Date(record.start_time);
            const localISO = new Date(localDate.getTime() - (localDate.getTimezoneOffset() * 60000)).toISOString().slice(0, 19);
            $('#edit-start-time').val(localISO);
        } else {
            $('#edit-start-time').val('');
        }

        $('#editPlayModal').modal('show');
    }

    function submitEditSession(e) {
        e.preventDefault();
        const recordId = $('#edit-record-id').val();
        
        const payload = {
            _token: "{{ csrf_token() }}",
            name: $('#edit-name').val(),
            token_id: $('#edit-token-id').val() || null,
            default_time: $('#edit-default-time').val(),
            no_of_players: $('#edit-players').val(),
            amount: $('#edit-amount').val(),
            payment_type_id: $('#edit-payment').val(),
            start_time: $('#edit-start-time').val()
        };

        $.ajax({
            url: `/skatepark/api/play-records/${recordId}/update`,
            type: "POST",
            data: payload,
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    $('#editPlayModal').modal('hide');
                    spokenRecords.delete(parseInt(recordId));
                    refreshSessionList();
                }
            },
            error: function(err) {
                alert(err.responseJSON ? err.responseJSON.message : "Failed to update play record.");
            }
        });
    }
</script>

<script>
    // ============================================================
    //  ADD TRANSACTION MODAL
    // ============================================================
    const txnAccounts = @json($accounts);
    const isNepali = {{ app()->getLocale() === 'ne' ? 'true' : 'false' }};
    let txnRowIndex = 0;

    function buildTxnRow(index) {
        const accountOptions = txnAccounts.map(function(a) {
            var sel = (a.is_default_cash_account && index === 0)
                      ? 'selected'
                      : (a.is_default_online_account && index === 1 ? 'selected' : '');
            
            var nameToShow = a.name;
            if (isNepali) {
                nameToShow = a.name_ne ? (a.name_ne + ' (' + a.name + ')') : a.name;
            }

            return '<option value="' + a.id + '" ' + sel + '>' + nameToShow + '</option>';
        }).join('');

        return '<div class="txn-row border rounded p-3 mb-2" data-txn-index="' + index + '" style="border-radius:10px!important;background:rgba(99,102,241,0.04)">'
            + '<div class="row g-2 align-items-end">'
            + '<div class="col-md-4"><label class="form-label fw-semibold mb-1" style="font-size:.8rem">{{ __("To Account") }}</label>'
            + '<select class="form-select" required style="border-radius:8px;font-size:.85rem;height:36px;padding:.2rem .5rem">' + accountOptions + '</select></div>'
            + '<div class="col-md-3"><label class="form-label fw-semibold mb-1" style="font-size:.8rem">{{ __("Amount") }} (Rs.)</label>'
            + '<input type="number" class="form-control txn-amount-input" min="0" value="0" onclick="this.select()" style="border-radius:8px;font-size:.9rem;height:36px;padding:.2rem .5rem"></div>'
            + '<div class="col-md-4"><label class="form-label fw-semibold mb-1" style="font-size:.8rem">{{ __("Remarks") }}</label>'
            + '<input type="text" class="form-control" placeholder="{{ __("Optional") }}" style="border-radius:8px;font-size:.85rem;height:36px;padding:.2rem .5rem"></div>'
            + '<div class="col-md-1 d-flex align-items-end justify-content-end">'
            + '<button type="button" class="btn btn-sm btn-outline-danger txn-remove-row fw-bold" style="border-radius:8px;height:36px;width:36px;padding:0">✕</button></div>'
            + '</div></div>';
    }

    function recalcTxnTotal() {
        var total = 0;
        document.querySelectorAll('.txn-amount-input').forEach(function(inp) {
            total += parseFloat(inp.value) || 0;
        });
        document.getElementById('txn-total-display').textContent = total.toFixed(2);
    }

    function initTxnModal() {
        txnRowIndex = 0;
        var container = document.getElementById('txn-repeater-container');
        container.innerHTML = '';
        container.insertAdjacentHTML('beforeend', buildTxnRow(txnRowIndex++));
        container.insertAdjacentHTML('beforeend', buildTxnRow(txnRowIndex++));
        recalcTxnTotal();
    }

    // Call immediately — DOM is available at this point in the body
    initTxnModal();

    document.getElementById('txn-add-row-btn').addEventListener('click', function() {
        document.getElementById('txn-repeater-container').insertAdjacentHTML('beforeend', buildTxnRow(txnRowIndex++));
        recalcTxnTotal();
    });

    document.getElementById('txn-repeater-container').addEventListener('input', function(e) {
        if (e.target.classList.contains('txn-amount-input')) recalcTxnTotal();
    });

    document.getElementById('txn-repeater-container').addEventListener('click', function(e) {
        var btn = e.target.closest('.txn-remove-row');
        if (!btn) return;
        if (document.querySelectorAll('.txn-row').length <= 1) return;
        btn.closest('.txn-row').remove();
        recalcTxnTotal();
    });

    document.getElementById('txn-submit-btn').addEventListener('click', function() {
        var dateBS = document.getElementById('txn-date-bs').value.trim();
        if (!dateBS) {
            alert("{{ __('Please enter the date.') }}");
            document.getElementById('txn-date-bs').focus();
            return;
        }

        var rows = document.querySelectorAll('.txn-row');
        var transactions = [];
        var valid = true;
        rows.forEach(function(row) {
            var toAccount = row.querySelector('select').value;
            var amount    = parseFloat(row.querySelector('.txn-amount-input').value) || 0;
            var remarks   = row.querySelector('input[type="text"]').value;
            if (!toAccount) valid = false;
            transactions.push({ to_account_id: toAccount, amount: amount, remarks: remarks });
        });

        if (!valid) { alert("{{ __('Please fill all required fields.') }}"); return; }

        var total = transactions.reduce(function(s, t) { return s + (parseFloat(t.amount) || 0); }, 0);

        var submitBtn = document.getElementById('txn-submit-btn');
        submitBtn.disabled = true;
        var originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + "{{ __('Saving...') }}";

        var payload = {
            _token: "{{ csrf_token() }}",
            date_bs: dateBS,
            total: total,
            transactions: transactions
        };

        $.ajax({
            url: "{{ route('transactions.store') }}",
            type: "POST",
            data: payload,
            dataType: "json",
            success: function(res) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                if (res.success) {
                    // Close the modal using Bootstrap
                    $('#addTransactionModal').modal('hide');
                    alert(res.message || "{{ __('Transactions created successfully.') }}");
                } else {
                    alert(res.message || "{{ __('Failed to save transactions.') }}");
                }
            },
            error: function(xhr) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                var errMsg = "{{ __('Failed to save transactions.') }}";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                alert(errMsg);
            }
        });
    });

    // ── Datepicker + modal events ──────────────────────────────────
    // window 'load' fires AFTER all external scripts (jQuery, nepali datepicker CDN)
    // have fully loaded, so $ and $.fn.nepaliDatePicker are safe to use here.
    window.addEventListener('load', function() {
        function getTodayBS() {
            if (typeof NepaliFunctions !== 'undefined' && NepaliFunctions.BS) {
                try {
                    var d = NepaliFunctions.BS.GetCurrentDate();
                    return NepaliFunctions.ConvertToDateFormat(d, 'YYYY-MM-DD');
                } catch(e) {}
            }
            return '';
        }

        var txnModal = document.getElementById('addTransactionModal');

        txnModal.addEventListener('shown.bs.modal', function() {
            // The layout's initializeDatePickers() already bound the calendar on document.ready.
            // Just reset the value to today — do NOT focus (that auto-opens the calendar).
            var todayBS = getTodayBS();
            var dateInput = document.getElementById('txn-date-bs');
            if (dateInput) {
                dateInput.value = todayBS;
            }
        });

        txnModal.addEventListener('hidden.bs.modal', function() {
            initTxnModal();
        });
    });
</script>
@endsection
