@extends('layouts.app')

@section('content')
<div class="row">
    <!-- Left Column: Player Details & Purchase Form -->
    <div class="col-lg-4 mb-4">
        <!-- Player Details Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 60px; height: 60px;">
                        {{ strtoupper(substr($player->name, 0, 1)) }}
                    </div>
                    <div class="ms-3">
                        <h4 class="mb-0 fw-bold text-dark">{{ $player->name }}</h4>
                        <span class="text-muted small">Player #{{ $player->id }}</span>
                    </div>
                </div>
                
                <hr class="text-secondary opacity-25">
                
                <div class="mb-2">
                    <span class="text-muted d-block small">Contact Number</span>
                    <strong class="text-dark font-monospace" style="font-size: 1.05rem;">{{ $player->contact ?? 'Not provided' }}</strong>
                </div>
                
                <div class="mb-3">
                    <span class="text-muted d-block small">Address</span>
                    <strong class="text-dark">{{ $player->address ?? 'Not provided' }}</strong>
                </div>
                
                <div class="d-grid mt-4">
                    <a href="{{ route('players.edit', $player->id) }}" class="btn btn-outline-warning fw-semibold" style="border-radius: 8px;">
                        Edit Profile Details
                    </a>
                </div>
            </div>
        </div>

        <!-- Purchase Package Card -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-dark text-white py-3">
                <h5 class="mb-0 fw-bold">Purchase New Package</h5>
            </div>
            <div class="card-body p-4">
                @if($packages->isEmpty())
                    <p class="text-muted text-center py-3">
                        No packages configured in masters. Configure packages in configuration before purchasing.
                    </p>
                @else
                    <form action="{{ route('player_packages.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="player_id" value="{{ $player->id }}">

                        <div class="mb-3">
                            <label for="package_id" class="form-label fw-semibold">Select Package</label>
                            <select class="form-select" id="package_id" name="package_id" required style="border-radius: 8px;" onchange="updateAmount(this)">
                                <option value="" disabled selected>-- Select Subscription --</option>
                                @foreach($packages as $pkg)
                                    <option value="{{ $pkg->id }}" data-amount="{{ $pkg->amount }}">
                                        {{ $pkg->gameType->game_name }} ({{ $pkg->time_per_day }} min/day - {{ $pkg->no_of_days }} Days) - Rs. {{ number_format($pkg->amount) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="total_amount" class="form-label fw-semibold">Total Price (Rs.)</label>
                            <input type="number" class="form-control" id="total_amount" name="total_amount" required min="0" style="border-radius: 8px;">
                        </div>

                        <div class="mb-3">
                            <label for="remarks" class="form-label fw-semibold">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="2" placeholder="e.g. Paid cash, partial paid" style="border-radius: 8px;"></textarea>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-success fw-semibold" style="border-radius: 8px;">
                                Purchase Package
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Purchased Packages list & Payment Histories -->
    <div class="col-lg-8">
        <h4 class="fw-bold mb-3 text-dark">Purchased Packages History</h4>
        
        @forelse($player->playerPackages as $playerPkg)
            @php
                $totalPaid = $playerPkg->payments->sum('amount');
                $remaining = $playerPkg->total_amount - $totalPaid;
            @endphp
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
                <!-- Package Card Header -->
                <div class="card-header bg-light d-flex justify-content-between align-items-center py-3 border-bottom-0">
                    <div>
                        <span class="badge bg-primary px-3 py-1.5 fs-7 mb-1" style="border-radius: 6px;">
                            {{ $playerPkg->package->gameType->game_name }}
                        </span>
                        <h5 class="mb-0 fw-bold text-dark">
                            {{ $playerPkg->package->time_per_day }} min/day for {{ $playerPkg->package->no_of_days }} Days
                        </h5>
                    </div>
                    <div>
                        @if($playerPkg->package_status === 'not played')
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 fw-bold" style="border-radius: 30px;">
                                Not Played
                            </span>
                        @elseif($playerPkg->package_status === 'started')
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 fw-bold" style="border-radius: 30px;">
                                Started
                            </span>
                        @elseif($playerPkg->package_status === 'completed')
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fw-bold" style="border-radius: 30px;">
                                Completed
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Package Summary Metrics -->
                <div class="card-body bg-white py-4 px-4 border-bottom">
                    <div class="row text-center">
                        <div class="col-md-4 border-end mb-3 mb-md-0">
                            <span class="text-muted d-block small mb-1">TOTAL AMOUNT</span>
                            <h4 class="fw-bold text-dark">Rs. {{ number_format($playerPkg->total_amount) }}</h4>
                        </div>
                        <div class="col-md-4 border-end mb-3 mb-md-0">
                            <span class="text-muted d-block small mb-1">PAID TO DATE</span>
                            <h4 class="fw-bold text-success">Rs. {{ number_format($totalPaid) }}</h4>
                        </div>
                        <div class="col-md-4">
                            <span class="text-muted d-block small mb-1">OUTSTANDING</span>
                            <h4 class="fw-bold {{ $remaining > 0 ? 'text-danger' : 'text-muted' }}">
                                Rs. {{ number_format($remaining) }}
                            </h4>
                        </div>
                    </div>
                </div>

                <!-- Payments Grid -->
                <div class="card-body bg-light-subtle p-4">
                    <div class="row">
                        <!-- Left Side: Recorded Payments list -->
                        <div class="col-md-7 mb-4 mb-md-0">
                            <h6 class="fw-bold text-dark mb-3">Recorded Payments</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover bg-white rounded shadow-2xs align-middle">
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
                                                <td class="py-2 text-secondary text-truncate" style="max-width: 150px; font-size: 0.85rem;" title="{{ $payment->remarks }}">{{ $payment->remarks ?? '-' }}</td>
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
                                                <td colspan="4" class="text-center text-muted py-3 small">No payments recorded for this package.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Right Side: Quick Record Payment Form -->
                        <div class="col-md-5">
                            <h6 class="fw-bold text-dark mb-3">Record Payment</h6>
                            <form action="{{ route('player_packages.payments.store', $playerPkg->id) }}" method="POST" class="p-3 bg-white rounded border border-light shadow-2xs">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label small fw-semibold text-muted mb-1">Payment Date</label>
                                    <input type="date" class="form-control form-control-sm" name="date" required value="{{ date('Y-m-d') }}" style="border-radius: 6px;">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small fw-semibold text-muted mb-1">Amount (Rs.)</label>
                                    <input type="number" class="form-control form-control-sm" name="amount" required min="1" max="{{ $remaining }}" value="{{ $remaining > 0 ? $remaining : '' }}" style="border-radius: 6px;">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small fw-semibold text-muted mb-1">Remarks</label>
                                    <input type="text" class="form-control form-control-sm" name="remarks" placeholder="e.g. Cash payment" style="border-radius: 6px;">
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100 mt-2 fw-semibold" style="border-radius: 6px;" {{ $remaining <= 0 ? 'disabled' : '' }}>
                                    ✓ Record Payment
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Package Card Footer Controls -->
                <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3 px-4 border-top">
                    <span class="text-muted small">
                        Purchased: {{ $playerPkg->created_at->format('Y-m-d H:i') }}
                    </span>
                    <div>
                        <!-- Edit Button Modal trigger -->
                        <button class="btn btn-outline-warning btn-sm me-1" style="border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#editPkgModal{{ $playerPkg->id }}">
                            Edit Package
                        </button>
                        <form action="{{ route('player_packages.destroy', $playerPkg->id) }}" method="POST" class="d-inline" onsubmit="confirmDelete(event, this, '{{ __('Are you sure you want to remove this player\'s package and all payments linked to it?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm" style="border-radius: 6px;">
                                Remove
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Package Modal for each package -->
            <div class="modal fade" id="editPkgModal{{ $playerPkg->id }}" tabindex="-1" aria-labelledby="editPkgModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                        <div class="modal-header bg-dark text-white">
                            <h5 class="modal-title fw-bold" id="editPkgModalLabel">Edit Package Assignment</h5>
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
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary fw-semibold" style="border-radius: 8px;" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary fw-semibold" style="border-radius: 8px;">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body text-center py-5 text-muted">
                    <i class="fs-1 d-block mb-3 text-secondary">📦</i>
                    This player hasn't purchased any subscription packages yet.
                    <br>Select a package in the left panel to register one.
                </div>
            </div>
        @endforelse
    </div>
</div>

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
function updateAmount(selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const amount = selectedOption.getAttribute('data-amount');
    document.getElementById('total_amount').value = amount || '';
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
