@extends('layouts.app')

@section('title', 'Tasks - Project Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Tasks</h1>
            <p class="text-muted mb-0">Manage and track your project tasks</p>
        </div>
        @can('tasks.create')
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create Task
        </a>
        @endcan
    </div>

    <!-- Filters and Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Priority</label>
                    <select name="priority" class="form-select">
                        <option value="">All Priorities</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-select">
                        <option value="">All Projects</option>
                        @foreach(\App\Models\Project::all() as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search tasks..." value="{{ request('search') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tasks Grid -->
    <div class="row">
        @forelse($tasks as $task)
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none text-dark fw-bold">
                                    {{ $task->title }}
                                </a>
                            </h6>
                            @if($task->project)
                                <small class="text-muted">
                                    <i class="bi bi-folder me-1"></i>{{ $task->project->name }}
                                </small>
                            @endif
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('tasks.show', $task) }}">
                                    <i class="bi bi-eye me-2"></i>View
                                </a></li>
                                @can('tasks.edit')
                                <li><a class="dropdown-item" href="{{ route('tasks.edit', $task) }}">
                                    <i class="bi bi-pencil me-2"></i>Edit
                                </a></li>
                                @endcan
                                <li><hr class="dropdown-divider"></li>
                                @can('tasks.delete')
                                <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete({{ $task->id }})">
                                    <i class="bi bi-trash me-2"></i>Delete
                                </a></li>
                                @endcan
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">{{ Str::limit($task->description, 100) }}</p>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'primary' : 'secondary') }} badge-sm">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                        <span class="badge bg-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : 'info') }} badge-sm">
                            {{ ucfirst($task->priority) }}
                        </span>
                    </div>

                    @if($task->assignedUser)
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-person me-2 text-muted"></i>
                            <small class="text-muted">{{ $task->assignedUser->name }}</small>
                        </div>
                    @endif

                    @if($task->due_date)
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-calendar me-2 text-muted"></i>
                            <small class="text-muted">{{ $task->due_date->format('M d, Y') }}</small>
                            @if($task->due_date->isPast() && $task->status !== 'completed')
                                <span class="badge bg-danger ms-2">Overdue</span>
                            @endif
                        </div>
                    @endif

                    @if($task->subtasks->count() > 0)
                        <div class="d-flex align-items-center">
                            <i class="bi bi-list-check me-2 text-muted"></i>
                            <small class="text-muted">
                                {{ $task->subtasks->where('status', 'completed')->count() }}/{{ $task->subtasks->count() }} subtasks
                            </small>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-light border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Created {{ $task->created_at->diffForHumans() }}
                        </small>
                        @if($task->status !== 'completed')
                            @can('tasks.edit')
                            <form action="{{ route('tasks.update', $task) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="bi bi-check-circle me-1"></i>Complete
                                </button>
                            </form>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-check-circle text-muted fs-1 mb-3"></i>
                <h6 class="text-muted">No Tasks Yet</h6>
                <p class="text-muted small">Create your first task to get started.</p>
                @can('tasks.create')
                <a href="{{ route('tasks.create') }}" class="btn btn-primary">Create Task</a>
                @endcan
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($tasks->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $tasks->links() }}
    </div>
    @endif
</div>

<script>
function confirmDelete(taskId) {
    if (confirm('Are you sure you want to delete this task?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/tasks/${taskId}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
