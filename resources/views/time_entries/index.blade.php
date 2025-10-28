@extends('layouts.app')

@section('title', 'Time Entries - Project Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Time Entries</h1>
            <p class="text-muted mb-0">Track your time across projects and tasks</p>
        </div>
        @can('time.create')
        <a href="{{ route('time_entries.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Log Time
        </a>
        @endcan
    </div>

    <!-- Time Entries Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Description</th>
                            <th>User</th>
                            <th>Project</th>
                            <th>Task</th>
                            <th>Duration</th>
                            <th>Billable</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($timeEntries as $entry)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $entry->description ?? 'No description' }}</div>
                            </td>
                            <td>{{ $entry->user->name }}</td>
                            <td>{{ $entry->project->name ?? 'No project' }}</td>
                            <td>{{ $entry->task->title ?? 'No task' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $entry->duration }}h</span>
                            </td>
                            <td>
                                @if($entry->billable)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $entry->start_time->format('M d, Y') }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('time_entries.show', $entry) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('time_entries.edit', $entry) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('time_entries.destroy', $entry) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Are you sure you want to delete this time entry?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-clock text-muted fs-1 mb-3"></i>
                                <h6 class="text-muted">No Time Entries Yet</h6>
                                <p class="text-muted small">Start logging your time to track productivity.</p>
                                @can('time.create')
                                <a href="{{ route('time_entries.create') }}" class="btn btn-primary">Log Time</a>
                                @endcan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($timeEntries->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $timeEntries->links() }}
    </div>
    @endif
</div>
@endsection
