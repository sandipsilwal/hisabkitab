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
<div class="modal fade" id="addSkaterModal" tabindex="-1" aria-labelledby="addSkaterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSkaterModalLabel">Add New Skater</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="skaterName" class="form-label">Skater Name</label>
                    <input type="text" class="form-control" id="skaterName" placeholder="Enter skater's name">
                </div>
                <div class="mb-3">
                    <label for="noOfSkaters" class="form-label">Number of Skaters</label>
                    <input type="number" class="form-control" id="noOfSkaters" placeholder="Enter number of skaters" min="1">
                </div>
                <div class="mb-3">
                    <label for="sessionMinutes" class="form-label">Session Time (Minutes)</label>
                    <input type="number" class="form-control" id="sessionMinutes" placeholder="Enter minutes" min="1">
                </div>
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount (NPR)</label>
                    <input type="number" class="form-control" id="amount" placeholder="Enter amount" min="0">
                </div>
                <div class="mb-3">
                    <label for="paymentMethod" class="form-label">Payment Method</label>
                    <select class="form-control" id="paymentMethod">
                        <option value="cash">Cash</option>
                        <option value="online">Online</option>
                        <option value="unpaid">Unpaid</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveSkater">Save Skater</button>
            </div>
        </div>
    </div>
</div>

<!-- Audio element for notification sound -->
<audio id="notificationSound" src="https://www.soundjay.com/buttons/beep-01a.mp3"></audio>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
<script>
$(document).ready(function() {
    // Save skater via AJAX
    $('#saveSkater').click(function() {
        let name = $('#skaterName').val().trim();
        let noOfSkaters = parseInt($('#noOfSkaters').val());
        let sessionMinutes = parseInt($('#sessionMinutes').val());
        let amount = parseInt($('#amount').val());
        let paymentMethod = $('#paymentMethod').val();

        if (!name || isNaN(noOfSkaters) || noOfSkaters <= 0 || isNaN(sessionMinutes) || sessionMinutes <= 0 || isNaN(amount) || amount < 0) {
            alert('Please fill all fields with valid values.');
            return;
        }

        $.ajax({
            url: '{{ route("skaters.store") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                name: name,
                no_of_skaters: noOfSkaters,
                session_minutes: sessionMinutes,
                amount: amount,
                payment_method: paymentMethod
            },
            success: function(response) {
                if (response.success) {
                    $('#sessionContent').html(response.partialView);
                    $('#addSkaterModal').modal('hide');
                    $('#skaterName, #noOfSkaters, #sessionMinutes, #amount').val('');
                } else {
                    alert('Failed to add skater: ' + response.message);
                }
            },
            error: function(xhr) {
                console.error('Error adding skater:', xhr.responseText);
                alert('Failed to add skater. Please try again.');
            }
        });
    });

    // Start Timer
    $(document).on('click', '.start-timer', function() {
        let $button = $(this);
        let skaterId = $button.data('id');
        $.ajax({
            url: '{{ route("skaters.start", ":id") }}'.replace(':id', skaterId),
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    $('#sessionContent').html(response.partialView);
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

        $.ajax({
            url: '{{ route("skaters.stop", ":id") }}'.replace(':id', skaterId),
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    $('#sessionContent').html(response.partialView);
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
    function startTimer(skaterId, totalSeconds, startTime) {
        let $listItem = $(`#skater-${skaterId}`);
        let $timer = $listItem.find('.timer');
        let interval = setInterval(function() {
            let now = new Date();
            let secondsElapsed = Math.floor((now - new Date(startTime)) / 1000);
            let remainingSeconds = totalSeconds - secondsElapsed;

            if (remainingSeconds <= 0) {
                clearInterval(interval);
                $('#notificationSound')[0].play();
                $.ajax({
                    url: '{{ route("skaters.complete", ":id") }}'.replace(':id', skaterId),
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            $('#sessionContent').html(response.partialView);
                        } else {
                            alert('Failed to complete timer: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error completing timer:', xhr.responseText);
                        alert('Failed to complete timer. Please try again.');
                    }
                });
                return;
            }

            let minutes = Math.floor(remainingSeconds / 60);
            let seconds = remainingSeconds % 60;
            $timer.text(`${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);
        }, 1000);
    }

    // Overtime Timer Logic
    function startOvertime(skaterId, startTime, sessionMinutes, $overtimeTimer) {
        let totalSeconds = Math.floor((new Date() - new Date(startTime)) / 1000) - (sessionMinutes * 60);
        let interval = setInterval(function() {
            let minutes = Math.floor(totalSeconds / 60);
            let seconds = totalSeconds % 60;
            $overtimeTimer.text(`${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);
            totalSeconds++;
        }, 1000);

        $overtimeTimer.closest('li').find('.stop-timer').one('click', function() {
            clearInterval(interval);
        });
    }

    // Initialize timers
    $('.timer').each(function() {
        let $timer = $(this);
        let skaterId = $timer.data('id');
        let totalSeconds = parseInt($timer.data('seconds'));
        let startTime = $timer.data('start-time');
        if (startTime && totalSeconds > 0) {
            startTimer(skaterId, totalSeconds, startTime);
        }
    });

    $('.overtime-timer').each(function() {
        let $timer = $(this);
        let skaterId = $timer.data('id');
        let startTime = $timer.data('start-time');
        let sessionMinutes = $timer.data('session-minutes');
        if (startTime) {
            startOvertime(skaterId, startTime, sessionMinutes, $timer);
        }
    });
});
</script>
@endsection