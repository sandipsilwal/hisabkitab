@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-dark text-white py-3">
                <h5 class="mb-0 fw-bold">Create Default Time</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('default_times.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="label" class="form-label fw-semibold">Label</label>
                        <input type="text" class="form-control" id="label" name="label" placeholder="e.g. 30 Min, 1 Hour" value="{{ old('label') }}" required style="border-radius: 8px;">
                    </div>

                    <div class="mb-3">
                        <label for="minutes" class="form-label fw-semibold">Minutes</label>
                        <input type="number" class="form-control" id="minutes" name="minutes" placeholder="e.g. 30, 60" value="{{ old('minutes') }}" required min="1" style="border-radius: 8px;">
                    </div>

                    <div class="mb-3">
                        <label for="display_order" class="form-label fw-semibold">Display Order</label>
                        <input type="number" class="form-control" id="display_order" name="display_order" placeholder="e.g. 1, 2" value="{{ old('display_order', 1) }}" required style="border-radius: 8px;">
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('default_times.index') }}" class="btn btn-light border fw-semibold px-4" style="border-radius: 8px;">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary fw-semibold px-4" style="border-radius: 8px;">
                            Save Default Time
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
