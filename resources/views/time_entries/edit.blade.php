@extends('layouts.app')

@section('title', 'Edit Time Entry')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Edit Time Entry</h1>
            <p class="text-muted mb-0">Update time entry information</p>
        </div>
        <a href="{{ route('time_entries.show', $timeEntry) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Time Entry
        </a>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('time_entries.update', $timeEntry) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description', $timeEntry->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_time" class="form-label fw-bold">Start Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror"
                                       id="start_time" name="start_time"
                                       value="{{ old('start_time', $timeEntry->start_time ? $timeEntry->start_time->format('Y-m-d\TH:i') : '') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="end_time" class="form-label fw-bold">End Time</label>
                                <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror"
                                       id="end_time" name="end_time"
                                       value="{{ old('end_time', $timeEntry->end_time ? $timeEntry->end_time->format('Y-m-d\TH:i') : '') }}">
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="duration" class="form-label fw-bold">Duration (Hours) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('duration') is-invalid @enderror"
                                       id="duration" name="duration" value="{{ old('duration', $timeEntry->duration) }}"
                                       step="0.25" min="0" required>
                                @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Billable</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                           id="billable" name="billable" value="1"
                                           {{ old('billable', $timeEntry->billable) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="billable">
                                        This time entry is billable
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="project_id" class="form-label fw-bold">Project</label>
                                <select class="form-select @error('project_id') is-invalid @enderror"
                                        id="project_id" name="project_id">
                                    <option value="">Select Project</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ old('project_id', $timeEntry->project_id) == $project->id ? 'selected' : '' }}>
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
                                        <option value="{{ $task->id }}" {{ old('task_id', $timeEntry->task_id) == $task->id ? 'selected' : '' }}>
                                            {{ $task->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('task_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Time Entry
                            </button>
                            <a href="{{ route('time_entries.show', $timeEntry) }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Time Entry Guidelines</h5>
                </div>
                <div class="card-body">
                    <h6>Duration</h6>
                    <ul class="small mb-3">
                        <li>Enter time in decimal hours (e.g., 1.5 for 1.5 hours)</li>
                        <li>Minimum 0.25 hours (15 minutes)</li>
                        <li>Maximum reasonable daily hours</li>
                    </ul>

                    <h6>Billable vs Non-billable</h6>
                    <ul class="small mb-3">
                        <li><strong>Billable:</strong> Client-facing work</li>
                        <li><strong>Non-billable:</strong> Internal meetings, admin tasks</li>
                    </ul>

                    <h6>Best Practices</h6>
                    <ul class="small mb-0">
                        <li>Log time as you work, not at the end of the day</li>
                        <li>Be specific in descriptions</li>
                        <li>Always associate with a project/task</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
