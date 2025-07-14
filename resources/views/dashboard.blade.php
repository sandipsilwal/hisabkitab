@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="mb-4">Dashboard</h1>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Accounts</h5>
                        <p class="card-text">{{ \App\Models\Account::count() }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Transactions</h5>
                        <p class="card-text">{{ \App\Models\Transaction::count() }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Expenses</h5>
                        <p class="card-text">{{ \App\Models\Expense::count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection