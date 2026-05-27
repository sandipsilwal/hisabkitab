@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-dark text-white py-3">
                <h5 class="mb-0 fw-bold">Edit Default Time</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('default_times.update', $defaultTime->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="label" class="form-label fw-semibold">Label</label>
                        <input type="text" class="form-control" id="label" name="label" value="{{ old('label', $defaultTime->label) }}" required style="border-radius: 8px;">
                    </div>

                    <div class="mb-3">
                        <label for="minutes" class="form-label fw-semibold">Minutes</label>
                        <input type="number" class="form-control" id="minutes" name="minutes" value="{{ old('minutes', $defaultTime->minutes) }}" required min="1" style="border-radius: 8px;">
                    </div>

                    <div class="mb-3">
                        <label for="display_order" class="form-label fw-semibold">Display Order</label>
                        <input type="number" class="form-control" id="display_order" name="display_order" value="{{ old('display_order', $defaultTime->display_order) }}" required style="border-radius: 8px;">
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('default_times.index') }}" class="btn btn-light border fw-semibold px-4" style="border-radius: 8px;">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-warning fw-semibold px-4" style="border-radius: 8px;">
                            Update Default Time
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
