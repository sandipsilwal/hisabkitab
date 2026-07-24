@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-dark text-white py-3">
                <h5 class="mb-0 fw-bold">Create Payment Type</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('payment_types.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Payment Type Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="e.g. Cash, eSewa, Fonepay" value="{{ old('name') }}" required style="border-radius: 8px;">
                    </div>

                    <div class="form-check form-switch mb-3 p-3 bg-light rounded" style="padding-left: 3.5rem;">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_default" name="is_default" value="1">
                        <label class="form-check-label fw-semibold text-dark" for="is_default">Set as Default Payment Type</label>
                        <div class="form-text text-muted">Setting this to default will unset any other default payment types automatically.</div>
                    </div>

                    <div class="form-check form-switch mb-3 p-3 bg-light rounded" style="padding-left: 3.5rem;">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_alert" name="is_alert" value="1" {{ old('is_alert') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark" for="is_alert">Set as Alert Payment Type ⚠️</label>
                        <div class="form-text text-muted">Active sessions with this payment type will be highlighted with an alert background color and confirmation check.</div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('payment_types.index') }}" class="btn btn-light border fw-semibold px-4" style="border-radius: 8px;">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary fw-semibold px-4" style="border-radius: 8px;">
                            Save Payment Type
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
