@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-dark text-white py-3">
                <h5 class="mb-0 fw-bold">Edit Token</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('tokens.update', $token->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Token Name / ID</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $token->name) }}" required style="border-radius: 8px;">
                    </div>

                    <div class="mb-3">
                        <label for="game_type_id" class="form-label fw-semibold">Game Type</label>
                        <select class="form-select" id="game_type_id" name="game_type_id" required style="border-radius: 8px;">
                            @foreach($gameTypes as $gameType)
                                <option value="{{ $gameType->id }}" {{ old('game_type_id', $token->game_type_id) == $gameType->id ? 'selected' : '' }}>{{ $gameType->game_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="display_order" class="form-label fw-semibold">Display Order</label>
                        <input type="number" class="form-control" id="display_order" name="display_order" value="{{ old('display_order', $token->display_order) }}" required style="border-radius: 8px;">
                    </div>

                    <div class="form-check form-switch mb-3 p-3 bg-light rounded" style="padding-left: 3.5rem;">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ $token->is_active ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark" for="is_active">Token is Active</label>
                        <div class="form-text text-muted">Inactive tokens will not appear in the Current Session active play list.</div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('tokens.index') }}" class="btn btn-light border fw-semibold px-4" style="border-radius: 8px;">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-warning fw-semibold px-4" style="border-radius: 8px;">
                            Update Token
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
