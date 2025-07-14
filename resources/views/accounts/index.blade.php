@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="mb-4">Accounts</h1>
        <a href="{{ route('accounts.create') }}" class="btn btn-primary mb-3">Create New Account</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Balance</th>
                    <th>Default Cash</th>
                    <th>Default Online</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($accounts as $account)
                    <tr>
                        <td>{{ $account->name }}</td>
                        <td>{{ $account->balance }}</td>
                        <td>{{ $account->is_default_cash_account ? 'Yes' : 'No' }}</td>
                        <td>{{ $account->is_default_online_account ? 'Yes' : 'No' }}</td>
                        <td>
                            <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection