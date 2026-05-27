@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">Payment Types</h5>
        <a href="{{ route('payment_types.create') }}" class="btn btn-primary btn-sm px-3 fw-semibold" style="border-radius: 6px;">
            + Add Payment Type
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 text-start py-3" style="width: 100px;">ID</th>
                        <th class="text-start py-3">Payment Type Name</th>
                        <th class="text-center py-3" style="width: 150px;">Default</th>
                        <th class="pe-4 text-end py-3" style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paymentTypes as $paymentType)
                        <tr>
                            <td class="ps-4 text-start font-monospace text-muted">{{ $paymentType->id }}</td>
                            <td class="text-start fw-semibold text-dark">{{ $paymentType->name }}</td>
                            <td class="text-center">
                                @if($paymentType->is_default)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5" style="border-radius: 30px;">
                                        ★ Default
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('payment_types.edit', $paymentType->id) }}" class="btn btn-outline-warning btn-sm me-1" style="border-radius: 6px;">
                                    Edit
                                </a>
                                @if(!$paymentType->is_default)
                                    <form action="{{ route('payment_types.destroy', $paymentType->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this payment type?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" style="border-radius: 6px;">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fs-1 d-block mb-2 text-secondary">💳</i>
                                No payment types registered yet. Click "+ Add Payment Type" to begin.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
