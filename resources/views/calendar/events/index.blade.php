@extends('layouts.app')

@section('title', 'Calendar Events - Project Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Calendar Events</h1>
            <p class="text-muted mb-0">Manage your calendar events and schedules</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('calendar.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-calendar me-2"></i>View Calendar
            </a>
            @can('calendar.create')
            <a href="{{ route('calendar.events.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add Event
            </a>
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="meeting" {{ request('type') == 'meeting' ? 'selected' : '' }}>Meeting</option>
                        <option value="milestone" {{ request('type') == 'milestone' ? 'selected' : '' }}>Milestone</option>
                        <option value="reminder" {{ request('type') == 'reminder' ? 'selected' : '' }}>Reminder</option>
                        <option value="task" {{ request('type') == 'task' ? 'selected' : '' }}>Task</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-select">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search events..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('calendar.events.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Events List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($events->count() > 0)
                <div class="row">
                    @foreach($events as $event)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title mb-0">{{ $event->title }}</h5>
                                        <span class="badge bg-{{ $event->type === 'meeting' ? 'primary' : ($event->type === 'milestone' ? 'warning' : ($event->type === 'reminder' ? 'secondary' : 'success')) }}">
                                            {{ ucfirst($event->type) }}
                                        </span>
                                    </div>

                                    @if($event->description)
                                        <p class="card-text text-muted small">{{ Str::limit($event->description, 100) }}</p>
                                    @endif

                                    <div class="mb-3">
                                        <div class="row text-sm">
                                            <div class="col-6">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                <strong>Start:</strong><br>
                                                {{ $event->start_date->format('M j, Y g:i A') }}
                                            </div>
                                            <div class="col-6">
                                                <i class="bi bi-calendar-check me-1"></i>
                                                <strong>End:</strong><br>
                                                {{ $event->end_date->format('M j, Y g:i A') }}
                                            </div>
                                        </div>
                                    </div>

                                    @if($event->project)
                                        <div class="mb-3">
                                            <i class="bi bi-folder me-1"></i>
                                            <strong>Project:</strong> {{ $event->project->name }}
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="bi bi-person me-1"></i>{{ $event->user->name }}
                                        </small>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('calendar.events.show', $event) }}" class="btn btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('calendar.events.edit', $event) }}" class="btn btn-outline-secondary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="{{ route('calendar.events.destroy', $event) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger"
                                                        onclick="return confirm('Are you sure you want to delete this event?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $events->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x display-1 text-muted mb-3"></i>
                    <h4 class="text-muted">No Events Found</h4>
                    <p class="text-muted">There are no calendar events matching your criteria.</p>
                    @can('calendar.create')
                    <a href="{{ route('calendar.events.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Your First Event
                    </a>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
