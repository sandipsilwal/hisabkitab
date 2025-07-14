@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h1 class="mb-4">Create Transaction</h1>
        <form action="{{ route('transactions.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="date_bs" class="form-label">Date</label>
                <input type="text" class="form-control nepali-datepicker default-today-date" id="nepali_date" name="date_bs" required>
            </div>
            <div id="repeater-container">
                <!-- First Repeater Item (Default Cash Account) -->
                <div class="repeater-item" data-index="0">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="transactions_0_to_account_id" class="form-label">To Account</label>
                            <select class="form-control" id="transactions_0_to_account_id" name="transactions[0][to_account_id]" required>
                                @if ($accounts->isNotEmpty())
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}" {{ $account->is_default_cash_account ? 'selected' : '' }}>
                                            {{ $account->name }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>No accounts available</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="transactions_0_amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="transactions_0_amount" name="transactions[0][amount]" required>
                        </div>
                        <div class="col-md-4">
                            <label for="transactions_0_remarks" class="form-label">Remarks</label>
                            <input type="text" class="form-control" id="transactions_0_remarks" name="transactions[0][remarks]">
                        </div>
                        <div class="col-md-1 align-self-end">
                            <button type="button" class="btn btn-danger remove-repeater">X</button>
                        </div>
                    </div>
                </div>
                <!-- Second Repeater Item (Default Online Account) -->
                <div class="repeater-item" data-index="1">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="transactions_1_to_account_id" class="form-label">To Account</label>
                            <select class="form-control" id="transactions_1_to_account_id" name="transactions[1][to_account_id]" required>
                                @if ($accounts->isNotEmpty())
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}" {{ $account->is_default_online_account ? 'selected' : '' }}>
                                            {{ $account->name }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>No accounts available</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="transactions_1_amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="transactions_1_amount" name="transactions[1][amount]" required>
                        </div>
                        <div class="col-md-4">
                            <label for="transactions_1_remarks" class="form-label">Remarks</label>
                            <input type="text" class="form-control" id="transactions_1_remarks" name="transactions[1][remarks]">
                        </div>
                        <div class="col-md-1 align-self-end">
                            <button type="button" class="btn btn-danger remove-repeater">X</button>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-success mt-3 ms-2">Create</button>
            <button type="button" class="btn btn-primary mt-3 add-repeater" style="float: right;" data-container="repeater-container">+</button>
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