@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-6">
        <h1 class="mb-4">Create Account</h1>
        <form action="{{ route('accounts.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="balance" class="form-label">Balance</label>
                <input type="number" class="form-control" id="balance" name="balance" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_default_cash_account" value="1" name="is_default_cash_account">
                <label class="form-check-label" for="is_default_cash_account">Default Cash Account</label>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" value="1" id="is_default_online_account" name="is_default_online_account">
                <label class="form-check-label" for="is_default_online_account">Default Online Account</label>
            </div>
            <button type="submit" class="btn btn-primary">Create</button>
        </form>
    </div>
</div>
@endsection