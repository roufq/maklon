@extends('layouts.app')

@section('title', 'Edit Task - ' . $task->title)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Edit Task</h1>
            <p class="text-muted mb-0">Update task information</p>
        </div>
        <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Task
        </a>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('tasks.update', $task) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="title" class="form-label fw-bold">Task Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                       id="title" name="title" value="{{ old('title', $task->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status', $task->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description', $task->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="priority" class="form-label fw-bold">Priority <span class="text-danger">*</span></label>
                                <select class="form-select @error('priority') is-invalid @enderror"
                                        id="priority" name="priority" required>
                                    <option value="">Select Priority</option>
                                    <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority', $task->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="due_date" class="form-label fw-bold">Due Date</label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                       id="due_date" name="due_date" value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="estimated_hours" class="form-label fw-bold">Estimated Hours</label>
                                <input type="number" class="form-control @error('estimated_hours') is-invalid @enderror"
                                       id="estimated_hours" name="estimated_hours" value="{{ old('estimated_hours', $task->estimated_hours) }}" step="0.5" min="0">
                                @error('estimated_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="project_id" class="form-label fw-bold">Project <span class="text-danger">*</span></label>
                                <select class="form-select @error('project_id') is-invalid @enderror"
                                        id="project_id" name="project_id" required>
                                    <option value="">Select Project</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="assigned_to" class="form-label fw-bold">Assigned To</label>
                                <select class="form-select @error('assigned_to') is-invalid @enderror"
                                        id="assigned_to" name="assigned_to">
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="parent_task_id" class="form-label fw-bold">Parent Task</label>
                            <select class="form-select @error('parent_task_id') is-invalid @enderror"
                                    id="parent_task_id" name="parent_task_id">
                                <option value="">Select Parent Task (Optional)</option>
                                @foreach($parentTasks as $parentTask)
                                    <option value="{{ $parentTask->id }}" {{ old('parent_task_id', $task->parent_task_id) == $parentTask->id ? 'selected' : '' }}>
                                        {{ $parentTask->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_task_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Work Station</label>
                                <select class="form-select" name="work_station_id">
                                    <option value="">-</option>
                                    @isset($workStations)
                                    @foreach($workStations as $ws)
                                        <option value="{{ $ws->id }}" @selected(old('work_station_id', $task->work_station_id)==$ws->id)>{{ $ws->name }}</option>
                                    @endforeach
                                    @endisset
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Scheduled Start</label>
                                <input type="datetime-local" name="scheduled_start" class="form-control" value="{{ optional($task->scheduled_start)->format('Y-m-d\\TH:i') }}" />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Scheduled End</label>
                                <input type="datetime-local" name="scheduled_end" class="form-control" value="{{ optional($task->scheduled_end)->format('Y-m-d\\TH:i') }}" />
                            </div>
                        </div>
<div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Task
                            </button>
                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Task Guidelines</h5>
                </div>
                <div class="card-body">
                    <h6>Task Status</h6>
                    <ul class="small mb-3">
                        <li><strong>Pending:</strong> Not started yet</li>
                        <li><strong>In Progress:</strong> Currently working on</li>
                        <li><strong>Completed:</strong> Finished successfully</li>
                        <li><strong>Cancelled:</strong> Terminated early</li>
                    </ul>

                    <h6>Priority Levels</h6>
                    <ul class="small mb-0">
                        <li><strong>Low:</strong> Can be done later</li>
                        <li><strong>Medium:</strong> Standard priority</li>
                        <li><strong>High:</strong> Should be done soon</li>
                        <li><strong>Urgent:</strong> Requires immediate attention</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

