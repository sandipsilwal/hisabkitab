@extends('layouts.app')

@section('content')
<!-- Top Header: Player Details Banner Card -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
    <div class="card-body p-4 bg-white">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <!-- Left: Player Profile Info -->
            <div class="d-flex align-items-center">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-2 shadow-sm me-3" style="width: 64px; height: 64px; min-width: 64px;">
                    {{ strtoupper(substr($player->name, 0, 1)) }}
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h3 class="mb-0 fw-bold text-dark">{{ $player->name }}</h3>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1" style="border-radius: 20px;">Player #{{ $player->id }}</span>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3 text-muted small">
                        <span>📞 Contact: <strong class="text-dark font-monospace">{{ $player->contact ?? 'Not provided' }}</strong></span>
                        <span>📍 Address: <strong class="text-dark">{{ $player->address ?? 'Not provided' }}</strong></span>
                        <span>📦 Total Packages: <strong class="text-dark">{{ $player->playerPackages->count() }}</strong></span>
                    </div>
                </div>
            </div>
            
            <!-- Right: Profile Action Buttons -->
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('players.edit', $player->id) }}" class="btn btn-outline-secondary fw-semibold px-3" style="border-radius: 8px;">
                    ✏️ Edit Profile
                </a>
                <button type="button" class="btn btn-success fw-bold px-4 py-2 shadow-sm" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#addPackageModal">
                    + Add Package
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Main Body: Purchased Packages Tabular List -->
<div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">Purchased Packages</h5>
        <span class="badge bg-secondary-subtle text-white border border-secondary px-3 py-1.5" style="border-radius: 20px;">
            Total: {{ $player->playerPackages->count() }} Packages
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 text-start py-3" style="width: 70px;">#</th>
                        <th class="text-start py-3">Game / Subscription Package</th>
                        <th class="text-center py-3">Purchased Date</th>
                        <th class="text-end py-3">Total (Rs.)</th>
                        <th class="text-end py-3">Paid (Rs.)</th>
                        <th class="text-end py-3">Remaining (Rs.)</th>
                        <th class="text-center py-3">Status</th>
                        <th class="pe-4 text-end py-3" style="width: 260px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($player->playerPackages as $playerPkg)
                        @php
                            $totalPaid = $playerPkg->payments->sum('amount');
                            $remaining = max(0, $playerPkg->total_amount - $totalPaid);
                        @endphp
                        <tr>
                            <td class="ps-4 text-start font-monospace text-muted">{{ $playerPkg->id }}</td>
                            <td class="text-start">
                                <span class="fw-bold text-dark d-block">{{ $playerPkg->package->gameType->game_name }}</span>
                                <span class="small text-muted">{{ $playerPkg->package->time_per_day }} min/day - {{ $playerPkg->package->no_of_days }} Days</span>
                            </td>
                            <td class="text-center font-monospace text-muted small">{{ $playerPkg->created_at->format('Y-m-d') }}</td>
                            <td class="text-end fw-bold text-dark">Rs. {{ number_format($playerPkg->total_amount) }}</td>
                            <td class="text-end fw-bold text-success">Rs. {{ number_format($totalPaid) }}</td>
                            <td class="text-end fw-bold {{ $remaining > 0 ? 'text-danger' : 'text-muted' }}">
                                Rs. {{ number_format($remaining) }}
                            </td>
                            <td class="text-center">
                                @if($playerPkg->package_status === 'not played')
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1 fw-semibold" style="border-radius: 20px;">
                                        Not Played
                                    </span>
                                @elseif($playerPkg->package_status === 'started')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 fw-semibold" style="border-radius: 20px;">
                                        Started
                                    </span>
                                @elseif($playerPkg->package_status === 'completed')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fw-semibold" style="border-radius: 20px;">
                                        Completed
                                    </span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    <!-- Record Payment Button -->
                                    <button type="button" class="btn btn-outline-primary btn-sm fw-semibold px-2" style="border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#recordPaymentModal{{ $playerPkg->id }}" title="Record Payment">
                                        💳 Record Payment
                                    </button>
                                    
                                    <!-- Eye Button (View Details) -->
                                    <button type="button" class="btn btn-outline-info btn-sm fw-semibold px-2" style="border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#packageDetailsModal{{ $playerPkg->id }}" title="View Package Details">
                                        👁️
                                    </button>

                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-outline-warning btn-sm fw-semibold px-2" style="border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#editPkgModal{{ $playerPkg->id }}" title="Edit Package">
                                        ✏️
                                    </button>

                                    <!-- Delete Button -->
                                    <form action="{{ route('player_packages.destroy', $playerPkg->id) }}" method="POST" class="d-inline" onsubmit="confirmDelete(event, this, '{{ __('Are you sure you want to remove this player\'s package and all payments linked to it?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm px-2" style="border-radius: 6px;" title="Remove Package">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fs-1 d-block mb-3 text-secondary">📦</i>
                                No subscription packages purchased yet.
                                <br>Click <strong>"+ Add Package"</strong> above to register a new package for {{ $player->name }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==============================================
     MODAL: ADD PACKAGE PURCHASE FORM
     ============================================== -->
<div class="modal fade" id="addPackageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title fw-bold">+ Purchase New Package</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('player_packages.store') }}" method="POST">
                @csrf
                <input type="hidden" name="player_id" value="{{ $player->id }}">

                <div class="modal-body p-4">
                    @if($packages->isEmpty())
                        <p class="text-muted text-center py-3 mb-0">
                            No packages configured in masters. Configure packages in settings before purchasing.
                        </p>
                    @else
                        <div class="mb-3">
                            <label for="modal_package_id" class="form-label fw-semibold">Select Subscription Package</label>
                            <select class="form-select" id="modal_package_id" name="package_id" required style="border-radius: 8px;" onchange="updateAddAmount(this)">
                                <option value="" disabled selected>-- Select Subscription --</option>
                                @foreach($packages as $pkg)
                                    <option value="{{ $pkg->id }}" data-amount="{{ $pkg->amount }}">
                                        {{ $pkg->gameType->game_name }} ({{ $pkg->time_per_day }} min/day - {{ $pkg->no_of_days }} Days) - Rs. {{ number_format($pkg->amount) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="modal_total_amount" class="form-label fw-semibold">Total Price (Rs.)</label>
                            <input type="number" class="form-control" id="modal_total_amount" name="total_amount" required min="0" style="border-radius: 8px;">
                        </div>

                        <div class="mb-3">
                            <label for="modal_remarks" class="form-label fw-semibold">Remarks</label>
                            <textarea class="form-control" id="modal_remarks" name="remarks" rows="2" placeholder="e.g. Paid cash, partial paid" style="border-radius: 8px;"></textarea>
                        </div>
                    @endif
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary fw-semibold px-3" style="border-radius: 8px;" data-bs-dismiss="modal">Cancel</button>
                    @if(!$packages->isEmpty())
                        <button type="submit" class="btn btn-success fw-semibold px-4" style="border-radius: 8px;">Purchase Package</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==============================================
     MODALS FOR EACH PURCHASED PACKAGE
     ============================================== -->
@foreach($player->playerPackages as $playerPkg)
    @php
        $totalPaid = $playerPkg->payments->sum('amount');
        $remaining = max(0, $playerPkg->total_amount - $totalPaid);
    @endphp

    <!-- MODAL 1: RECORD PAYMENT FORM -->
    <div class="modal fade" id="recordPaymentModal{{ $playerPkg->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold">💳 Record Payment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('player_packages.payments.store', $playerPkg->id) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="p-3 bg-light rounded mb-3 border">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Package:</span>
                                <strong class="text-dark">{{ $playerPkg->package->gameType->game_name }} ({{ $playerPkg->package->no_of_days }} Days)</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Total Package Price:</span>
                                <strong class="text-dark">Rs. {{ number_format($playerPkg->total_amount) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Paid To Date:</span>
                                <strong class="text-success">Rs. {{ number_format($totalPaid) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">Outstanding Balance:</span>
                                <strong class="{{ $remaining > 0 ? 'text-danger' : 'text-success' }}">Rs. {{ number_format($remaining) }}</strong>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Payment Date</label>
                            <input type="date" class="form-control" name="date" required value="{{ date('Y-m-d') }}" style="border-radius: 8px;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Payment Amount (Rs.)</label>
                            <input type="number" class="form-control" name="amount" required min="1" max="{{ $remaining > 0 ? $remaining : $playerPkg->total_amount }}" value="{{ $remaining > 0 ? $remaining : '' }}" style="border-radius: 8px;" placeholder="Enter amount paid">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Remarks</label>
                            <input type="text" class="form-control" name="remarks" placeholder="e.g. Cash, eSewa payment" style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-secondary fw-semibold px-3" style="border-radius: 8px;" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary fw-semibold px-4" style="border-radius: 8px;">✓ Save Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: PACKAGE DETAILS & PAYMENT HISTORY (EYE BUTTON) -->
    <div class="modal fade" id="packageDetailsModal{{ $playerPkg->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header bg-dark text-white py-3">
                    <h5 class="modal-title fw-bold">👁️ Package Details — {{ $playerPkg->package->gameType->game_name }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Metrics Summary -->
                    <div class="row text-center bg-light p-3 rounded mb-4 border">
                        <div class="col-md-3 border-end">
                            <span class="text-muted d-block small mb-1">TOTAL PRICE</span>
                            <h5 class="fw-bold text-dark mb-0">Rs. {{ number_format($playerPkg->total_amount) }}</h5>
                        </div>
                        <div class="col-md-3 border-end">
                            <span class="text-muted d-block small mb-1">TOTAL PAID</span>
                            <h5 class="fw-bold text-success mb-0">Rs. {{ number_format($totalPaid) }}</h5>
                        </div>
                        <div class="col-md-3 border-end">
                            <span class="text-muted d-block small mb-1">REMAINING</span>
                            <h5 class="fw-bold {{ $remaining > 0 ? 'text-danger' : 'text-muted' }} mb-0">Rs. {{ number_format($remaining) }}</h5>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted d-block small mb-1">STATUS</span>
                            <span class="badge bg-primary px-2.5 py-1">{{ strtoupper($playerPkg->package_status) }}</span>
                        </div>
                    </div>

                    <!-- Package Meta Info -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2">Package Information</h6>
                        <ul class="list-group border-0 rounded overflow-hidden" style="border-radius: 8px;">
                            <li class="list-group-item d-flex justify-content-between bg-dark text-white border-secondary py-2.5">
                                <span class="text-white-50">Subscription Package:</span>
                                <strong class="text-white">{{ $playerPkg->package->gameType->game_name }} ({{ $playerPkg->package->time_per_day }} min/day - {{ $playerPkg->package->no_of_days }} Days)</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between bg-dark text-white border-secondary py-2.5">
                                <span class="text-white-50">Purchased Date:</span>
                                <strong class="font-monospace text-white">{{ $playerPkg->created_at->format('Y-m-d H:i') }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between bg-dark text-white border-secondary py-2.5">
                                <span class="text-white-50">Remarks:</span>
                                <span class="text-white">{{ $playerPkg->remarks ?? 'None' }}</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Payment Log History -->
                    <div>
                        <h6 class="fw-bold text-dark mb-2">Payment History Log</h6>
                        <div class="table-responsive border rounded">
                            <table class="table table-sm table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3 py-2">Date</th>
                                        <th class="text-end py-2">Amount</th>
                                        <th class="py-2">Remarks</th>
                                        <th class="pe-3 text-end py-2" style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($playerPkg->payments as $payment)
                                        <tr>
                                            <td class="ps-3 py-2 font-monospace text-muted" style="font-size: 0.85rem;">{{ $payment->date->format('Y-m-d') }}</td>
                                            <td class="text-end fw-bold text-success py-2" style="font-size: 0.85rem;">Rs. {{ number_format($payment->amount) }}</td>
                                            <td class="py-2 text-secondary" style="font-size: 0.85rem;">{{ $payment->remarks ?? '-' }}</td>
                                            <td class="pe-3 text-end py-2">
                                                <form action="{{ route('player_packages.payments.destroy', [$playerPkg->id, $payment->id]) }}" method="POST" onsubmit="confirmDelete(event, this, '{{ __('Are you sure you want to delete this payment log?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger p-0 border-0" title="Delete Payment Log">
                                                        ✕
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3 small">No payment logs recorded yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary fw-semibold px-4" style="border-radius: 8px;" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 3: EDIT PACKAGE MODAL -->
    <div class="modal fade" id="editPkgModal{{ $playerPkg->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header bg-dark text-white py-3">
                    <h5 class="modal-title fw-bold">Edit Package Assignment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('player_packages.update', $playerPkg->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Package Total Price (Rs.)</label>
                            <input type="number" class="form-control" name="total_amount" value="{{ $playerPkg->total_amount }}" required min="0" style="border-radius: 8px;">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" name="package_status" required style="border-radius: 8px;">
                                <option value="not played" {{ $playerPkg->package_status === 'not played' ? 'selected' : '' }}>Not Played</option>
                                <option value="started" {{ $playerPkg->package_status === 'started' ? 'selected' : '' }}>Started</option>
                                <option value="completed" {{ $playerPkg->package_status === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Remarks</label>
                            <textarea class="form-control" name="remarks" rows="2" style="border-radius: 8px;">{{ $playerPkg->remarks }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-secondary fw-semibold px-3" style="border-radius: 8px;" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary fw-semibold px-4" style="border-radius: 8px;">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- Modal: Delete Confirmation -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold" id="delete-modal-title">{{ __('Confirm Action') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-start gap-3">
                    <span class="fs-2" id="delete-modal-icon">🗑️</span>
                    <div>
                        <p class="mb-0 text-dark fw-medium" id="delete-modal-message" style="font-size: 0.95rem;"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top-0 py-2.5">
                <button type="button" class="btn btn-sm btn-secondary fw-semibold px-3 py-1.5" style="border-radius: 8px;" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-sm btn-danger fw-semibold px-3 py-1.5" style="border-radius: 8px;" id="delete-confirm-action-btn" onclick="submitDeleteForm()">{{ __('Delete') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
function updateAddAmount(selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const amount = selectedOption.getAttribute('data-amount');
    document.getElementById('modal_total_amount').value = amount || '';
}

let formToSubmit = null;

function confirmDelete(event, form, message, title = "{{ __('Confirm Action') }}", icon = "🗑️") {
    event.preventDefault();
    formToSubmit = form;
    
    document.getElementById('delete-modal-title').textContent = title;
    document.getElementById('delete-modal-message').textContent = message;
    document.getElementById('delete-modal-icon').textContent = icon;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    deleteModal.show();
}

function submitDeleteForm() {
    if (formToSubmit) {
        formToSubmit.submit();
    }
}
</script>
@endsection
