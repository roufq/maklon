@extends('layouts.app')

@section('title', 'Calendar Event Details - Project Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">{{ $event->title }}</h1>
            <p class="text-muted mb-0">Event details and information</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('calendar.events.edit', $event) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-2"></i>Edit Event
            </a>
            <a href="{{ route('calendar.events.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Events
            </a>
        </div>
    </div>

    <!-- Event Details -->
    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h4 class="card-title mb-2">{{ $event->title }}</h4>
                            <span class="badge bg-{{ $event->type === 'meeting' ? 'primary' : ($event->type === 'milestone' ? 'warning' : ($event->type === 'reminder' ? 'secondary' : 'success')) }} fs-6">
                                {{ ucfirst($event->type) }}
                            </span>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">Created by {{ $event->user->name }}</small>
                            <br>
                            <small class="text-muted">{{ $event->created_at->format('M j, Y g:i A') }}</small>
                        </div>
                    </div>

                    @if($event->description)
                        <div class="mb-4">
                            <h6 class="fw-bold mb-2">Description</h6>
                            <p class="mb-0">{{ $event->description }}</p>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2">Start Date & Time</h6>
                            <p class="mb-0">
                                <i class="bi bi-calendar-event me-2"></i>
                                {{ $event->start_date->format('l, F j, Y \a\t g:i A') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2">End Date & Time</h6>
                            <p class="mb-0">
                                <i class="bi bi-calendar-check me-2"></i>
                                {{ $event->end_date->format('l, F j, Y \a\t g:i A') }}
                            </p>
                        </div>
                    </div>

                    @if($event->project)
                        <div class="mb-4">
                            <h6 class="fw-bold mb-2">Associated Project</h6>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-folder me-2"></i>
                                <a href="{{ route('projects.show', $event->project) }}" class="text-decoration-none">
                                    {{ $event->project->name }}
                                </a>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2">Duration</h6>
                            <p class="mb-0">
                                <i class="bi bi-clock me-2"></i>
                                {{ $event->start_date->diffForHumans($event->end_date, true) }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2">Status</h6>
                            <p class="mb-0">
                                @if($event->start_date->isFuture())
                                    <span class="badge bg-info">Upcoming</span>
                                @elseif($event->end_date->isPast())
                                    <span class="badge bg-secondary">Completed</span>
                                @else
                                    <span class="badge bg-success">In Progress</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('calendar.events.edit', $event) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Event
                        </a>
                        <a href="{{ route('calendar.index') }}" class="btn btn-outline-info">
                            <i class="bi bi-calendar me-2"></i>View in Calendar
                        </a>
                        <button class="btn btn-outline-success" onclick="shareEvent()">
                            <i class="bi bi-share me-2"></i>Share Event
                        </button>
                        <form method="POST" action="{{ route('calendar.events.destroy', $event) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100"
                                    onclick="return confirm('Are you sure you want to delete this event?')">
                                <i class="bi bi-trash me-2"></i>Delete Event
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Event Summary -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold">Event Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary mb-1">{{ $event->start_date->format('j') }}</h4>
                                <small class="text-muted">{{ $event->start_date->format('M') }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success mb-1">{{ $event->end_date->format('j') }}</h4>
                            <small class="text-muted">{{ $event->end_date->format('M') }}</small>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <small class="text-muted">Event Type</small>
                        <br>
                        <span class="badge bg-{{ $event->type === 'meeting' ? 'primary' : ($event->type === 'milestone' ? 'warning' : ($event->type === 'reminder' ? 'secondary' : 'success')) }}">
                            {{ ucfirst($event->type) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function shareEvent() {
    const eventUrl = window.location.href;
    const eventTitle = "{{ $event->title }}";

    if (navigator.share) {
        navigator.share({
            title: eventTitle,
            text: 'Check out this event: ' + eventTitle,
            url: eventUrl
        });
    } else {
        // Fallback for browsers that don't support Web Share API
        navigator.clipboard.writeText(eventUrl).then(function() {
            alert('Event link copied to clipboard!');
        });
    }
}
</script>
@endsection
