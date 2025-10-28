@extends('layouts.app')

@section('title', 'Log Time - Project Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Log Time</h1>
            <p class="text-muted mb-0">Record your time spent on projects and tasks</p>
        </div>
        <a href="{{ route('time_entries.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Time Entries
        </a>
    </div>

    <!-- Create Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('time_entries.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3"
                                      placeholder="What did you work on?">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_time" class="form-label fw-bold">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_time') is-invalid @enderror"
                                       id="start_time" name="start_time" value="{{ old('start_time', date('Y-m-d')) }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="duration" class="form-label fw-bold">Duration (hours) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('duration') is-invalid @enderror"
                                       id="duration" name="duration" value="{{ old('duration') }}" step="0.25" min="0.25" max="24" required>
                                @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="project_id" class="form-label fw-bold">Project</label>
                                <select class="form-select @error('project_id') is-invalid @enderror"
                                        id="project_id" name="project_id">
                                    <option value="">Select Project</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="task_id" class="form-label fw-bold">Task</label>
                                <select class="form-select @error('task_id') is-invalid @enderror"
                                        id="task_id" name="task_id">
                                    <option value="">Select Task</option>
                                    @foreach($tasks as $task)
                                        <option value="{{ $task->id }}" {{ old('task_id') == $task->id ? 'selected' : '' }}>
                                            {{ $task->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('task_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="billable" name="billable" value="1"
                                       {{ old('billable') ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="billable">
                                    Billable Time
                                </label>
                                <div class="form-text">Check if this time should be billed to the client</div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Log Time
                            </button>
                            <a href="{{ route('time_entries.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Time Tracking Guidelines</h5>
                </div>
                <div class="card-body">
                    <h6>Best Practices</h6>
                    <ul class="small mb-3">
                        <li>Log time as you work, not at the end of the day</li>
                        <li>Be specific in your descriptions</li>
                        <li>Use quarter-hour increments (0.25, 0.5, 0.75, 1.0)</li>
                        <li>Only mark time as billable if it directly benefits the client</li>
                    </ul>

                    <h6>Duration Examples</h6>
                    <ul class="small mb-3">
                        <li>15 minutes = 0.25 hours</li>
                        <li>30 minutes = 0.5 hours</li>
                        <li>45 minutes = 0.75 hours</li>
                        <li>1 hour = 1.0 hours</li>
                    </ul>

                    <h6>Billable vs Non-Billable</h6>
                    <ul class="small mb-0">
                        <li><strong>Billable:</strong> Client meetings, development work</li>
                        <li><strong>Non-billable:</strong> Internal meetings, administrative tasks</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
