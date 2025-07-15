@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="mb-4">Dashboard</h1>
        <div class="col-md-12 align-self-end">
            <span class="btn btn-sm total_bg" style="float: right;"><b>Total Balance Today: {{ number_format($total_balance, 2) }}</b></span>
        </div>
        <form method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label for="date_range_filter" class="form-label">Start Date</label>
                    <input type="text" value="{{$filter_date_range}}" class="form-control nepali-datepicker date-range" id="date_range_filter" name="date_range_filter" autocomplete="off">
                </div>
                <div class="col-md-3 align-self-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
                <div class="col-md-6 align-self-end">
                    <button type="submit" name="date_filter" value="this_month" class="btn btn-secondary btn-sm">This Month</button>
                    <button type="submit" name="date_filter" value="last_month" class="btn btn-secondary btn-sm">Last Month</button>
                    <button type="submit" name="date_filter" value="this_week" class="btn btn-secondary btn-sm">This Week</button>
                    <button type="submit" name="date_filter" value="last_week" class="btn btn-secondary btn-sm">Last Week</button>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card" style="background: rgb(28, 201, 71);color:white;">
                    <div class="card-body">
                        <h5 class="card-title">Skating Income</h5>
                        <p class="card-text">Rs. {{$total_amount}}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card" style="background: rgba(18, 121, 18, 0.58); color:white;">
                    <div class="card-body">
                        <h5 class="card-title">Total Extra Income</h5>
                        <p class="card-text">Rs. {{ $extra_income }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card"  style="background: rgba(255,0,0,1); color:white">
                    <div class="card-body">
                        <h5 class="card-title">Total Expenses</h5>
                        <p class="card-text">Rs. {{ $expenses }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection