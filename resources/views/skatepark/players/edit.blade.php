@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-dark text-white py-3">
                <h5 class="mb-0 fw-bold">Edit Player</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('players.update', $player->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Player Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $player->name) }}" required style="border-radius: 8px;">
                    </div>

                    <div class="mb-3">
                        <label for="contact" class="form-label fw-semibold">Contact Number</label>
                        <input type="text" class="form-control" id="contact" name="contact" value="{{ old('contact', $player->contact) }}" style="border-radius: 8px;">
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label fw-semibold">Address</label>
                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $player->address) }}" style="border-radius: 8px;">
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('players.index') }}" class="btn btn-light border fw-semibold px-4" style="border-radius: 8px;">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-warning fw-semibold px-4" style="border-radius: 8px;">
                            Update Player
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
