@extends('layouts.app')

@section('title', 'Teams - Project Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Teams</h1>
            <p class="text-muted mb-0">Manage your project teams</p>
        </div>
        @can('team.create')
        <a href="{{ route('teams.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create Team
        </a>
        @endcan
    </div>

    <!-- Teams Grid -->
    <div class="row">
        @forelse($teams as $team)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title mb-1">{{ $team->name }}</h5>
                            <span class="badge bg-{{ $team->type === 'project_team' ? 'primary' : 'info' }}">
                                {{ ucfirst(str_replace('_', ' ', $team->type)) }}
                            </span>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('teams.show', $team) }}">
                                    <i class="bi bi-eye me-2"></i>View Details
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('teams.edit', $team) }}">
                                    <i class="bi bi-pencil me-2"></i>Edit Team
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('teams.destroy', $team) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger"
                                                onclick="return confirm('Are you sure you want to delete this team?')">
                                            <i class="bi bi-trash me-2"></i>Delete Team
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <p class="card-text text-muted small mb-3">
                        {{ Str::limit($team->description, 100) }}
                    </p>

                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-person-circle me-2 text-primary"></i>
                            <small class="text-muted">Team Leader:</small>
                        </div>
                        <div class="fw-bold small">{{ $team->leader->name ?? 'Not assigned' }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-people me-2 text-success"></i>
                            <small class="text-muted">Members ({{ $team->members->count() }}):</small>
                        </div>
                        <div class="d-flex flex-wrap gap-1">
                            @forelse($team->members->take(3) as $member)
                                <span class="badge bg-light text-dark small">{{ $member->name }}</span>
                            @empty
                                <span class="text-muted small">No members</span>
                            @endforelse
                            @if($team->members->count() > 3)
                                <span class="badge bg-secondary small">+{{ $team->members->count() - 3 }} more</span>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Created {{ $team->created_at->diffForHumans() }}
                        </small>
                        <a href="{{ route('teams.show', $team) }}" class="btn btn-sm btn-outline-primary">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-people text-muted fs-1 mb-3"></i>
                    <h6 class="text-muted">No Teams Yet</h6>
                    <p class="text-muted small">Create your first team to get started.</p>
                    @can('team.create')
                    <a href="{{ route('teams.create') }}" class="btn btn-primary">Create Team</a>
                    @endcan
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($teams->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $teams->links() }}
    </div>
    @endif
</div>
@endsection
