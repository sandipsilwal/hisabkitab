@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">Extra Incomes</h1>
        <a href="{{ route('extra_incomes.create') }}" class="btn btn-primary mb-3">New Income</a>
        <form method="GET" action="{{ route('extra_incomes.index') }}" class="mb-4">
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
                    <th class="total_bg">To Account</th>
                    <th class="total_bg">Amount</th>
                    <th class="total_bg">Remarks</th>
                    <th class="total_bg">Status</th>
                    <th class="total_bg">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($extraIncomes as $extraIncome)
                    <tr>
                        <td class="{{ $extraIncome->is_posted ? '' : 'bg-red' }}">{{ $extraIncome->date_bs }}</td>
                        <td class="{{ $extraIncome->is_posted ? '' : 'bg-red' }}">{{ $extraIncome->title }}</td>
                        <td class="{{ $extraIncome->is_posted ? '' : 'bg-red' }}">{{ $extraIncome->toAccount->name }}</td>
                        <td class="{{ $extraIncome->is_posted ? '' : 'bg-red' }}">{{ $extraIncome->amount }}</td>
                        <td class="{{ $extraIncome->is_posted ? '' : 'bg-red' }}">{{ $extraIncome->remarks }}</td>
                        <td class="{{ $extraIncome->is_posted ? '' : 'bg-red' }}">{{ $extraIncome->is_posted ? 'Posted' : 'Unposted' }}</td>
                        <td class="{{ $extraIncome->is_posted ? '' : 'bg-red' }}">
                            @if (!$extraIncome->is_posted)
                                <a href="{{ route('extra_incomes.edit', $extraIncome) }}" class="btn btn-sm btn-primary">Edit</a>
                                <form action="{{ route('extra_incomes.post', $extraIncome) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Post</button>
                                </form>
                                <form action="{{ route('extra_incomes.destroy', $extraIncome) }}" method="POST" style="display:inline;">
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
        {{ $extraIncomes->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection