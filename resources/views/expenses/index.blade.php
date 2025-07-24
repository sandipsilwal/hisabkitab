@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">Expenses</h1>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary mb-3">New Expense</a>
        <form method="GET" action="{{ route('expenses.index') }}" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label for="date_range_filter" class="form-label"> Date</label>
                    <input type="text" value="{{$filter_date_range}}" class="form-control nepali-datepicker date-range" id="date_range_filter" name="date_range_filter" autocomplete="off">
                </div>
                <div class="col-md-3">
                    <label for="account_id" class="form-label">Account</label>
                    <select class="form-control" id="account_id" name="account_id">
                        <option value="">All Accounts</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}" {{$filter_account_id==$account->id?'selected':''}}>
                                {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
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
                <div class="col-md-6 align-self-end">
                    <span class="btn btn-sm total_bg" style="float: right;"><b>Total Amount: {{ number_format($total_amount, 2) }}</b></span>
                </div>
            </div>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="total_bg">Date</th>
                    <th class="total_bg">Title</th>
                    <th class="total_bg">From Account</th>
                    <th class="total_bg">Amount</th>
                    <th class="total_bg">Remarks</th>
                    <th class="total_bg">Status</th>
                    <th class="total_bg">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($expenses as $expense)
                    <tr>
                        <td class="{{ $expense->is_posted ? '' : 'bg-red' }}">{{ $expense->date_bs }}</td>
                        <td class="{{ $expense->is_posted ? '' : 'bg-red' }}">{{ $expense->title }}</td>
                        <td class="{{ $expense->is_posted ? '' : 'bg-red' }}">{{ $expense->fromAccount->name }}</td>
                        <td class="{{ $expense->is_posted ? '' : 'bg-red' }}">{{ $expense->amount }}</td>
                        <td class="{{ $expense->is_posted ? '' : 'bg-red' }}">{{ $expense->remarks }}</td>
                        <td class="{{ $expense->is_posted ? '' : 'bg-red' }}">{{ $expense->is_posted ? 'Posted' : 'Unposted' }}</td>
                        <td class="{{ $expense->is_posted ? '' : 'bg-red' }}">
                            @if (!$expense->is_posted)
                                <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-primary">Edit</a>
                                <form action="{{ route('expenses.post', $expense) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Post</button>
                                </form>
                                <form action="{{ route('expenses.destroy', $expense) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="font-weight-bold total_bg">Total</td>
                    <td class="font-weight-bold total_bg">{{ number_format($total_amount, 2) }}</td>
                    <td class="total_bg" colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="justify-content-center">
        {{ $expenses->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection