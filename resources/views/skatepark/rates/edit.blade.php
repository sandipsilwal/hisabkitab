@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-dark text-white py-3">
                <h5 class="mb-0 fw-bold">Edit Pricing Rate</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('rates.update', $rate->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="game_type_id" class="form-label fw-semibold">Game Type</label>
                        <select class="form-select" id="game_type_id" name="game_type_id" required style="border-radius: 8px;">
                            @foreach($gameTypes as $gameType)
                                <option value="{{ $gameType->id }}" {{ old('game_type_id', $rate->game_type_id) == $gameType->id ? 'selected' : '' }}>{{ $gameType->game_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="default_time_id" class="form-label fw-semibold">Duration (Default Time)</label>
                        <select class="form-select" id="default_time_id" name="default_time_id" required style="border-radius: 8px;">
                            @foreach($defaultTimes as $defaultTime)
                                <option value="{{ $defaultTime->id }}" {{ old('default_time_id', $rate->default_time_id) == $defaultTime->id ? 'selected' : '' }}>{{ $defaultTime->label }} ({{ $defaultTime->minutes }} Min)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="rate" class="form-label fw-semibold">Rate Amount (Rs.)</label>
                        <input type="number" class="form-control" id="rate" name="rate" value="{{ old('rate', $rate->rate) }}" required min="0" style="border-radius: 8px;">
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('rates.index') }}" class="btn btn-light border fw-semibold px-4" style="border-radius: 8px;">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-warning fw-semibold px-4" style="border-radius: 8px;">
                            Update Rate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
