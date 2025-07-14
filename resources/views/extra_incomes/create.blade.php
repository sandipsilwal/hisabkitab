@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h1 class="mb-4">Extra Income</h1>
        <form action="{{ route('extra_incomes.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="date_bs" class="form-label">Date</label>
                <input type="text" class="form-control nepali-datepicker default-today-date" id="date_bs" name="date_bs" required>
            </div>
            <div id="repeater-container">
                <div class="repeater-item">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" required>
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
            <button type="submit" class="btn btn-primary mt-3 ms-2">Create</button>
        </form>
    </div>
</div>

@endsection