@extends('layouts.app')

@section('title', 'Projects - Project Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Projects</h1>
            <p class="text-muted mb-0">Manage your project portfolio</p>
        </div>
        @can('projects.create')
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create Project
        </a>
        @endcan
    </div>

    <!-- Projects Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Team</th>
                            <th>Progress</th>
                            <th>Due Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                        <tr>
                            <td>
                                <div>
                                    <div class="fw-bold">{{ $project->name }}</div>
                                    <small class="text-muted">{{ Str::limit($project->description, 50) }}</small>
                                </div>
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
                            <td>{{ $project->team->name ?? 'No Team' }}</td>
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
                                    @can('projects.edit')
                                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('projects.delete')
                                    <form action="{{ route('projects.destroy', $project) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Are you sure you want to delete this project?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="bi bi-folder text-muted fs-1 mb-3"></i>
                                <h6 class="text-muted">No Projects Yet</h6>
                                <p class="text-muted small">Create your first project to get started.</p>
                                @can('projects.create')
                                <a href="{{ route('projects.create') }}" class="btn btn-primary">Create Project</a>
                                @endcan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($projects->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $projects->links() }}
    </div>
    @endif
</div>
@endsection
