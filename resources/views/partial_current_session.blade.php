<div class="row">
    <!-- Left Half: Active and Playing Skaters -->
    <div class="col-md-6">
        <h6>Active Skaters</h6>
        <ul class="list-group mb-3">
            @forelse ($activeSkaters as $activeSkater)
                <li class="list-group-item d-flex justify-content-between align-items-center" id="skater-{{ $activeSkater->id }}">
                    <span>
                        {{ $activeSkater->skater->name }} ({{ $activeSkater->no_of_skaters }} skaters, NPR {{ $activeSkater->amount }}, {{ $activeSkater->payment_method }}) - 
                        <span class="active-timer" id="active-timer-{{$activeSkater->id}}" data-id="{{ $activeSkater->id }}" data-seconds="{{ $activeSkater->session_minutes * 60 }}" data-session-minutes="{{ $activeSkater->session_minutes }}">{{ $activeSkater->session_minutes }}:00</span>
                    </span>
                    <button class="btn btn-success btn-sm start-timer" id="start-button-{{ $activeSkater->id }}" data-id="{{ $activeSkater->id }}">Start</button>
                </li>
            @empty
            @endforelse
            @forelse ($playingSkaters as $playingSkater)
                @if ($playingSkater->remaining_seconds > 0)
                    <li class="list-group-item d-flex justify-content-between align-items-center" id="skater-{{ $playingSkater->id }}">
                        <span>
                            {{ $playingSkater->skater->name }} ({{ $playingSkater->no_of_skaters }} skaters, NPR {{ $playingSkater->amount }}, {{ $playingSkater->payment_method }}) - 
                            <span class="playing-timer" id="playing-timer-{{$playingSkater->id}}" data-id="{{ $playingSkater->id }}" data-seconds="{{ $playingSkater->remaining_seconds }}" data-start-time="{{ $playingSkater->start_time }}" data-session-minutes="{{ $playingSkater->session_minutes }}">{{ floor($playingSkater->remaining_seconds / 60) }}:{{ str_pad($playingSkater->remaining_seconds % 60, 2, '0', STR_PAD_LEFT) }}</span>
                        </span>
                        <button class="btn btn-success btn-sm start-timer" data-id="{{ $playingSkater->id }}" disabled>Running</button>
                    </li>
                @endif
            @empty
            @endforelse
        </ul>
        <!-- <ul class="list-group">
        </ul> -->
    </div>

    <!-- Right Half: Overtime Skaters -->
    <div class="col-md-6">
        <h6>Overtime Skaters</h6>
        <ul class="list-group">
            @forelse ($overTimeSkaters as $overTimeSkater)
                @if ($overTimeSkater->overtime_seconds > 0)
                    <li class="list-group-item d-flex justify-content-between align-items-center" id="skater-{{ $overTimeSkater->id }}">
                        <span>
                            {{ $overTimeSkater->skater->name }} ({{ $overTimeSkater->no_of_skaters }} skaters, NPR {{ $overTimeSkater->amount }}, {{ $overTimeSkater->payment_method }}) - 
                            <span class="overtime-timer" id="overtime-timer-{{$overTimeSkater->id}}" data-id="{{ $overTimeSkater->id }}" data-overtime_seconds="{{ $overTimeSkater->overtime_seconds }}" data-session-minutes="{{ $overTimeSkater->session_minutes }}">{{ floor($overTimeSkater->overtime_seconds / 60) }}:{{ str_pad($overTimeSkater->overtime_seconds % 60, 2, '0', STR_PAD_LEFT) }}</span>
                        </span>
                        <button class="btn btn-danger btn-sm stop-timer" id="stop-button-{{ $overTimeSkater->id }}" data-id="{{ $overTimeSkater->id }}">Stop</button>
                    </li>
                @endif
            @empty
                <li class="list-group-item">No overtime skaters</li>
            @endforelse
        </ul>
    </div>
</div>