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
    <style>
        body {
            padding-top: 56px;
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
</style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/">Accounting System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('accounts.index') }}">Accounts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('transactions.index') }}">Transactions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('account_transactions.index') }}">Money Transfer</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('expenses.index') }}">Expenses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('extra_incomes.index') }}">Extra Incomes</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://nepalidatepicker.sajanmaharjan.com.np/v5/nepali.datepicker/js/nepali.datepicker.v5.0.4.min.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            function initializeDatePickers() {
                try {
                    $('.nepali-datepicker').each(function() {
                        let defaultToday = '';
                        let range = false;
                        if($(this).hasClass('date-range')){
                            range = true;
                        }
                        if($(this).hasClass('default-today-date')){
                            let bsDate = NepaliFunctions.BS.GetCurrentDate();
                            defaultToday = NepaliFunctions.ConvertToDateFormat(bsDate,"YYYY-MM-DD");
                        }
                        if (!$(this).hasClass('nepali-datepicker-initialized')) {
                            this.NepaliDatePicker({
                                'language':'english',
                                // "miniEnglishDates": true,
                                'value':defaultToday,
                                'range':range
                            });
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