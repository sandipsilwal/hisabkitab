@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-dark text-white py-3">
                <h5 class="mb-0 fw-bold">Edit Game Type</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('game_types.update', $gameType->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="game_name" class="form-label fw-semibold">Game Name</label>
                        <input type="text" class="form-control" id="game_name" name="game_name" value="{{ old('game_name', $gameType->game_name) }}" required style="border-radius: 8px;">
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('game_types.index') }}" class="btn btn-light border fw-semibold px-4" style="border-radius: 8px;">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-warning fw-semibold px-4" style="border-radius: 8px;">
                            Update Game Type
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
