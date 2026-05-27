@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-dark text-white py-3">
                <h5 class="mb-0 fw-bold">Edit Package</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('packages.update', $package->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="game_type_id" class="form-label fw-semibold">Game Type</label>
                        <select class="form-select" id="game_type_id" name="game_type_id" required style="border-radius: 8px;">
                            @foreach($gameTypes as $gameType)
                                <option value="{{ $gameType->id }}" {{ old('game_type_id', $package->game_type_id) == $gameType->id ? 'selected' : '' }}>{{ $gameType->game_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="time_per_day" class="form-label fw-semibold">Time Per Day (Minutes)</label>
                        <input type="number" class="form-control" id="time_per_day" name="time_per_day" value="{{ old('time_per_day', $package->time_per_day) }}" required min="1" style="border-radius: 8px;">
                    </div>

                    <div class="mb-3">
                        <label for="no_of_days" class="form-label fw-semibold">Number of Days</label>
                        <input type="number" class="form-control" id="no_of_days" name="no_of_days" value="{{ old('no_of_days', $package->no_of_days) }}" required min="1" style="border-radius: 8px;">
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label fw-semibold">Package Amount (Rs.)</label>
                        <input type="number" class="form-control" id="amount" name="amount" value="{{ old('amount', $package->amount) }}" required min="0" style="border-radius: 8px;">
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('packages.index') }}" class="btn btn-light border fw-semibold px-4" style="border-radius: 8px;">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-warning fw-semibold px-4" style="border-radius: 8px;">
                            Update Package
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
