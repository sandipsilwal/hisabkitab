@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">Skate Park Packages</h5>
        <a href="{{ route('packages.create') }}" class="btn btn-primary btn-sm px-3 fw-semibold" style="border-radius: 6px;">
            + Add Package
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 text-start py-3" style="width: 100px;">ID</th>
                        <th class="text-start py-3">Game Type</th>
                        <th class="text-center py-3">Time Per Day</th>
                        <th class="text-center py-3">Number of Days</th>
                        <th class="text-center py-3">Total Minutes</th>
                        <th class="text-center py-3">Amount</th>
                        <th class="pe-4 text-end py-3" style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($packages as $package)
                        <tr>
                            <td class="ps-4 text-start font-monospace text-muted">{{ $package->id }}</td>
                            <td class="text-start fw-bold text-dark">{{ $package->gameType->game_name }}</td>
                            <td class="text-center fw-semibold text-primary">{{ $package->time_per_day }} min / day</td>
                            <td class="text-center fw-semibold text-dark">{{ $package->no_of_days }} days</td>
                            <td class="text-center font-monospace text-secondary">{{ $package->time_per_day * $package->no_of_days }} min</td>
                            <td class="text-center fw-bold text-success" style="font-size: 1.05rem;">
                                Rs. {{ number_format($package->amount) }}
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('packages.edit', $package->id) }}" class="btn btn-outline-warning btn-sm me-1" style="border-radius: 6px;">
                                    Edit
                                </a>
                                <form action="{{ route('packages.destroy', $package->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this package?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" style="border-radius: 6px;">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fs-1 d-block mb-2 text-secondary">📦</i>
                                No subscription packages defined yet. Click "+ Add Package" to begin.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
