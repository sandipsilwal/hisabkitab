@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h1 class="mb-4">Money Transfer</h1>
        <form action="{{ route('account_transactions.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="date_bs" class="form-label">Date</label>
                <input type="text" class="form-control nepali-datepicker default-today-date" id="nepali_date" name="date_bs" required>
            </div>
            <div id="repeater-container">
                <div class="repeater-item">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="from_account_id" class="form-label">From Account</label>
                            <select class="form-control" name="from_account_id" required>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="to_account_id" class="form-label">To Account</label>
                            <select class="form-control" name="to_account_id" required>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" name="amount" required>
                        </div>
                        <div class="col-md-2">
                            <label for="remarks" class="form-label">Remarks</label>
                            <input type="text" class="form-control" name="remarks">
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-success mt-3 ms-2">Transfer</button>
        </form>
    </div>
</div>
@endsection