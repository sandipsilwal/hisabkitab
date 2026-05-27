@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">Skate Park Tokens</h5>
        <a href="{{ route('tokens.create') }}" class="btn btn-primary btn-sm px-3 fw-semibold" style="border-radius: 6px;">
            + Add Token
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 text-start py-3" style="width: 100px;">Order</th>
                        <th class="text-start py-3">Token Name</th>
                        <th class="text-start py-3">Game Type</th>
                        <th class="text-center py-3" style="width: 150px;">Status</th>
                        <th class="pe-4 text-end py-3" style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tokens as $token)
                        <tr>
                            <td class="ps-4 text-start font-monospace text-muted">{{ $token->display_order }}</td>
                            <td class="text-start fw-bold text-dark">{{ $token->name }}</td>
                            <td class="text-start">
                                <span class="badge bg-primary-subtle text-primary px-3 py-1.5" style="border-radius: 6px; font-weight: 600;">
                                    {{ $token->gameType->game_name }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($token->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5" style="border-radius: 30px;">
                                        ● Active
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5" style="border-radius: 30px;">
                                        ○ Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('tokens.edit', $token->id) }}" class="btn btn-outline-warning btn-sm me-1" style="border-radius: 6px;">
                                    Edit
                                </a>
                                <form action="{{ route('tokens.destroy', $token->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this token?')">
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
                                <i class="fs-1 d-block mb-2 text-secondary">🎫</i>
                                No tokens registered yet. Click "+ Add Token" to begin.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
