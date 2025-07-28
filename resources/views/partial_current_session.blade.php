<div class="row">
    <!-- Left Half: Active and Playing Skaters -->
    <div class="col-md-6">
        <h6>Active Skaters</h6>
        <ul class="list-group mb-3">
            @forelse ($activeSkaters as $skaterHistory)
                <li class="list-group-item d-flex justify-content-between align-items-center" id="skater-{{ $skaterHistory->id }}">
                    <span>
                        {{ $skaterHistory->skater->name }} ({{ $skaterHistory->no_of_skaters }} skaters, NPR {{ $skaterHistory->amount }}, {{ $skaterHistory->payment_method }}) - 
                        <span class="timer" data-id="{{ $skaterHistory->id }}" data-seconds="{{ $skaterHistory->session_minutes * 60 }}">{{ $skaterHistory->session_minutes }}:00</span>
                    </span>
                    <button class="btn btn-success btn-sm start-timer" data-id="{{ $skaterHistory->id }}">Start</button>
                </li>
            @empty
                <li class="list-group-item">No active skaters</li>
            @endforelse
        </ul>
        <h6>Playing Skaters</h6>
        <ul class="list-group">
            @forelse ($playingSkaters as $skaterHistory)
                @php
                    $remainingSeconds = $skaterHistory->start_time
                        ? max(0, ($skaterHistory->session_minutes * 60) - floor((now()->timestamp - strtotime($skaterHistory->start_time)) / 1000))
                        : $skaterHistory->session_minutes * 60;
                    $minutes = floor($remainingSeconds / 60);
                    $seconds = $remainingSeconds % 60;
                @endphp
                @if ($remainingSeconds > 0)
                    <li class="list-group-item d-flex justify-content-between align-items-center" id="skater-{{ $skaterHistory->id }}">
                        <span>
                            {{ $skaterHistory->skater->name }} ({{ $skaterHistory->no_of_skaters }} skaters, NPR {{ $skaterHistory->amount }}, {{ $skaterHistory->payment_method }}) - 
                            <span class="timer" data-id="{{ $skaterHistory->id }}" data-seconds="{{ $remainingSeconds }}" data-start-time="{{ $skaterHistory->start_time }}">{{ $minutes }}:{{ str_pad($seconds, 2, '0', STR_PAD_LEFT) }}</span>
                        </span>
                        <button class="btn btn-success btn-sm start-timer" data-id="{{ $skaterHistory->id }}" disabled>Running</button>
                    </li>
                @endif
            @empty
                <li class="list-group-item">No playing skaters</li>
            @endforelse
        </ul>
    </div>

    <!-- Right Half: Overtime Skaters -->
    <div class="col-md-6">
        <h6>Overtime Skaters</h6>
        <ul class="list-group">
            @forelse ($overTimeSkaters as $skaterHistory)
                @php
                    $overtimeSeconds = floor((now()->timestamp - strtotime($skaterHistory->start_time)) / 1000) - ($skaterHistory->session_minutes * 60);
                    $minutes = floor($overtimeSeconds / 60);
                    $seconds = $overtimeSeconds % 60;
                @endphp
                <li class="list-group-item d-flex justify-content-between align-items-center" id="skater-{{ $skaterHistory->id }}">
                    <span>
                        {{ $skaterHistory->skater->name }} ({{ $skaterHistory->no_of_skaters }} skaters, NPR {{ $skaterHistory->amount }}, {{ $skaterHistory->payment_method }}) - 
                        <span class="overtime-timer" data-id="{{ $skaterHistory->id }}" data-start-time="{{ $skaterHistory->start_time }}" data-session-minutes="{{ $skaterHistory->session_minutes }}">{{ $minutes }}:{{ str_pad($seconds, 2, '0', STR_PAD_LEFT) }}</span>
                    </span>
                    <button class="btn btn-danger btn-sm stop-timer" data-id="{{ $skaterHistory->id }}">Stop</button>
                </li>
            @empty
                <li class="list-group-item">No overtime skaters</li>
            @endforelse
        </ul>
    </div>
</div>