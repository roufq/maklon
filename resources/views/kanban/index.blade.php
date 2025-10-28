@extends('layouts.app')

@section('title', 'Kanban Board - Project Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Kanban Board</h1>
            <p class="text-muted mb-0">Visual task management with drag-and-drop</p>
        </div>
        <div class="d-flex gap-2">
            <select id="projectFilter" class="form-select" style="width: auto;" title="Filter tasks by project">
                <option value="">All Projects</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ $projectId == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
            <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add Task
            </a>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="kanban-board">
        <div class="row">
            <!-- Pending Column -->
            <div class="col-md-3 mb-4">
                <div class="kanban-column" data-status="pending">
                    <div class="kanban-column-header bg-secondary text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-circle me-2"></i>Pending
                            <small class="ms-1">(WIP: {{ $wipLimits['pending'] ?? '∞' }})</small>
                            <span class="badge bg-light text-dark ms-2">{{ $tasks->get('pending', collect())->count() }}</span>
                        </h6>
                    </div>
                    <div class="kanban-column-body" id="pending-column">
                        @forelse($tasks->get('pending', collect()) as $task)
                        <div class="kanban-card" data-task-id="{{ $task->id }}">
                            <div class="kanban-card-header">
                                <h6 class="mb-1">{{ $task->title }}</h6>
                                @if($task->project)
                                    <small class="text-muted">
                                        <i class="bi bi-folder me-1"></i>{{ $task->project->name }}
                                    </small>
                                @endif
                            </div>
                            <div class="kanban-card-body">
                                <p class="small text-muted mb-2">{{ Str::limit($task->description, 80) }}</p>

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : 'info') }} badge-sm">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                    @if($task->due_date)
                                        <small class="text-muted">
                                            <i class="bi bi-calendar"></i> {{ $task->due_date->format('M d') }}
                                        </small>
                                    @endif
                                </div>

                                @if($task->assignedUser)
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person me-1 text-muted"></i>
                                        <small class="text-muted">{{ $task->assignedUser->name }}</small>
                                    </div>
                                @endif
                            </div>
                            <div class="kanban-card-footer">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('tasks.show', $task) }}">
                                            <i class="bi bi-eye me-2"></i>View
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('tasks.edit', $task) }}">
                                            <i class="bi bi-pencil me-2"></i>Edit
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete({{ $task->id }})">
                                            <i class="bi bi-trash me-2"></i>Delete
                                        </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-circle fs-2 mb-2"></i>
                            <p class="small">No pending tasks</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- In Progress Column -->
            <div class="col-md-3 mb-4">
                <div class="kanban-column" data-status="in_progress">
                    <div class="kanban-column-header bg-primary text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-play-circle me-2"></i>In Progress
                            <small class="ms-1">(WIP: {{ $wipLimits['in_progress'] ?? '∞' }})</small>
                            <span class="badge bg-light text-dark ms-2">{{ $tasks->get('in_progress', collect())->count() }}</span>
                        </h6>
                    </div>
                    <div class="kanban-column-body" id="in_progress-column">
                        @forelse($tasks->get('in_progress', collect()) as $task)
                        <div class="kanban-card" data-task-id="{{ $task->id }}">
                            <div class="kanban-card-header">
                                <h6 class="mb-1">{{ $task->title }}</h6>
                                @if($task->project)
                                    <small class="text-muted">
                                        <i class="bi bi-folder me-1"></i>{{ $task->project->name }}
                                    </small>
                                @endif
                            </div>
                            <div class="kanban-card-body">
                                <p class="small text-muted mb-2">{{ Str::limit($task->description, 80) }}</p>

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : 'info') }} badge-sm">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                    @if($task->due_date)
                                        <small class="text-muted">
                                            <i class="bi bi-calendar"></i> {{ $task->due_date->format('M d') }}
                                        </small>
                                    @endif
                                </div>

                                @if($task->assignedUser)
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person me-1 text-muted"></i>
                                        <small class="text-muted">{{ $task->assignedUser->name }}</small>
                                    </div>
                                @endif
                            </div>
                            <div class="kanban-card-footer">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('tasks.show', $task) }}">
                                            <i class="bi bi-eye me-2"></i>View
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('tasks.edit', $task) }}">
                                            <i class="bi bi-pencil me-2"></i>Edit
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete({{ $task->id }})">
                                            <i class="bi bi-trash me-2"></i>Delete
                                        </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-play-circle fs-2 mb-2"></i>
                            <p class="small">No tasks in progress</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Completed Column -->
            <div class="col-md-3 mb-4">
                <div class="kanban-column" data-status="completed">
                    <div class="kanban-column-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-check-circle me-2"></i>Completed
                            <small class="ms-1">(WIP: {{ $wipLimits['completed'] ?? '∞' }})</small>
                            <span class="badge bg-light text-dark ms-2">{{ $tasks->get('completed', collect())->count() }}</span>
                        </h6>
                    </div>
                    <div class="kanban-column-body" id="completed-column">
                        @forelse($tasks->get('completed', collect()) as $task)
                        <div class="kanban-card" data-task-id="{{ $task->id }}">
                            <div class="kanban-card-header">
                                <h6 class="mb-1">{{ $task->title }}</h6>
                                @if($task->project)
                                    <small class="text-muted">
                                        <i class="bi bi-folder me-1"></i>{{ $task->project->name }}
                                    </small>
                                @endif
                            </div>
                            <div class="kanban-card-body">
                                <p class="small text-muted mb-2">{{ Str::limit($task->description, 80) }}</p>

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : 'info') }} badge-sm">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                    @if($task->due_date)
                                        <small class="text-muted">
                                            <i class="bi bi-calendar"></i> {{ $task->due_date->format('M d') }}
                                        </small>
                                    @endif
                                </div>

                                @if($task->assignedUser)
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person me-1 text-muted"></i>
                                        <small class="text-muted">{{ $task->assignedUser->name }}</small>
                                    </div>
                                @endif
                            </div>
                            <div class="kanban-card-footer">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('tasks.show', $task) }}">
                                            <i class="bi bi-eye me-2"></i>View
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('tasks.edit', $task) }}">
                                            <i class="bi bi-pencil me-2"></i>Edit
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete({{ $task->id }})">
                                            <i class="bi bi-trash me-2"></i>Delete
                                        </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-check-circle fs-2 mb-2"></i>
                            <p class="small">No completed tasks</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Cancelled Column -->
            <div class="col-md-3 mb-4">
                <div class="kanban-column" data-status="cancelled">
                    <div class="kanban-column-header bg-danger text-white">
                        <h6 class="mb-0">
                            <i class="bi bi-x-circle me-2"></i>Cancelled
                            <small class="ms-1">(WIP: {{ $wipLimits['cancelled'] ?? '∞' }})</small>
                            <span class="badge bg-light text-dark ms-2">{{ $tasks->get('cancelled', collect())->count() }}</span>
                        </h6>
                    </div>
                    <div class="kanban-column-body" id="cancelled-column">
                        @forelse($tasks->get('cancelled', collect()) as $task)
                        <div class="kanban-card" data-task-id="{{ $task->id }}">
                            <div class="kanban-card-header">
                                <h6 class="mb-1">{{ $task->title }}</h6>
                                @if($task->project)
                                    <small class="text-muted">
                                        <i class="bi bi-folder me-1"></i>{{ $task->project->name }}
                                    </small>
                                @endif
                            </div>
                            <div class="kanban-card-body">
                                <p class="small text-muted mb-2">{{ Str::limit($task->description, 80) }}</p>

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : 'info') }} badge-sm">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                    @if($task->due_date)
                                        <small class="text-muted">
                                            <i class="bi bi-calendar"></i> {{ $task->due_date->format('M d') }}
                                        </small>
                                    @endif
                                </div>

                                @if($task->assignedUser)
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person me-1 text-muted"></i>
                                        <small class="text-muted">{{ $task->assignedUser->name }}</small>
                                    </div>
                                @endif
                            </div>
                            <div class="kanban-card-footer">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('tasks.show', $task) }}">
                                            <i class="bi bi-eye me-2"></i>View
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('tasks.edit', $task) }}">
                                            <i class="bi bi-pencil me-2"></i>Edit
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete({{ $task->id }})">
                                            <i class="bi bi-trash me-2"></i>Delete
                                        </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-x-circle fs-2 mb-2"></i>
                            <p class="small">No cancelled tasks</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.kanban-board {
    min-height: 600px;
}

.kanban-column {
    background: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.kanban-column-header {
    padding: 1rem;
    font-weight: 600;
}

.kanban-column-body {
    min-height: 500px;
    padding: 1rem;
    background: white;
}

.kanban-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    margin-bottom: 0.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
    cursor: move;
}

.kanban-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    transform: translateY(-1px);
}

.kanban-card-header {
    padding: 0.75rem 0.75rem 0.5rem;
    border-bottom: 1px solid #f8f9fa;
}

.kanban-card-body {
    padding: 0.5rem 0.75rem;
}

.kanban-card-footer {
    padding: 0.5rem 0.75rem 0.75rem;
    border-top: 1px solid #f8f9fa;
    text-align: right;
}

.sortable-ghost {
    opacity: 0.4;
}

.sortable-chosen {
    opacity: 1;
    transform: rotate(5deg);
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize drag and drop for each column
    const columns = ['pending', 'in_progress', 'completed', 'cancelled'];
    const wipLimits = @json($wipLimits ?? []);

    columns.forEach(status => {
        const column = document.getElementById(`${status}-column`);

        new Sortable(column, {
            group: 'kanban',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: function(evt) {
                const taskId = evt.item.dataset.taskId;
                const newStatus = evt.to.closest('.kanban-column').dataset.status;
                const oldStatus = evt.from.closest('.kanban-column').dataset.status;

                if (newStatus !== oldStatus) {
                    const columnBody = evt.to.closest('.kanban-column').querySelector('.kanban-column-body');
                    const limit = wipLimits[newStatus] ?? 999;
                    const countIfMoved = columnBody.children.length;
                    if (countIfMoved > limit) {
                        evt.from.appendChild(evt.item);
                        alert('Cannot move. WIP limit reached for ' + newStatus + ' (max ' + limit + ').');
                        updateColumnCounts();
                        return;
                    }
                    updateTaskStatus(taskId, newStatus);
                }
            }
        });
    });

    // Project filter
    document.getElementById('projectFilter').addEventListener('change', function() {
        const projectId = this.value;
        const url = new URL(window.location);
        if (projectId) {
            url.searchParams.set('project_id', projectId);
        } else {
            url.searchParams.delete('project_id');
        }
        window.location.href = url.toString();
    });
});

function updateTaskStatus(taskId, status) {
    fetch(`/kanban/tasks/${taskId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            status: status
        })
    })
    .then(async response => {
        if (!response.ok) {
            const data = await response.json().catch(()=>({}));
            throw new Error(data.message || 'Server rejected');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update column counts
            updateColumnCounts();
        } else {
            alert('Error updating task status');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating task status');
        location.reload();
    });
}

function updateColumnCounts() {
    const columns = ['pending', 'in_progress', 'completed', 'cancelled'];
    columns.forEach(status => {
        const column = document.querySelector(`[data-status="${status}"]`);
        const count = column.querySelector('.kanban-column-body').children.length;
        const badge = column.querySelector('.kanban-column-header .badge');
        if (badge) {
            badge.textContent = count;
        }
    });
}

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
