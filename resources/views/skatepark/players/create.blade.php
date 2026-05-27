@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-dark text-white py-3">
                <h5 class="mb-0 fw-bold">Add New Player</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('players.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Player Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="e.g. John Doe" value="{{ old('name') }}" required style="border-radius: 8px;">
                    </div>

                    <div class="mb-3">
                        <label for="contact" class="form-label fw-semibold">Contact Number</label>
                        <input type="text" class="form-control" id="contact" name="contact" placeholder="e.g. 98XXXXXXXX" value="{{ old('contact') }}" style="border-radius: 8px;">
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label fw-semibold">Address</label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="e.g. Kathmandu, Lalitpur" value="{{ old('address') }}" style="border-radius: 8px;">
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('players.index') }}" class="btn btn-light border fw-semibold px-4" style="border-radius: 8px;">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary fw-semibold px-4" style="border-radius: 8px;">
                            Save Player
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
