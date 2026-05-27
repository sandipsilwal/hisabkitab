@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">{{ __('Skate Park Players') }}</h5>
        <button type="button" class="btn btn-primary btn-sm px-3 fw-semibold" style="border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#addPlayerModal">
            + {{ __('Add New Player') }}
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 text-start py-3" style="width: 100px;">{{ __('ID') }}</th>
                        <th class="text-start py-3">{{ __('Name') }}</th>
                        <th class="text-start py-3">{{ __('Contact') }}</th>
                        <th class="text-start py-3">{{ __('Address') }}</th>
                        <th class="text-center py-3" style="width: 180px;">{{ __('Active Packages') }}</th>
                        <th class="pe-4 text-end py-3" style="width: 250px;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($players as $player)
                        <tr>
                            <td class="ps-4 text-start font-monospace text-muted">{{ $player->id }}</td>
                            <td class="text-start fw-bold text-dark">{{ $player->name }}</td>
                            <td class="text-start font-monospace text-muted">{{ $player->contact ?? '-' }}</td>
                            <td class="text-start text-secondary">{{ $player->address ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1.5 fw-semibold" style="border-radius: 30px;">
                                    {{ $player->player_packages_count }} {{ __('Purchased') }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('players.show', $player->id) }}" class="btn btn-primary btn-sm me-1" style="border-radius: 6px; font-weight: 500;">
                                    {{ __('Profile & Packages') }}
                                </a>
                                <button type="button" class="btn btn-outline-warning btn-sm me-1" style="border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#editPlayerModal{{ $player->id }}">
                                    {{ __('Edit') }}
                                </button>
                                <form action="{{ route('players.destroy', $player->id) }}" method="POST" class="d-inline" onsubmit="confirmDeletePlayer(event, this)">
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
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fs-1 d-block mb-2 text-secondary">👥</i>
                                {{ __('No players registered yet. Click "+ Add New Player" to begin.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add New Player -->
<div class="modal fade" id="addPlayerModal" tabindex="-1" aria-labelledby="addPlayerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold" id="addPlayerModalLabel">{{ __('Add New Player') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('players.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="add-name" class="form-label fw-semibold">{{ __('Player Name') }}</label>
                        <input type="text" class="form-control" id="add-name" name="name" placeholder="{{ __('e.g. John Doe') }}" value="{{ old('name') }}" required style="border-radius: 8px;">
                    </div>

                    <div class="mb-3">
                        <label for="add-contact" class="form-label fw-semibold">{{ __('Contact Number') }}</label>
                        <input type="text" class="form-control" id="add-contact" name="contact" placeholder="{{ __('e.g. 98XXXXXXXX') }}" value="{{ old('contact') }}" style="border-radius: 8px;">
                    </div>

                    <div class="mb-3">
                        <label for="add-address" class="form-label fw-semibold">{{ __('Address') }}</label>
                        <input type="text" class="form-control" id="add-address" name="address" placeholder="{{ __('e.g. Kathmandu, Lalitpur') }}" value="{{ old('address') }}" style="border-radius: 8px;">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary fw-semibold px-4" style="border-radius: 8px;" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary fw-semibold px-4" style="border-radius: 8px;">{{ __('Save Player') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modals: Edit Player for each item -->
@foreach($players as $player)
<div class="modal fade" id="editPlayerModal{{ $player->id }}" tabindex="-1" aria-labelledby="editPlayerModalLabel{{ $player->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold" id="editPlayerModalLabel{{ $player->id }}">{{ __('Edit Player') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('players.update', $player->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="edit-name-{{ $player->id }}" class="form-label fw-semibold">{{ __('Player Name') }}</label>
                        <input type="text" class="form-control" id="edit-name-{{ $player->id }}" name="name" value="{{ old('name', $player->name) }}" required style="border-radius: 8px;">
                    </div>

                    <div class="mb-3">
                        <label for="edit-contact-{{ $player->id }}" class="form-label fw-semibold">{{ __('Contact Number') }}</label>
                        <input type="text" class="form-control" id="edit-contact-{{ $player->id }}" name="contact" value="{{ old('contact', $player->contact) }}" style="border-radius: 8px;">
                    </div>

                    <div class="mb-3">
                        <label for="edit-address-{{ $player->id }}" class="form-label fw-semibold">{{ __('Address') }}</label>
                        <input type="text" class="form-control" id="edit-address-{{ $player->id }}" name="address" value="{{ old('address', $player->address) }}" style="border-radius: 8px;">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary fw-semibold px-4" style="border-radius: 8px;" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary fw-semibold px-4" style="border-radius: 8px;">{{ __('Update Player') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Modal: Delete Confirmation -->
<div class="modal fade" id="deletePlayerConfirmModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold">{{ __('Confirm Deletion') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-start gap-3">
                    <span class="fs-2">🗑️</span>
                    <div>
                        <p class="mb-0 text-dark fw-medium" style="font-size: 0.95rem;">
                            {{ __('Are you sure you want to delete this player? All linked packages and payments will also be deleted.') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top-0 py-2.5">
                <button type="button" class="btn btn-sm btn-secondary fw-semibold px-3 py-1.5" style="border-radius: 8px;" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-sm btn-danger fw-semibold px-3 py-1.5" style="border-radius: 8px;" id="confirm-delete-btn" onclick="submitDeleteForm()">{{ __('Delete') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
let formToSubmit = null;

function confirmDeletePlayer(event, form) {
    event.preventDefault();
    formToSubmit = form;
    const modalEl = document.getElementById('deletePlayerConfirmModal');
    const deleteModal = new bootstrap.Modal(modalEl);
    deleteModal.show();
}

function submitDeleteForm() {
    if (formToSubmit) {
        formToSubmit.submit();
    }
}
</script>
@endsection
