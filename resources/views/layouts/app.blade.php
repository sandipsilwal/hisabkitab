<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounting System</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://nepalidatepicker.sajanmaharjan.com.np/v5/nepali.datepicker/css/nepali.datepicker.v5.0.4.min.css" rel="stylesheet" type="text/css"/> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link href="https://unpkg.com/nepali-date-picker@2.0.2/dist/nepaliDatePicker.min.css" rel="stylesheet" crossorigin="anonymous"> -->
    <link href="https://nepalidatepicker.sajanmaharjan.com.np/v5/nepali.datepicker/css/nepali.datepicker.v5.0.4.min.css" rel="stylesheet" type="text/css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Override native window.alert to display a beautiful SweetAlert2 modal instead
        window.alert = function(message) {
            const isDarkMode = document.body.classList.contains('dark-mode') || localStorage.getItem('theme') === 'dark';
            Swal.fire({
                text: message,
                background: isDarkMode ? '#1e293b' : '#ffffff',
                color: isDarkMode ? '#f8fafc' : '#0f172a',
                confirmButtonColor: '#38bdf8',
                confirmButtonText: 'OK'
            });
        };
    </script>
    <style>
        body {
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .navbar-custom {
            background: linear-gradient(185deg, #1e293b 0%, #0f172a 100%) !important;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }
        .navbar-brand {
            font-size: 1.25rem;
            letter-spacing: 0.5px;
        }
        .nav-link {
            font-weight: 600;
            font-size: 0.92rem;
            padding: 0.65rem 0.85rem !important;
            margin: 0.15rem 0;
            border-radius: 6px;
            transition: all 0.2s ease;
            color: rgba(255, 255, 255, 0.7) !important;
            width: 100%;
            display: flex !important;
            align-items: center;
            justify-content: space-between;
        }
        .nav-link.active {
            background-color: #38bdf8 !important;
            color: #000000 !important;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(56, 189, 248, 0.25);
        }
        .nav-link:hover:not(.active) {
            background-color: rgba(255, 255, 255, 0.06) !important;
            color: #ffffff !important;
        }
        .dropdown-toggle::after {
            display: inline-block !important;
            content: "";
            width: 6px;
            height: 6px;
            border-right: 2px solid currentColor;
            border-bottom: 2px solid currentColor;
            transform: rotate(45deg);
            transition: transform 0.2s ease;
            margin-left: auto;
            border-top: 0 !important;
            border-left: 0 !important;
        }
        .dropdown-toggle[aria-expanded="true"]::after,
        .dropdown-toggle.show::after {
            transform: rotate(-135deg);
        }
        .repeater-item {
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .bg-red {
            background: rgba(255, 0, 0, 0.4) !important;
            color: white !important;
        }
        .total_bg{
            background: rgba(91, 189, 61, 0.97) !important;
            color: white !important;
        }
        .day_bg{
            background:rgba(132, 202, 138, 0.97) !important;
            color:white;
            font-weight:bold;
        }
        table tr td,th{
            border: 1px solid white;
            text-align:center;
            vertical-align:middle;
        }

        /* Fixed Left Sidebar Drawer System */
        .navbar-sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            flex-direction: column;
            justify-content: flex-start !important;
            padding: 2rem 1.2rem !important;
            overflow-y: auto;
            z-index: 99995 !important;
            border-radius: 0 !important;
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            box-shadow: 10px 0 30px rgba(0, 0, 0, 0.25);
        }
        .navbar-sidebar.show {
            transform: translateX(0);
        }
        .navbar-sidebar .sidebar-menu {
            width: 100%;
            display: block !important;
        }
        .navbar-sidebar .sidebar-nav-list {
            display: flex !important;
            flex-direction: column;
            width: 100%;
            padding-left: 0;
            margin-bottom: 0;
            list-style: none;
            gap: 0.25rem;
        }
        .navbar-sidebar .dropdown-menu {
            position: static !important;
            transform: none !important;
            float: none !important;
            background-color: transparent !important;
            border: none !important;
            border-left: 1.5px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 0 !important;
            margin: 0.1rem 0 0.5rem 1.2rem !important;
            padding: 0.2rem 0 0.2rem 0.5rem !important;
            box-shadow: none !important;
            width: auto !important;
        }
        .navbar-sidebar .dropdown-item {
            color: rgba(255, 255, 255, 0.55) !important;
            font-size: 0.86rem;
            font-weight: 500;
            padding: 0.45rem 0.85rem !important;
            border-radius: 5px;
            transition: all 0.15s ease;
            white-space: normal;
            display: block;
        }
        .navbar-sidebar .dropdown-item:hover {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.05) !important;
            padding-left: 1.15rem !important;
        }
        .navbar-sidebar .dropdown-item.active {
            color: #38bdf8 !important;
            font-weight: 700;
            background-color: transparent !important;
            padding-left: 1.3rem !important;
        }
        
        /* Sidebar Backdrop Overlay */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(2px);
            z-index: 99990 !important;
            display: none;
        }
        .sidebar-backdrop.show {
            display: block;
        }

        /* Hamburger Menu Button */
        .sidebar-toggle-btn {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 99980 !important;
            width: 42px;
            height: 42px;
            border-radius: 8px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            background-color: #ffffff;
            color: #0f172a !important;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .sidebar-toggle-btn:hover {
            transform: scale(1.05);
            background-color: #f1f5f9;
        }
        .sidebar-toggle-btn:active {
            transform: scale(0.95);
        }

        /* User Avatar Button (top-right) */
        .user-avatar-btn {
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 99980 !important;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 2px solid rgba(56, 189, 248, 0.5);
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            cursor: pointer;
            font-size: 1.1rem;
        }
        .user-avatar-btn:hover {
            transform: scale(1.06);
            border-color: #38bdf8;
            box-shadow: 0 6px 18px rgba(56,189,248,0.25);
        }
        .user-avatar-btn:active {
            transform: scale(0.95);
        }

        /* User Dropdown Panel */
        .user-dropdown-panel {
            position: fixed;
            top: 65px;
            right: 15px;
            z-index: 99985 !important;
            width: 240px;
            background: #1e293b;
            border-radius: 14px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.35);
            border: 1px solid rgba(255,255,255,0.08);
            padding: 0.5rem;
            display: none;
            transform-origin: top right;
            animation: dropdownIn 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .user-dropdown-panel.show {
            display: block;
        }
        @keyframes dropdownIn {
            from { opacity: 0; transform: scale(0.92) translateY(-8px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }
        .user-dropdown-panel .udp-header {
            padding: 0.65rem 0.75rem 0.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            margin-bottom: 0.35rem;
        }
        .user-dropdown-panel .udp-header .udp-name {
            font-size: 0.82rem;
            font-weight: 700;
            color: #f1f5f9;
        }
        .user-dropdown-panel .udp-header .udp-label {
            font-size: 0.68rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .udp-section-label {
            font-size: 0.63rem;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            font-weight: 700;
            padding: 0.45rem 0.75rem 0.2rem;
        }
        .udp-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            font-size: 0.82rem;
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }
        .udp-item:hover {
            background: rgba(255,255,255,0.07);
            color: #ffffff;
        }
        .udp-item.active-lang {
            color: #38bdf8;
            font-weight: 700;
        }
        .udp-item.logout-item:hover {
            background: rgba(239,68,68,0.12);
            color: #f87171;
        }
        .udp-divider {
            border-top: 1px solid rgba(255,255,255,0.06);
            margin: 0.3rem 0.5rem;
        }
        body.dark-mode .user-avatar-btn {
            background: linear-gradient(135deg, #0f172a 0%, #020617 100%);
            border-color: rgba(56, 189, 248, 0.4);
        }

        /* Content Layout */
        .main-content {
            padding: 2rem !important;
            padding-top: 75px !important; /* Clears top toggle button */
            min-height: 100vh;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        /* Dark mode integration for layouts navbar */
        body.dark-mode .navbar-sidebar {
            background: #121212 !important;
            border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 10px 0 40px rgba(0, 0, 0, 0.6);
        }
        body.dark-mode .navbar-sidebar .dropdown-menu {
            background-color: transparent !important;
        }
        body.dark-mode .main-content {
            background-color: #000000 !important;
        }
        body.dark-mode .sidebar-toggle-btn {
            background-color: #1e293b !important;
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
        }
        body.dark-mode .sidebar-toggle-btn:hover {
            background-color: #334155 !important;
        }
        
        /* Global premium dark mode overrides */
        body.dark-mode {
            background-color: #000000 !important;
            color: #e2e8f0 !important;
        }
        body.dark-mode h1,
        body.dark-mode h2,
        body.dark-mode h3,
        body.dark-mode h4,
        body.dark-mode h5,
        body.dark-mode h6,
        body.dark-mode .h1,
        body.dark-mode .h2,
        body.dark-mode .h3,
        body.dark-mode .h4,
        body.dark-mode .h5,
        body.dark-mode .h6,
        body.dark-mode label,
        body.dark-mode .form-label,
        body.dark-mode th,
        body.dark-mode td:not(.text-white),
        body.dark-mode .card-title,
        body.dark-mode .modal-title,
        body.dark-mode strong {
            color: #f8fafc !important;
        }
        body.dark-mode .text-dark {
            color: #f8fafc !important;
        }
        body.dark-mode .text-muted,
        body.dark-mode .form-text {
            color: #94a3b8 !important;
        }
        body.dark-mode .card {
            background-color: #121212 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            color: #e2e8f0 !important;
        }
        body.dark-mode .card-header {
            background-color: #121212 !important;
            border-bottom-color: rgba(255, 255, 255, 0.08) !important;
            color: #f8fafc !important;
        }
        body.dark-mode .bg-light {
            background-color: #000000 !important;
        }
        body.dark-mode .modal-content {
            background-color: #121212 !important;
            color: #e2e8f0 !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
        }
        body.dark-mode .modal-footer,
        body.dark-mode .modal-header.bg-light {
            background-color: #000000 !important;
        }
        body.dark-mode input.form-control,
        body.dark-mode select.form-control,
        body.dark-mode select.form-select,
        body.dark-mode textarea.form-control {
            background-color: #1a1a1a !important;
            border-color: rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
        }
        body.dark-mode input.form-control:focus,
        body.dark-mode select.form-control:focus,
        body.dark-mode select.form-select:focus,
        body.dark-mode textarea.form-control:focus {
            border-color: #38bdf8 !important;
            box-shadow: 0 0 0 0.25rem rgba(56, 189, 248, 0.25) !important;
        }
        
        /* Table overrides in dark mode */
        body.dark-mode table,
        body.dark-mode .table {
            color: #e2e8f0 !important;
            background-color: #000000 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        body.dark-mode .table th,
        body.dark-mode .table td {
            background-color: #121212 !important;
            color: #e2e8f0 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        body.dark-mode .table-striped > tbody > tr:nth-of-type(odd) > * {
            background-color: #1a1a1a !important;
            color: #e2e8f0 !important;
        }
        body.dark-mode .table-hover tbody tr:hover > * {
            background-color: #262626 !important;
            color: #ffffff !important;
        }
        body.dark-mode .border-top,
        body.dark-mode .border-bottom,
        body.dark-mode .border {
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        
        /* Pagination in dark mode */
        body.dark-mode .pagination .page-link {
            background-color: #121212 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            color: #38bdf8 !important;
        }
        body.dark-mode .pagination .page-item.active .page-link {
            background-color: #38bdf8 !important;
            border-color: #38bdf8 !important;
            color: #000000 !important;
        }
        body.dark-mode .pagination .page-item.disabled .page-link {
            background-color: #1a1a1a !important;
            border-color: rgba(255, 255, 255, 0.05) !important;
            color: #64748b !important;
        }

        /* Dark mode custom background and border overrides */
        body.dark-mode .bg-white {
            background-color: #121212 !important;
            color: #e2e8f0 !important;
        }
        body.dark-mode .bg-light-subtle {
            background-color: #1a1a1a !important;
            color: #e2e8f0 !important;
        }
        body.dark-mode .border-light {
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        /* Subtle badge overrides in dark mode */
        body.dark-mode .bg-success-subtle {
            background-color: rgba(34, 197, 94, 0.12) !important;
            color: #4ade80 !important;
            border-color: rgba(34, 197, 94, 0.25) !important;
        }
        body.dark-mode .bg-warning-subtle {
            background-color: rgba(234, 179, 8, 0.12) !important;
            color: #facc15 !important;
            border-color: rgba(234, 179, 8, 0.25) !important;
        }
        body.dark-mode .bg-secondary-subtle {
            background-color: rgba(148, 163, 184, 0.12) !important;
            color: #cbd5e1 !important;
            border-color: rgba(148, 163, 184, 0.25) !important;
        }
        body.dark-mode .bg-info-subtle {
            background-color: rgba(56, 189, 248, 0.12) !important;
            color: #38bdf8 !important;
            border-color: rgba(56, 189, 248, 0.25) !important;
        }

        /* Dark mode button colors */
        body.dark-mode .btn-primary {
            background-color: #38bdf8 !important;
            border-color: #38bdf8 !important;
            color: #000000 !important;
            font-weight: 600 !important;
        }
        body.dark-mode .btn-primary:hover,
        body.dark-mode .btn-primary:focus,
        body.dark-mode .btn-primary:active {
            background-color: #0ea5e9 !important;
            border-color: #0ea5e9 !important;
            color: #000000 !important;
        }

        body.dark-mode .btn-success {
            background-color: #22c55e !important;
            border-color: #22c55e !important;
            color: #000000 !important;
            font-weight: 600 !important;
        }
        body.dark-mode .btn-success:hover,
        body.dark-mode .btn-success:focus,
        body.dark-mode .btn-success:active {
            background-color: #16a34a !important;
            border-color: #16a34a !important;
            color: #000000 !important;
        }

        body.dark-mode .btn-outline-warning {
            color: #facc15 !important;
            border-color: #facc15 !important;
            background-color: transparent !important;
        }
        body.dark-mode .btn-outline-warning:hover,
        body.dark-mode .btn-outline-warning:focus,
        body.dark-mode .btn-outline-warning:active {
            background-color: #facc15 !important;
            border-color: #facc15 !important;
            color: #000000 !important;
        }

        body.dark-mode .btn-outline-danger {
            color: #f87171 !important;
            border-color: #f87171 !important;
            background-color: transparent !important;
        }
        body.dark-mode .btn-outline-danger:hover,
        body.dark-mode .btn-outline-danger:focus,
        body.dark-mode .btn-outline-danger:active {
            background-color: #ef4444 !important;
            border-color: #ef4444 !important;
            color: #ffffff !important;
        }

        body.dark-mode .btn-secondary {
            background-color: #334155 !important;
            border-color: #475569 !important;
            color: #f8fafc !important;
        }
        body.dark-mode .btn-secondary:hover,
        body.dark-mode .btn-secondary:focus,
        body.dark-mode .btn-secondary:active {
            background-color: #475569 !important;
            border-color: #64748b !important;
            color: #ffffff !important;
        }

        body.dark-mode .btn-outline-secondary {
            color: #cbd5e1 !important;
            border-color: #475569 !important;
            background-color: transparent !important;
        }
        body.dark-mode .btn-outline-secondary:hover,
        body.dark-mode .btn-outline-secondary:focus,
        body.dark-mode .btn-outline-secondary:active {
            background-color: #334155 !important;
            border-color: #475569 !important;
            color: #ffffff !important;
        }

        body.dark-mode .btn-light {
            background-color: #334155 !important;
            border-color: #475569 !important;
            color: #f8fafc !important;
        }
        body.dark-mode .btn-light:hover,
        body.dark-mode .btn-light:focus,
        body.dark-mode .btn-light:active {
            background-color: #475569 !important;
            border-color: #64748b !important;
            color: #ffffff !important;
        }
    </style>
</head>
<body>
    <script>
        // Immediately apply dark mode class if stored in localStorage to prevent page flash
        (function() {
            const currentTheme = localStorage.getItem('theme') || 'dark';
            if (currentTheme === 'dark') {
                document.body.classList.add('dark-mode');
            }
        })();
    </script>

    <!-- Sidebar Toggle Button -->
    <button id="sidebar-toggle" class="sidebar-toggle-btn" title="Toggle Navigation Menu">
        <span style="font-size: 1.25rem; font-weight: bold; line-height: 1;">☰</span>
    </button>

    <!-- User Avatar Button (top-right) -->
    <button id="user-avatar-btn" class="user-avatar-btn" title="Account Options">👤</button>

    <!-- User Dropdown Panel -->
    <div id="user-dropdown-panel" class="user-dropdown-panel">
        @auth
        <div class="udp-header">
            <div class="udp-label">{{ __('Signed in as') }}</div>
            <div class="udp-name">{{ Auth::user()->name }}</div>
        </div>
        @endauth

        <!-- Language -->
        <div class="udp-section-label">{{ __('Language') }}</div>
        <a href="{{ route('lang.switch', 'en') }}" class="udp-item {{ app()->getLocale() == 'en' ? 'active-lang' : '' }}">
            <span>🇬🇧</span> English {{ app()->getLocale() == 'en' ? '✓' : '' }}
        </a>
        <a href="{{ route('lang.switch', 'ne') }}" class="udp-item {{ app()->getLocale() == 'ne' ? 'active-lang' : '' }}">
            <span>🇳🇵</span> नेपाली {{ app()->getLocale() == 'ne' ? '✓' : '' }}
        </a>

        <div class="udp-divider"></div>

        <!-- Theme -->
        <div class="udp-section-label">{{ __('Theme') }}</div>
        <button id="theme-toggle" class="udp-item">
            <span id="theme-icon">🌙</span>
            <span id="theme-text">{{ __('Dark Mode') }}</span>
        </button>

        <div class="udp-divider"></div>

        <!-- Logout -->
        @auth
        <form action="{{ route('logout') }}" method="POST" id="udp-logout-form">
            @csrf
        </form>
        <button onclick="document.getElementById('udp-logout-form').submit()" class="udp-item logout-item">
            <span>🚪</span> {{ __('Logout') }}
        </button>
        @endauth
    </div>

    <!-- Sidebar Backdrop Overlay -->
    <div id="sidebar-backdrop" class="sidebar-backdrop"></div>

    <nav class="navbar navbar-dark navbar-custom navbar-sidebar">
        <div class="w-100 px-3">
            <a class="navbar-brand fw-bold d-flex align-items-center mb-4 w-100" href="{{ route('dashboard') }}">
                <span class="bg-primary text-white px-2 py-0.5 rounded me-2 fs-6">HK</span>
                <span>Hisab Kitab</span>
            </a>
            <div class="sidebar-menu w-100" id="navbarNav">
                <ul class="sidebar-nav-list w-100">
                    <!-- Finance Dropdown -->
                    @php
                        $isFinanceActive = request()->routeIs('dashboard') || 
                                           request()->is('accounts*') || 
                                           request()->is('account_transactions*') || 
                                           request()->is('expenses*') || 
                                           request()->is('extra_incomes*');
                    @endphp
                    <li class="nav-item dropdown w-100">
                        <a class="nav-link dropdown-toggle {{ $isFinanceActive ? 'active' : '' }}" href="#" id="financeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __('Finance') }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark border-0 {{ $isFinanceActive ? 'show' : '' }}" aria-labelledby="financeDropdown">
                            <li><a class="dropdown-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                            <li><a class="dropdown-item {{ request()->is('accounts*') ? 'active' : '' }}" href="{{ route('accounts.index') }}">{{ __('Accounts') }}</a></li>
                            <li><a class="dropdown-item {{ request()->is('account_transactions*') ? 'active' : '' }}" href="{{ route('account_transactions.index') }}">{{ __('Money Transfer') }}</a></li>
                            <li><a class="dropdown-item {{ request()->is('expenses*') ? 'active' : '' }}" href="{{ route('expenses.index') }}">{{ __('Expenses') }}</a></li>
                            <li><a class="dropdown-item {{ request()->is('extra_incomes*') ? 'active' : '' }}" href="{{ route('extra_incomes.index') }}">{{ __('Extra Incomes') }}</a></li>
                        </ul>
                    </li>

                    <!-- Transactions (Top-level) -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('transactions*') ? 'active' : '' }}" href="{{ route('transactions.index') }}">{{ __('Transactions') }}</a>
                    </li>

                    <!-- Current Session (Top-level) -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('skatepark.current-session') ? 'active' : '' }}" href="{{ route('skatepark.current-session') }}">{{ __('Current Session') }}</a>
                    </li>

                    <!-- Reports (Top-level) -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('skatepark.reports') ? 'active' : '' }}" href="{{ route('skatepark.reports') }}">{{ __('Reports') }}</a>
                    </li>

                    <!-- Players & Packages (Top-level) -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('skatepark/players*') || request()->is('skatepark/player_packages*') ? 'active' : '' }}" href="{{ route('players.index') }}">{{ __('Players & Packages') }}</a>
                    </li>

                    <!-- Masters Dropdown -->
                    @php
                        $isMastersActive = request()->is('skatepark/game_types*') || 
                                           request()->is('skatepark/payment_types*') || 
                                           request()->is('skatepark/default_times*') || 
                                           request()->is('skatepark/rates*') || 
                                           request()->is('skatepark/tokens*') || 
                                           request()->is('skatepark/packages*');
                    @endphp
                    <li class="nav-item dropdown w-100">
                        <a class="nav-link dropdown-toggle {{ $isMastersActive ? 'active' : '' }}" href="#" id="mastersDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __('Masters') }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark border-0 {{ $isMastersActive ? 'show' : '' }}" aria-labelledby="mastersDropdown">
                            <li><a class="dropdown-item {{ request()->is('skatepark/game_types*') ? 'active' : '' }}" href="{{ route('game_types.index') }}">{{ __('Game Types') }}</a></li>
                            <li><a class="dropdown-item {{ request()->is('skatepark/payment_types*') ? 'active' : '' }}" href="{{ route('payment_types.index') }}">{{ __('Payment Types') }}</a></li>
                            <li><a class="dropdown-item {{ request()->is('skatepark/default_times*') ? 'active' : '' }}" href="{{ route('default_times.index') }}">{{ __('Default Times') }}</a></li>
                            <li><a class="dropdown-item {{ request()->is('skatepark/rates*') ? 'active' : '' }}" href="{{ route('rates.index') }}">{{ __('Rates') }}</a></li>
                            <li><a class="dropdown-item {{ request()->is('skatepark/tokens*') ? 'active' : '' }}" href="{{ route('tokens.index') }}">{{ __('Tokens') }}</a></li>
                            <li><a class="dropdown-item {{ request()->is('skatepark/packages*') ? 'active' : '' }}" href="{{ route('packages.index') }}">{{ __('Packages') }}</a></li>
                        </ul>
                    </li>


                </ul>
            </div>
        </div>
    </nav>

    <script>
        // High-reliability Vanilla JS toggle script
        (function() {
            const toggleBtn = document.getElementById('sidebar-toggle');
            const sidebar = document.querySelector('.navbar-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');

            if (toggleBtn && sidebar && backdrop) {
                const openSidebar = function(e) {
                    if (e) e.preventDefault();
                    sidebar.classList.add('show');
                    backdrop.classList.add('show');
                };

                const closeSidebar = function() {
                    sidebar.classList.remove('show');
                    backdrop.classList.remove('show');
                };

                toggleBtn.addEventListener('click', openSidebar);
                backdrop.addEventListener('click', closeSidebar);

                // Close sidebar when clicking links inside it (excluding dropdowns)
                const links = sidebar.querySelectorAll('.nav-link:not(.dropdown-toggle), .dropdown-item');
                links.forEach(function(link) {
                    link.addEventListener('click', closeSidebar);
                });
            }
        })();

        // User Avatar Dropdown
        (function() {
            const avatarBtn = document.getElementById('user-avatar-btn');
            const panel = document.getElementById('user-dropdown-panel');

            if (avatarBtn && panel) {
                avatarBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    panel.classList.toggle('show');
                });
                document.addEventListener('click', function(e) {
                    if (!panel.contains(e.target) && e.target !== avatarBtn) {
                        panel.classList.remove('show');
                    }
                });
            }
        })();

        // Global Theme Toggle Script
        (function() {
            const themeBtn = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');
            const themeText = document.getElementById('theme-text');

            function updateThemeUI(theme) {
                if (theme === 'dark') {
                    if (themeIcon) themeIcon.textContent = '☀️';
                    if (themeText) themeText.textContent = "{{ __('Light Mode') }}";
                } else {
                    if (themeIcon) themeIcon.textContent = '🌙';
                    if (themeText) themeText.textContent = "{{ __('Dark Mode') }}";
                }
            }

            const initialTheme = localStorage.getItem('theme') || 'dark';
            updateThemeUI(initialTheme);

            if (themeBtn) {
                themeBtn.addEventListener('click', function() {
                    const body = document.body;
                    if (body.classList.contains('dark-mode')) {
                        body.classList.remove('dark-mode');
                        localStorage.setItem('theme', 'light');
                        updateThemeUI('light');
                    } else {
                        body.classList.add('dark-mode');
                        localStorage.setItem('theme', 'dark');
                        updateThemeUI('dark');
                    }
                });
            }
        })();
    </script>

    <div class="main-content">
        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://nepalidatepicker.sajanmaharjan.com.np/v5/nepali.datepicker/js/nepali.datepicker.v5.0.4.min.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            // SweetAlert flash messages and error alerts
            const isDarkMode = document.body.classList.contains('dark-mode') || localStorage.getItem('theme') === 'dark';
            
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                background: isDarkMode ? '#1e293b' : '#ffffff',
                color: isDarkMode ? '#f8fafc' : '#0f172a',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            @if (session('success'))
                Toast.fire({
                    icon: 'success',
                    title: {!! json_encode(session('success')) !!}
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: {!! json_encode(session('error')) !!},
                    background: isDarkMode ? '#1e293b' : '#ffffff',
                    color: isDarkMode ? '#f8fafc' : '#0f172a',
                    confirmButtonColor: '#38bdf8',
                    confirmButtonText: 'OK'
                });
            @endif

            @if ($errors->any())
                @php
                    $errorHtml = '<ul class="text-start mb-0" style="list-style-type: disc; padding-left: 15px;">';
                    foreach ($errors->all() as $error) {
                        $errorHtml .= '<li>' . e($error) . '</li>';
                    }
                    $errorHtml .= '</ul>';
                @endphp
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Errors',
                    html: '{!! $errorHtml !!}',
                    background: isDarkMode ? '#1e293b' : '#ffffff',
                    color: isDarkMode ? '#f8fafc' : '#0f172a',
                    confirmButtonColor: '#38bdf8',
                    confirmButtonText: 'OK'
                });
            @endif

            function initializeDatePickers() {
                try {
                    $('.nepali-datepicker').each(function() {
                        let defaultToday = '';
                        let range = $(this).hasClass('date-range');
                        
                        if ($(this).hasClass('default-today-date')) {
                            if (typeof NepaliFunctions !== 'undefined' && NepaliFunctions.BS) {
                                let bsDate = NepaliFunctions.BS.GetCurrentDate();
                                defaultToday = NepaliFunctions.ConvertToDateFormat(bsDate, "YYYY-MM-DD");
                            }
                        }
                        
                        if (!$(this).hasClass('nepali-datepicker-initialized')) {
                            if (typeof $.fn.nepaliDatePicker === 'function') {
                                $(this).nepaliDatePicker({
                                    'language': 'english',
                                    'value': defaultToday,
                                    'range': range
                                });
                                $(this).addClass('nepali-datepicker-initialized');
                            } else if (typeof this.NepaliDatePicker === 'function') {
                                this.NepaliDatePicker({
                                    'language': 'english',
                                    'value': defaultToday,
                                    'range': range
                                });
                                $(this).addClass('nepali-datepicker-initialized');
                            }
                        }
                    });
                    console.log('Nepali Date Picker initialization completed');
                } catch (error) {
                    console.error('Nepali Date Picker initialization failed:', error);
                }
            }
            initializeDatePickers();


        });
    </script>
</body>
</html>