@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-6">
        <h1 class="mb-4">Edit Account</h1>
        <form action="{{ route('accounts.update', $account) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $account->name }}" required>
            </div>
            <div class="mb-3">
                <label for="balance" class="form-label">Balance</label>
                <input type="number" class="form-control" id="balance" name="balance" value="{{ $account->balance }}" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_default_cash_account" name="is_default_cash_account" {{ $account->is_default_cash_account ? 'checked' : '' }}>
                <label class="form-check-label" for="is_default_cash_account">Default Cash Account</label>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_default_online_account" name="is_default_online_account" {{ $account->is_default_online_account ? 'checked' : '' }}>
                <label class="form-check-label" for="is_default_online_account">Default Online Account</label>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection