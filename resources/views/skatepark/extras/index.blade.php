@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">{{ __('Extras Management') }}</h5>
        <button type="button" class="btn btn-primary btn-sm px-3 fw-semibold" style="border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#addExtraModal">
            + {{ __('Add New Extra') }}
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 text-start py-3" style="width: 100px;">{{ __('ID') }}</th>
                        <th class="text-start py-3">{{ __('Title') }}</th>
                        <th class="text-start py-3">{{ __('Amount') }}</th>
                        <th class="pe-4 text-end py-3" style="width: 250px;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($extras as $extra)
                        <tr>
                            <td class="ps-4 text-start font-monospace text-muted">{{ $extra->id }}</td>
                            <td class="text-start fw-bold text-dark">
                                {{ $extra->title }}
                                @if($extra->is_default)
                                    <span class="badge bg-primary ms-2">{{ __('Default') }}</span>
                                @endif
                            </td>
                            <td class="text-start text-dark">Rs. {{ number_format($extra->amount) }}</td>
                            <td class="pe-4 text-end">
                                <button type="button" class="btn btn-outline-warning btn-sm me-1" style="border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#editExtraModal{{ $extra->id }}">
                                    {{ __('Edit') }}
                                </button>
                                <form action="{{ route('extras.destroy', $extra->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this extra?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" style="border-radius: 6px;">
                                        {{ __('Delete') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fs-1 d-block mb-2 text-secondary">🏷️</i>
                                {{ __('No extras found. Click "+ Add New Extra" to add.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add New Extra -->
<div class="modal fade" id="addExtraModal" tabindex="-1" aria-labelledby="addExtraModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold" id="addExtraModalLabel">{{ __('Add New Extra') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('extras.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="add-title" class="form-label fw-semibold">{{ __('Title') }}</label>
                        <input type="text" class="form-control" id="add-title" name="title" placeholder="{{ __('e.g. Water Bottle') }}" value="{{ old('title') }}" required style="border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label for="add-amount" class="form-label fw-semibold">{{ __('Amount (Rs.)') }}</label>
                        <input type="number" class="form-control" id="add-amount" name="amount" placeholder="{{ __('e.g. 50') }}" value="{{ old('amount') }}" required min="0" style="border-radius: 8px;">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="add-is-default" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="add-is-default">{{ __('Selected by Default') }}</label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary fw-semibold px-4" style="border-radius: 8px;" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary fw-semibold px-4" style="border-radius: 8px;">{{ __('Save Extra') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modals: Edit Extra for each item -->
@foreach($extras as $extra)
<div class="modal fade" id="editExtraModal{{ $extra->id }}" tabindex="-1" aria-labelledby="editExtraModalLabel{{ $extra->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold" id="editExtraModalLabel{{ $extra->id }}">{{ __('Edit Extra') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('extras.update', $extra->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="edit-title-{{ $extra->id }}" class="form-label fw-semibold">{{ __('Title') }}</label>
                        <input type="text" class="form-control" id="edit-title-{{ $extra->id }}" name="title" value="{{ old('title', $extra->title) }}" required style="border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label for="edit-amount-{{ $extra->id }}" class="form-label fw-semibold">{{ __('Amount (Rs.)') }}</label>
                        <input type="number" class="form-control" id="edit-amount-{{ $extra->id }}" name="amount" value="{{ old('amount', $extra->amount) }}" required min="0" style="border-radius: 8px;">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edit-is-default-{{ $extra->id }}" name="is_default" value="1" {{ old('is_default', $extra->is_default) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="edit-is-default-{{ $extra->id }}">{{ __('Selected by Default') }}</label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary fw-semibold px-4" style="border-radius: 8px;" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary fw-semibold px-4" style="border-radius: 8px;">{{ __('Update Extra') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
