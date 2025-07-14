@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h1 class="mb-4">Edit Transaction</h1>
        <form action="{{ route('transactions.update', $transaction) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="text" class="form-control nepali-datepicker" id="date" name="date_bs" value="{{ $transaction->date_bs }}" required>
            </div>
            <div class="mb-3">
                <label for="to_account_id" class="form-label">To Account</label>
                <select class="form-control" id="to_account_id" name="to_account_id" required>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}" {{ $transaction->to_account_id == $account->id ? 'selected' : '' }}>
                            {{ $account->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" value="{{ $transaction->amount }}" required>
            </div>
            <div class="mb-3">
                <label for="remarks" class="form-label">Remarks</label>
                <input type="text" class="form-control" id="remarks" name="remarks" value="{{ $transaction->remarks }}">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" crossorigin="anonymous"></script>
<script>
    // Repeater functionality
    $('.add-repeater').on('click', function() {
        var container = $('#' + $(this).data('container'));
        var lastItem = container.find('.repeater-item:last');
        var index = parseInt(lastItem.data('index')) + 1;
        var newItem = lastItem.clone();

        // Update indices and IDs
        newItem.attr('data-index', index);
        newItem.find('select, input').each(function() {
            var name = $(this).attr('name').replace(/\[\d+\]/, '[' + index + ']');
            var id = $(this).attr('id').replace(/_\d+_/g, '_' + index + '_');
            $(this).attr('name', name);
            $(this).attr('id', id);
            if ($(this).is('select')) {
                $(this).find('option').removeAttr('selected');
                // Set default online account for new rows
                @if ($accounts->isNotEmpty())
                    @foreach ($accounts as $account)
                        if ($(this).find('option[value="{{ $account->id }}"]').length) {
                            @if ($account->is_default_online_account)
                                $(this).find('option[value="{{ $account->id }}"]').prop('selected', true);
                            @endif
                        }
                    @endforeach
                @endif
            }
        });

        // Append new item
        container.append(newItem);
    });

    $(document).on('click', '.remove-repeater', function() {
        if ($('#repeater-container .repeater-item').length > 2) {
            $(this).closest('.repeater-item').remove();
        }
    });
</script>
@endsection