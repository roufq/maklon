@extends('layouts.app')

@section('title', 'Time Tracking Report')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Time Tracking Report</h1>
            <p class="text-muted mb-0">Analyze time spent on projects and tasks</p>
        </div>
        <div>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-2"></i>Back to Reports
            </a>
            <a href="{{ route('reports.exportTimeTracking', request()->query()) }}" class="btn btn-primary">
                <i class="bi bi-download me-2"></i>Export CSV
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h4 class="text-primary mb-1">{{ number_format($totalHours, 1) }}</h4>
                    <p class="text-muted mb-0">Total Hours</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h4 class="text-success mb-1">{{ number_format($billableHours, 1) }}</h4>
                    <p class="text-muted mb-0">Billable Hours</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h4 class="text-warning mb-1">{{ number_format($nonBillableHours, 1) }}</h4>
                    <p class="text-muted mb-0">Non-Billable Hours</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h4 class="text-info mb-1">{{ $timeEntries->count() }}</h4>
                    <p class="text-muted mb-0">Time Entries</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="user_id" class="form-label">User</label>
                    <select name="user_id" id="user_id" class="form-select">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="project_id" class="form-label">Project</label>
                    <select name="project_id" id="project_id" class="form-select">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search me-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('reports.timeTracking') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-2"></i>Clear Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Time Entries Table -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Time Entries</h5>
                </div>
                <div class="card-body">
                    @if($timeEntries->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Project</th>
                                    <th>Task</th>
                                    <th>Start Time</th>
                                    <th>Duration</th>
                                    <th>Billable</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($timeEntries as $entry)
                                <tr>
                                    <td>{{ $entry->user->name }}</td>
                                    <td>{{ $entry->project->name ?? 'N/A' }}</td>
                                    <td>{{ $entry->task->title ?? 'N/A' }}</td>
                                    <td>{{ $entry->start_time->format('M d, H:i') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $entry->billable ? 'success' : 'secondary' }}">
                                            {{ number_format($entry->duration / 60, 1) }}h
                                        </span>
                                    </td>
                                    <td>
                                        @if($entry->billable)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('time_entries.show', $entry) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-clock-history text-muted" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mt-3">No time entries found</h5>
                        <p class="text-muted">Try adjusting your filters or start tracking time.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Summary Charts -->
        <div class="col-lg-4">
            <!-- User Summary -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Hours by User</h6>
                </div>
                <div class="card-body">
                    @if($userSummary->count() > 0)
                    @foreach($userSummary as $user => $stats)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-truncate" style="max-width: 120px;">{{ $user }}</span>
                        <span class="badge bg-primary">{{ number_format($stats['total_hours'], 1) }}h</span>
                    </div>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar bg-primary" style="width: {{ ($stats['total_hours'] / max($userSummary->pluck('total_hours')->toArray())) * 100 }}%"></div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-muted text-center mb-0">No data available</p>
                    @endif
                </div>
            </div>

            <!-- Project Summary -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Hours by Project</h6>
                </div>
                <div class="card-body">
                    @if($projectSummary->count() > 0)
                    @foreach($projectSummary as $project => $stats)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-truncate" style="max-width: 120px;">{{ $project ?: 'No Project' }}</span>
                        <span class="badge bg-success">{{ number_format($stats['total_hours'], 1) }}h</span>
                    </div>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: {{ ($stats['total_hours'] / max($projectSummary->pluck('total_hours')->toArray())) * 100 }}%"></div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-muted text-center mb-0">No data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
