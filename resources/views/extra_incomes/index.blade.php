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
            </div>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Title</th>
                    <th>To Account</th>
                    <th>Amount</th>
                    <th>Remarks</th>
                    <th>Status</th>
                    <th>Actions</th>
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
        </table>
    </div>
</div>
@endsection