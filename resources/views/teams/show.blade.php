@extends('layouts.app')

@section('title', $team->name . ' - Team Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">{{ $team->name }}</h1>
            <p class="text-muted mb-0">{{ $team->description }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('teams.edit', $team) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
            <a href="{{ route('teams.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Teams
            </a>
        </div>
    </div>

    <!-- Team Details -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Team Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Type</label>
                                <div>
                                    <span class="badge bg-primary fs-6">
                                        {{ ucfirst(str_replace('_', ' ', $team->type)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Team Leader</label>
                                <div>{{ $team->leader->name ?? 'No leader assigned' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <div>{{ $team->description ?? 'No description provided' }}</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Total Members</label>
                                <div class="h4 mb-0 fw-bold text-primary">{{ $team->members->count() }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Active Projects</label>
                                <div class="h4 mb-0 fw-bold text-success">{{ $team->projects->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Team Stats -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Team Stats</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h4 mb-1 fw-bold text-primary">{{ $team->members->count() }}</div>
                            <small class="text-muted">Members</small>
                        </div>
                        <div class="col-6">
                            <div class="h4 mb-1 fw-bold text-success">{{ $team->projects->count() }}</div>
                            <small class="text-muted">Projects</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('projects.create') }}?team_id={{ $team->id }}" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle me-2"></i>Create Project
                        </a>
                        <a href="#" class="btn btn-outline-secondary">
                            <i class="bi bi-person-plus me-2"></i>Add Member
                        </a>
                        <a href="#" class="btn btn-outline-info">
                            <i class="bi bi-bar-chart me-2"></i>View Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Members Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Team Members</h5>
            <a href="#" class="btn btn-sm btn-primary">
                <i class="bi bi-person-plus me-2"></i>Add Member
            </a>
        </div>
        <div class="card-body">
            @if($team->members->count() > 0)
                <div class="row">
                    @foreach($team->members as $member)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <div class="avatar avatar-lg mb-3 mx-auto">
                                    <div class="avatar-initial bg-primary rounded-circle">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                </div>
                                <h6 class="card-title mb-1">{{ $member->name }}</h6>
                                <p class="card-text text-muted small">{{ $member->email }}</p>
                                @if($member->id === $team->leader_id)
                                <span class="badge bg-warning">Team Leader</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-people text-muted fs-1 mb-3"></i>
                    <h6 class="text-muted">No Team Members Yet</h6>
                    <p class="text-muted small">Add members to this team to get started.</p>
                    <a href="#" class="btn btn-primary">Add Member</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Projects Section -->
    @if($team->projects->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Team Projects</h5>
            <a href="{{ route('projects.create') }}?team_id={{ $team->id }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create Project
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Project</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Progress</th>
                            <th>Due Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($team->projects as $project)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $project->name }}</div>
                                <small class="text-muted">{{ Str::limit($project->description, 50) }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $project->status === 'active' ? 'success' : ($project->status === 'completed' ? 'primary' : 'secondary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $project->priority === 'urgent' ? 'danger' : ($project->priority === 'high' ? 'warning' : 'info') }}">
                                    {{ ucfirst($project->priority) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                        <div class="progress-bar bg-primary" style="width: {{ $project->progress }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $project->progress }}%</small>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $project->end_date ? $project->end_date->format('M d, Y') : 'No due date' }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
