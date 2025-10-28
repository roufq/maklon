@extends('layouts.app')

@section('title', 'Edit Calendar Event - Project Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Edit Calendar Event</h1>
            <p class="text-muted mb-0">Update event details and information</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('calendar.events.show', $event) }}" class="btn btn-outline-info">
                <i class="bi bi-eye me-2"></i>View Event
            </a>
            <a href="{{ route('calendar.events.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Events
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('calendar.events.update', $event) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                       id="title" name="title" value="{{ old('title', $event->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="4">{{ old('description', $event->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Start Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror"
                                       id="start_date" name="start_date"
                                       value="{{ old('start_date', $event->start_date->format('Y-m-d\TH:i')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">End Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror"
                                       id="end_date" name="end_date"
                                       value="{{ old('end_date', $event->end_date->format('Y-m-d\TH:i')) }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Event Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="meeting" {{ old('type', $event->type) == 'meeting' ? 'selected' : '' }}>Meeting</option>
                                    <option value="milestone" {{ old('type', $event->type) == 'milestone' ? 'selected' : '' }}>Milestone</option>
                                    <option value="reminder" {{ old('type', $event->type) == 'reminder' ? 'selected' : '' }}>Reminder</option>
                                    <option value="task" {{ old('type', $event->type) == 'task' ? 'selected' : '' }}>Task</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="project_id" class="form-label">Project</label>
                                <select class="form-select @error('project_id') is-invalid @enderror" id="project_id" name="project_id">
                                    <option value="">No Project</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ old('project_id', $event->project_id) == $project->id ? 'selected' : '' }}>
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror"
                                       id="location" name="location" value="{{ old('location', $event->location ?? '') }}"
                                       placeholder="Meeting room, online link, etc.">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="attendees" class="form-label">Attendees</label>
                                <input type="text" class="form-control @error('attendees') is-invalid @enderror"
                                       id="attendees" name="attendees" value="{{ old('attendees', $event->attendees ?? '') }}"
                                       placeholder="john@example.com, jane@example.com">
                                @error('attendees')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    Created by {{ $event->user->name }} on {{ $event->created_at->format('M j, Y g:i A') }}
                                    @if($event->updated_at != $event->created_at)
                                        <br>Last updated: {{ $event->updated_at->format('M j, Y g:i A') }}
                                    @endif
                                </small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('calendar.events.show', $event) }}" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Update Event
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');

    // Auto-update end date when start date changes (only if end date is empty or follows the pattern)
    startInput.addEventListener('change', function() {
        const startTime = new Date(this.value);
        const currentEndTime = new Date(endInput.value);

        // Only auto-update if the current end time is exactly 1 hour after start, or if end is empty
        const expectedEndTime = new Date(startTime.getTime() + 60 * 60 * 1000);

        if (!endInput.value || Math.abs(currentEndTime - expectedEndTime) < 60000) { // Within 1 minute
            endInput.value = expectedEndTime.toISOString().slice(0, 16);
        }
    });
});
</script>
@endsection
