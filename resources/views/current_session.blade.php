@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title">Current Skating Session</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSkaterModal">Add Skater</button>
            </div>
            <div class="card-body" id="sessionContent">
                {!! $partialView !!}
            </div>
        </div>
    </div>
</div>

<!-- Add Skater Modal -->
<div class="modal fade bd-example-modal-lg"" id="addSkaterModal" tabindex="-1" aria-labelledby="addSkaterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSkaterModalLabel">Add New Skater</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="skater_add_form" action="{{ route('skaters.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="skaterName" class="form-label">Skater Name</label>
                            <input type="text" class="form-control" name="name" id="skaterName" placeholder="Enter skater's name">
                        </div>
                        <div class="col-md-6">
                            <label for="sessionMinutes" class="form-label">Session Time (Minutes)</label>&emsp;<span class="default-times selected-time">30</span> &nbsp;&nbsp;<span class="default-times">60</span>&nbsp;&nbsp;<span class="default-times">120</span>
                            <input type="number" name="session_minutes" class="form-control" value="30" id="sessionMinutes" placeholder="Enter minutes" min="1">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="noOfSkaters" class="form-label">Number of Skaters</label>
                            <input type="number" name="no_of_skaters" class="form-control" id="noOfSkaters" value="1" placeholder="Enter number of skaters" min="1">
                        </div>
                        <div class="col-md-6">
                            <label for="amount" class="form-label">Amount (NPR)</label>
                            <input type="number" name="amount" class="form-control" id="amount" value="100" placeholder="Enter amount" min="0">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="paymentMethod" class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-control" id="paymentMethod">
                                <option value="cash">Cash</option>
                                <option value="online">Online</option>
                                <option value="unpaid">Unpaid</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveSkater">Save Skater</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Audio element for notification sound -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
<script>
$(document).ready(function() {
    $(document).on('click', '.default-times', function () {
        $('.default-times').removeClass('selected-time'); // remove from all
        $(this).addClass('selected-time'); // add to clicked
        $('#sessionMinutes').val(parseInt($(this).html()));
        $('#amount').val(parseInt(parseInt($(this).html())*3.34)*$('#noOfSkaters').val());
    });
    $(document).on('keyup', '#noOfSkaters', function () {
        $('#amount').val(parseInt(parseInt($('#sessionMinutes').val())*3.34)*$('#noOfSkaters').val());
    });
    $(document).on('keyup', '#sessionMinutes', function () {
        $('#amount').val(parseInt(parseInt($('#sessionMinutes').val())*3.34)*$('#noOfSkaters').val());
    });
    $('#addSkaterModal').on('shown.bs.modal', function () {
        $('#skaterName').focus();
    });

    // Save skater via AJAX
    // $('#saveSkater').click(function() {
    //     let name = $('#skaterName').val().trim();
    //     let noOfSkaters = parseInt($('#noOfSkaters').val());
    //     let sessionMinutes = parseInt($('#sessionMinutes').val());
    //     let amount = parseInt($('#amount').val());
    //     let paymentMethod = $('#paymentMethod').val();

    //     if (!name || isNaN(noOfSkaters) || noOfSkaters <= 0 || isNaN(sessionMinutes) || sessionMinutes <= 0 || isNaN(amount) || amount < 0) {
    //         alert('Please fill all fields with valid values.');
    //         return;
    //     }

    //     $.ajax({
    //         url: '{{ route("skaters.store") }}',
    //         method: 'POST',
    //         data: {
    //             _token: '{{ csrf_token() }}',
    //             name: name,
    //             no_of_skaters: noOfSkaters,
    //             session_minutes: sessionMinutes,
    //             amount: amount,
    //             payment_method: paymentMethod
    //         },
    //         success: function(response) {
    //             if (response.success) {
    //                 location.reload();
    //                 // $('#sessionContent').html(response.partialView);
    //                 // $('#addSkaterModal').modal('hide');
    //                 // $('#skaterName, #noOfSkaters, #sessionMinutes, #amount').val('');
    //             } else {
    //                 alert('Failed to add skater: ' + response.message);
    //             }
    //         },
    //         error: function(xhr) {
    //             console.error('Error adding skater:', xhr.responseText);
    //             alert('Failed to add skater. Please try again.');
    //         }
    //     });
    // });

    // Start Timer
    $(document).on('click', '.start-timer', function() {
        let $button = $(this);
        let skaterId = $button.data('id');
        $(`#skater-${skaterId}`).hide();
        $.ajax({
            url: '{{ route("skaters.start", ":id") }}'.replace(':id', skaterId),
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    location.reload();
                    // $('#sessionContent').html(response.partialView);
                    // timeStarter();
                } else {
                    alert('Failed to start timer: ' + response.message);
                }
            },
            error: function(xhr) {
                console.error('Error starting timer:', xhr.responseText);
                alert('Failed to start timer. Please try again.');
            }
        });
    });

    // Stop Timer
    $(document).on('click', '.stop-timer', function() {
        let $button = $(this);
        let skaterId = $button.data('id');
        $(`#skater-${skaterId}`).hide();
        $.ajax({
            url: '{{ route("skaters.stop", ":id") }}'.replace(':id', skaterId),
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    location.reload();
                    // $('#sessionContent').html(response.partialView);
                    // timeStarter();
                } else {
                    alert('Failed to stop timer: ' + response.message);
                }
            },
            error: function(xhr) {
                console.error('Error stopping timer:', xhr.responseText);
                alert('Failed to stop timer. Please try again.');
            }
        });
    });

    // Timer Logic
    function startTimer(skaterId, remainingSeconds) {
        let $timer = $(`#playing-timer-${skaterId}`);
        // Skip if timer is already cleared or processed
        // if ($timer.data('interval-cleared')) {
        //     return;
        // }

        let interval = setInterval(function() {
            if (remainingSeconds <= 0) {
                // clearInterval(interval); // Clear interval immediately
                location.reload();
                // $timer.data('interval-cleared', true); // Mark as processed
                // $.ajax({
                //     url: '{{ route("skaters.overTime", ":id") }}'.replace(':id', skaterId),
                //     method: 'POST',
                //     data: { _token: '{{ csrf_token() }}' },
                //     success: function(response) {
                //         if (response.success) {
                //             $('#sessionContent').html(response.partialView);
                //         } else {
                //             console.error('Overtime update failed for skater ' + skaterId + ':', response.message);
                //             alert('Cannot update overtime for skater ' + skaterId + ': ' + response.message);
                //         }
                //     },
                //     error: function(xhr) {
                //         console.error('Error completing timer for skater ' + skaterId + ':', xhr.responseText);
                //         alert('Failed to complete timer for skater ' + skaterId + '. Please try again.');
                //     }
                // });
                // return;
            }
            remainingSeconds--;
            let minutes = Math.floor(remainingSeconds / 60);
            let seconds = remainingSeconds % 60;
            $(`#playing-timer-${skaterId}`).text(`${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);
        }, 1000);
    }

    // Overtime Timer Logic
    function startOvertime(skaterId, overtime_seconds) {
        let $listItem = $(`#skater-${skaterId}`);
        let $timer = $listItem.find('.overtime-timer');
        let interval = setInterval(function() {
            overtime_seconds++;
            let minutes = Math.floor(overtime_seconds / 60);
            let seconds = parseInt(overtime_seconds % 60);
            $timer.text(`${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);
        }, 1000);

        $timer.closest('li').find('.stop-timer').one('click', function() {
            clearInterval(interval);
        });
    }
    timeStarter();
    // Initialize timers
    function timeStarter(){
        $('.playing-timer').each(function() {
            let $timer = $(this);
            let skaterId = $timer.data('id');
            let startTime = $timer.data('start-time');
            let remainingSeconds = parseInt($timer.data('seconds'));
            if (remainingSeconds > 0 && startTime) {
                startTimer(skaterId, remainingSeconds);
            }
        });
        $('.overtime-timer').each(function() {
            let $timer = $(this);
            let skaterId = $timer.data('id');
            let overtime_seconds = $timer.data('overtime_seconds');
            if (overtime_seconds) {
                startOvertime(skaterId, overtime_seconds);
            }
        });
    }
});
</script>
@endsection