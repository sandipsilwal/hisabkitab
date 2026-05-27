@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">Default Times</h5>
        <a href="{{ route('default_times.create') }}" class="btn btn-primary btn-sm px-3 fw-semibold" style="border-radius: 6px;">
            + Add Default Time
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 text-start py-3" style="width: 100px;">Order</th>
                        <th class="text-start py-3">Label</th>
                        <th class="text-center py-3">Minutes</th>
                        <th class="pe-4 text-end py-3" style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($defaultTimes as $defaultTime)
                        <tr>
                            <td class="ps-4 text-start font-monospace text-muted">{{ $defaultTime->display_order }}</td>
                            <td class="text-start fw-semibold text-dark">{{ $defaultTime->label }}</td>
                            <td class="text-center fw-semibold text-primary">{{ $defaultTime->minutes }} min</td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('default_times.edit', $defaultTime->id) }}" class="btn btn-outline-warning btn-sm me-1" style="border-radius: 6px;">
                                    Edit
                                </a>
                                <form action="{{ route('default_times.destroy', $defaultTime->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this default time duration? All linked rates will be deleted too.')">
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
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fs-1 d-block mb-2 text-secondary">⏱</i>
                                No default times registered yet. Click "+ Add Default Time" to begin.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
