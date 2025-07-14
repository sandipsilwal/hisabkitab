@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h1 class="mb-4">Edit Expense</h1>
        <form action="{{ route('expenses.update', $expense) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="date_bs" class="form-label">Date</label>
                <input type="text" class="form-control nepali-datepicker" id="date_bs" name="date_bs" value="{{ $expense->date_bs }}" required>
            </div>
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ $expense->title }}" required>
            </div>
            <div class="mb-3">
                <label for="from_account_id" class="form-label">From Account</label>
                <select class="form-control" id="from_account_id" name="from_account_id" required>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}" {{ $expense->from_account_id == $account->id ? 'selected' : '' }}>
                            {{ $account->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" value="{{ $expense->amount }}" required>
            </div>
            <div class="mb-3">
                <label for="remarks" class="form-label">Remarks</label>
                <input type="text" class="form-control" id="remarks" name="remarks" value="{{ $expense->remarks }}">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection