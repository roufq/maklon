@extends('layouts.app')

@section('title', $task->title . ' - Task Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">{{ $task->title }}</h1>
            <p class="text-muted mb-0">{{ $task->description }}</p>
        </div>
        <div class="d-flex gap-2">
            @can('tasks.edit')
            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
            @endcan
            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Tasks
            </a>
        </div>
    </div>

    <!-- Task Details -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Task Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <div>
                                    <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'primary' : 'secondary') }} fs-6">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Priority</label>
                                <div>
                                    <span class="badge bg-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : 'info') }} fs-6">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Project</label>
                                <div>
                                    <a href="{{ route('projects.show', $task->project) }}" class="text-decoration-none">
                                        {{ $task->project->name }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Assigned To</label>
                                <div>{{ $task->assignedUser->name ?? 'Unassigned' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Due Date</label>
                                <div>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Estimated Hours</label>
                                <div>{{ $task->estimated_hours ? $task->estimated_hours . ' hours' : 'Not set' }}</div>
                            </div>
                        </div>
                    </div>

                    @if($task->parentTask)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Parent Task</label>
                        <div>
                            <a href="{{ route('tasks.show', $task->parentTask) }}" class="text-decoration-none">
                                {{ $task->parentTask->title }}
                            </a>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Created By</label>
                                <div>{{ $task->creator->name ?? 'Unknown' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Created At</label>
                                <div>{{ $task->created_at->format('M d, Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Task Stats -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Task Stats</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h4 mb-1 fw-bold text-primary">{{ $task->subtasks->count() }}</div>
                            <small class="text-muted">Subtasks</small>
                        </div>
                        <div class="col-6">
                            <div class="h4 mb-1 fw-bold text-success">{{ number_format($task->timeEntries->sum('duration') / 60, 2) }}</div>
                            <small class="text-muted">Hours Logged</small>
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
                        <a href="{{ route('tasks.create') }}?parent_task_id={{ $task->id }}" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle me-2"></i>Add Subtask
                        </a>
                        @php
                            $running = $task->timeEntries->where('user_id', auth()->id())->whereNull('end_time')->first();
                        @endphp
                        @can('time.create')
                            @if(!$running)
                            <form method="POST" action="{{ route('tasks.time.start', $task) }}">
                                @csrf
                                <button class="btn btn-outline-secondary w-100" title="Start timer">
                                    <i class="bi bi-play-circle me-2"></i>Start Timer
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('tasks.time.stop', $task) }}">
                                @csrf
                                <button class="btn btn-outline-danger w-100" title="Stop timer">
                                    <i class="bi bi-stop-circle me-2"></i>Stop Timer
                                </button>
                            </form>
                            @endif
                        @endcan
                        <a href="#" class="btn btn-outline-info">
                            <i class="bi bi-file-earmark me-2"></i>Add Attachment
                        </a>
                        @if($task->status !== 'completed')
                        <form action="{{ route('tasks.update', $task) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-outline-success w-100">
                                <i class="bi bi-check-circle me-2"></i>Mark Complete
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subtasks Section -->
    @if($task->subtasks->count() > 0)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Subtasks</h5>
            <a href="{{ route('tasks.create') }}?parent_task_id={{ $task->id }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add Subtask
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Task</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Assigned To</th>
                            <th>Due Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($task->subtasks as $subtask)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $subtask->title }}</div>
                                <small class="text-muted">{{ Str::limit($subtask->description, 50) }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $subtask->status === 'completed' ? 'success' : ($subtask->status === 'in_progress' ? 'primary' : 'secondary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $subtask->status)) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $subtask->priority === 'urgent' ? 'danger' : ($subtask->priority === 'high' ? 'warning' : 'info') }}">
                                    {{ ucfirst($subtask->priority) }}
                                </span>
                            </td>
                            <td>{{ $subtask->assignedUser->name ?? 'Unassigned' }}</td>
                            <td>
                                <small class="text-muted">
                                    {{ $subtask->due_date ? $subtask->due_date->format('M d, Y') : 'No due date' }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('tasks.show', $subtask) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('tasks.edit', $subtask) }}" class="btn btn-sm btn-outline-secondary">
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

    <!-- Time Entries Section -->
    @if($task->timeEntries->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Time Entries</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Description</th>
                            <th>Hours</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($task->timeEntries as $entry)
                        <tr>
                            <td>{{ $entry->user->name }}</td>
                            <td>{{ $entry->description ?? 'No description' }}</td>
                            <td>{{ number_format($entry->duration_in_hours, 2) }}</td>
                            <td>{{ optional($entry->start_time)->format('M d, Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@php
  $comments = \App\Models\Comment::with('user')->where('commentable_type', \App\Models\Task::class)->where('commentable_id', $task->id)->latest()->take(50)->get();
@endphp
@include('partials.comments', ['type'=>'task','id'=>$task->id,'comments'=>$comments])
@endsection
