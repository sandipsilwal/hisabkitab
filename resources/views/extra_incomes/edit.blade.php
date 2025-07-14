@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h1 class="mb-4">Edit Extra Income</h1>
        <form action="{{ route('extra_incomes.update', $extraIncome) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="date_bs" class="form-label">Date</label>
                <input type="text" class="form-control nepali-datepicker" id="date_bs" name="date_bs" value="{{ $extraIncome->date_bs }}" required>
            </div>
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ $extraIncome->title }}" required>
            </div>
            <div class="mb-3">
                <label for="to_account_id" class="form-label">To Account</label>
                <select class="form-control" id="to_account_id" name="to_account_id" required>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}" {{ $extraIncome->to_account_id == $account->id ? 'selected' : '' }}>
                            {{ $account->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" value="{{ $extraIncome->amount }}" required>
            </div>
            <div class="mb-3">
                <label for="remarks" class="form-label">Remarks</label>
                <input type="text" class="form-control" id="remarks" name="remarks" value="{{ $extraIncome->remarks }}">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection