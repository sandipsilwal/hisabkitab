@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">Skate Park Rates</h5>
        <a href="{{ route('rates.create') }}" class="btn btn-primary btn-sm px-3 fw-semibold" style="border-radius: 6px;">
            + Add Rate
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 text-start py-3" style="width: 100px;">ID</th>
                        <th class="text-start py-3">Game Type</th>
                        <th class="text-center py-3">Default Time</th>
                        <th class="text-center py-3">Rate</th>
                        <th class="pe-4 text-end py-3" style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rates as $rate)
                        <tr>
                            <td class="ps-4 text-start font-monospace text-muted">{{ $rate->id }}</td>
                            <td class="text-start fw-semibold text-dark">{{ $rate->gameType->game_name }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary px-3 py-1.5" style="border-radius: 30px; font-weight: 600;">
                                    ⏱ {{ $rate->defaultTime->label }} ({{ $rate->defaultTime->minutes }} Min)
                                </span>
                            </td>
                            <td class="text-center fw-bold text-success" style="font-size: 1.05rem;">
                                Rs. {{ number_format($rate->rate) }}
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('rates.edit', $rate->id) }}" class="btn btn-outline-warning btn-sm me-1" style="border-radius: 6px;">
                                    Edit
                                </a>
                                <form action="{{ route('rates.destroy', $rate->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this rate?')">
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
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fs-1 d-block mb-2 text-secondary">💰</i>
                                No pricing rates defined yet. Click "+ Add Rate" to begin.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
