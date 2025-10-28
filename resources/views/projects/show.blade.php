@extends('layouts.app')

@section('title', $project->name . ' - Project Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">{{ $project->name }}</h1>
            <p class="text-muted mb-0">{{ $project->description }}</p>
        </div>
        <div class="d-flex gap-2">
            @can('projects.edit')
            <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
            @endcan
            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Projects
            </a>
        </div>
    </div>

    <!-- Project Details -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Project Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <div>
                                    <span class="badge bg-{{ $project->status === 'active' ? 'success' : ($project->status === 'completed' ? 'primary' : 'secondary') }} fs-6">
                                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Priority</label>
                                <div>
                                    <span class="badge bg-{{ $project->priority === 'urgent' ? 'danger' : ($project->priority === 'high' ? 'warning' : 'info') }} fs-6">
                                        {{ ucfirst($project->priority) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Start Date</label>
                                <div>{{ $project->start_date ? $project->start_date->format('M d, Y') : 'Not set' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">End Date</label>
                                <div>{{ $project->end_date ? $project->end_date->format('M d, Y') : 'Not set' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Budget</label>
                                <div>{{ $project->budget ? '$' . number_format($project->budget, 2) : 'Not set' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Team</label>
                                <div>{{ $project->team->name ?? 'No team assigned' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Progress</label>
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1 me-3" style="height: 10px;">
                                <div class="progress-bar bg-primary" style="width: {{ $project->progress }}%"></div>
                            </div>
                            <span class="fw-bold">{{ $project->progress }}%</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Customer</label>
                                <div>{{ $project->customer->name ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Order Quantity</label>
                                <div>{{ $project->order_quantity ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Due Date</label>
                                <div>{{ optional($project->due_date)->format('M d, Y') ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Production Status</label>
                                <div>{{ ucfirst(str_replace('_',' ',$project->production_status ?? 'draft')) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">BPOM Registration</label>
                                <div>
                                    @if($project->bpomRegistration)
                                        {{ $project->bpomRegistration->product_name }} — {{ $project->bpomRegistration->registration_number }} ({{ ucfirst($project->bpomRegistration->status) }})
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Created By</label>
                                <div>{{ $project->creator->name ?? 'Unknown' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Created At</label>
                                <div>{{ $project->created_at->format('M d, Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Project Stats -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Project Stats</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h4 mb-1 fw-bold text-primary">{{ $project->tasks->count() }}</div>
                            <small class="text-muted">Tasks</small>
                        </div>
                        <div class="col-6">
                            <div class="h4 mb-1 fw-bold text-success">{{ $project->tasks->where('status', 'completed')->count() }}</div>
                            <small class="text-muted">Completed</small>
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
                        @can('tasks.create')
                        <a href="{{ route('tasks.create') }}?project_id={{ $project->id }}" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle me-2"></i>Add Task
                        </a>
                        @endcan
                        <a href="#" class="btn btn-outline-secondary">
                            <i class="bi bi-clock me-2"></i>Log Time
                        </a>
                        <a href="#" class="btn btn-outline-info">
                            <i class="bi bi-file-earmark me-2"></i>Add Attachment
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks Section -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Project Tasks</h5>
            @can('tasks.create')
            <a href="{{ route('tasks.create') }}?project_id={{ $project->id }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add Task
            </a>
            @endcan
        </div>
        <div class="card-body">
            @if($project->tasks->count() > 0)
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
                            @foreach($project->tasks as $task)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $task->title }}</div>
                                    <small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'primary' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : 'info') }}">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </td>
                                <td>{{ $task->assignedUser->name ?? 'Unassigned' }}</td>
                                <td>
                                    <small class="text-muted">
                                        {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-check-circle text-muted fs-1 mb-3"></i>
                    <h6 class="text-muted">No Tasks Yet</h6>
                    <p class="text-muted small">Create your first task for this project.</p>
                    @can('tasks.create')
                    <a href="{{ route('tasks.create') }}?project_id={{ $project->id }}" class="btn btn-primary">Create Task</a>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</div>
@php
  $comments = \App\Models\Comment::with('user')->where('commentable_type', \App\Models\Project::class)->where('commentable_id', $project->id)->latest()->take(50)->get();
@endphp
@include('partials.comments', ['type'=>'project','id'=>$project->id,'comments'=>$comments])
@endsection
