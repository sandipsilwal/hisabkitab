@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="mb-4">Accounts</h1>
        <div class="row">
            <div class="col-md-6 align-self-end">
                <a href="{{ route('accounts.create') }}" class="btn btn-primary mb-3">Create New Account</a>
            </div>
            <div class="col-md-6">
                <span class="btn btn-sm total_bg" style="float: right;"><b>Total Amount: {{ number_format($total_amount, 2) }}</b></span>
            </div>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="total_bg">Name</th>
                    <th class="total_bg">Balance</th>
                    <th class="total_bg">Default Cash</th>
                    <th class="total_bg">Default Online</th>
                    <th class="total_bg">Actions</th>
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
            <tfoot>
                <tr>
                    <td class="font-weight-bold total_bg">Total</td>
                    <td class="font-weight-bold total_bg">{{ number_format($total_amount, 2) }}</td>
                    <td class="total_bg" colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection