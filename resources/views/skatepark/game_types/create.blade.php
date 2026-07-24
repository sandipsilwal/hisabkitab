@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-dark text-white py-3">
                <h5 class="mb-0 fw-bold">Create Game Type</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('game_types.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="game_name" class="form-label fw-semibold">Game Name</label>
                        <input type="text" class="form-control" id="game_name" name="game_name" placeholder="e.g. Skateboarding, Scooter" value="{{ old('game_name') }}" required style="border-radius: 8px;">
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_default" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_default">Set as Default Game Type</label>
                        <div class="form-text text-muted" style="font-size: 0.78rem;">Setting this as default will unmark any other default game type.</div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('game_types.index') }}" class="btn btn-light border fw-semibold px-4" style="border-radius: 8px;">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary fw-semibold px-4" style="border-radius: 8px;">
                            Save Game Type
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
