@extends('layouts.app')

@section('title', 'Time Entry Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Time Entry Details</h1>
            <p class="text-muted mb-0">{{ $timeEntry->description ?? 'No description' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('time_entries.edit', $timeEntry) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
            <a href="{{ route('time_entries.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Time Entries
            </a>
        </div>
    </div>

    <!-- Time Entry Details -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Time Entry Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">User</label>
                                <div>{{ $timeEntry->user->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Billable</label>
                                <div>
                                    @if($timeEntry->billable)
                                        <span class="badge bg-success">Billable</span>
                                    @else
                                        <span class="badge bg-secondary">Non-billable</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Start Time</label>
                                <div>{{ $timeEntry->start_time->format('M d, Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">End Time</label>
                                <div>{{ $timeEntry->end_time ? $timeEntry->end_time->format('M d, Y H:i') : 'Not set' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Duration</label>
                                <div class="h4 mb-0 fw-bold text-primary">{{ number_format($timeEntry->duration_in_hours, 2) }} hours</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Date</label>
                                <div>{{ $timeEntry->start_time->format('M d, Y') }}</div>
                            </div>
                        </div>
                    </div>

                    @if($timeEntry->project)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Project</label>
                        <div>
                            <a href="{{ route('projects.show', $timeEntry->project) }}" class="text-decoration-none">
                                {{ $timeEntry->project->name }}
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($timeEntry->task)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Task</label>
                        <div>
                            <a href="{{ route('tasks.show', $timeEntry->task) }}" class="text-decoration-none">
                                {{ $timeEntry->task->title }}
                            </a>
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <div>{{ $timeEntry->description ?? 'No description provided' }}</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Created At</label>
                                <div>{{ $timeEntry->created_at->format('M d, Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Last Updated</label>
                                <div>{{ $timeEntry->updated_at->format('M d, Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Time Stats -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Time Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-12">
                            <div class="h3 mb-1 fw-bold text-primary">{{ number_format($timeEntry->duration_in_hours, 2) }}</div>
                            <small class="text-muted">Hours Logged</small>
                        </div>
                    </div>
                    <hr>
                    <div class="small text-muted">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Billable:</span>
                            <span>{{ $timeEntry->billable ? 'Yes' : 'No' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Rate:</span>
                            <span>$50/hour</span>
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
                        <a href="{{ route('time_entries.create') }}" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle me-2"></i>Log More Time
                        </a>
                        <a href="#" class="btn btn-outline-secondary">
                            <i class="bi bi-clock me-2"></i>Start Timer
                        </a>
                        <a href="#" class="btn btn-outline-info">
                            <i class="bi bi-file-earmark me-2"></i>Export Report
                        </a>
                        <form action="{{ route('time_entries.destroy', $timeEntry) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100"
                                    onclick="return confirm('Are you sure you want to delete this time entry?')">
                                <i class="bi bi-trash me-2"></i>Delete Entry
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
