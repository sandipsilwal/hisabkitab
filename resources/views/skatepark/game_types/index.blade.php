@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">Game Types</h5>
        <a href="{{ route('game_types.create') }}" class="btn btn-primary btn-sm px-3 fw-semibold" style="border-radius: 6px;">
            + Add Game Type
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 text-start py-3" style="width: 100px;">ID</th>
                        <th class="text-start py-3">Game Name</th>
                        <th class="pe-4 text-end py-3" style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gameTypes as $gameType)
                        <tr>
                            <td class="ps-4 text-start font-monospace text-muted">{{ $gameType->id }}</td>
                            <td class="text-start fw-semibold text-dark">{{ $gameType->game_name }}</td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('game_types.edit', $gameType->id) }}" class="btn btn-outline-warning btn-sm me-1" style="border-radius: 6px;">
                                    Edit
                                </a>
                                <form action="{{ route('game_types.destroy', $gameType->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this game type? All linked rates, tokens, and packages will also be deleted.')">
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
                            <td colspan="3" class="text-center py-5 text-muted">
                                <i class="fs-1 d-block mb-2 text-secondary">🎮</i>
                                No game types registered yet. Click "+ Add Game Type" to begin.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
