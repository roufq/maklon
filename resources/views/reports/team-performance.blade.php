@extends('layouts.app')

@section('title', 'Team Performance Report')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Team Performance Report</h1>
            <p class="text-muted mb-0">Monitor team productivity and project completion rates</p>
        </div>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Reports
        </a>
    </div>

    <!-- Team Performance Cards -->
    <div class="row">
        @foreach($teamStats as $stat)
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $stat['team']->name }}</h5>
                    <span class="badge bg-primary">{{ $stat['member_count'] }} members</span>
                </div>
                <div class="card-body">
                    <!-- Team Stats -->
                    <div class="row text-center mb-4">
                        <div class="col-3">
                            <div class="h5 mb-1 fw-bold text-primary">{{ $stat['total_projects'] }}</div>
                            <small class="text-muted">Projects</small>
                        </div>
                        <div class="col-3">
                            <div class="h5 mb-1 fw-bold text-success">{{ $stat['active_projects'] }}</div>
                            <small class="text-muted">Active</small>
                        </div>
                        <div class="col-3">
                            <div class="h5 mb-1 fw-bold text-info">{{ $stat['total_tasks'] }}</div>
                            <small class="text-muted">Tasks</small>
                        </div>
                        <div class="col-3">
                            <div class="h5 mb-1 fw-bold text-warning">{{ number_format($stat['total_time'] / 60, 1) }}</div>
                            <small class="text-muted">Hours</small>
                        </div>
                    </div>

                    <!-- Completion Rate -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Task Completion Rate</span>
                            <span class="fw-bold">{{ $stat['completion_rate'] }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-{{ $stat['completion_rate'] >= 80 ? 'success' : ($stat['completion_rate'] >= 60 ? 'warning' : 'danger') }}"
                                 style="width: {{ $stat['completion_rate'] }}%"></div>
                        </div>
                    </div>

                    <!-- Team Details -->
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Leader</small>
                            <div class="fw-bold">{{ $stat['team']->leader->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Type</small>
                            <div class="fw-bold">{{ ucfirst(str_replace('_', ' ', $stat['team']->type)) }}</div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="mt-3">
                        <a href="{{ route('teams.show', $stat['team']) }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-eye me-2"></i>View Team Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($teamStats->count() == 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
            <h5 class="text-muted mt-3">No teams found</h5>
            <p class="text-muted">Create teams to start tracking performance metrics.</p>
            <a href="{{ route('teams.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create Team
            </a>
        </div>
    </div>
    @endif

    <!-- Overall Statistics -->
    @if($teamStats->count() > 0)
    <div class="row mt-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h4 class="text-primary mb-1">{{ $teamStats->count() }}</h4>
                    <p class="text-muted mb-0">Total Teams</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h4 class="text-success mb-1">{{ $teamStats->sum('total_projects') }}</h4>
                    <p class="text-muted mb-0">Total Projects</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h4 class="text-warning mb-1">{{ number_format($teamStats->avg('completion_rate'), 1) }}%</h4>
                    <p class="text-muted mb-0">Avg Completion Rate</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <h4 class="text-info mb-1">{{ number_format($teamStats->sum('total_time') / 60, 1) }}</h4>
                    <p class="text-muted mb-0">Total Hours</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Comparison Chart -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Team Performance Comparison</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Team Name</th>
                            <th>Members</th>
                            <th>Projects</th>
                            <th>Active Projects</th>
                            <th>Tasks Completed</th>
                            <th>Completion Rate</th>
                            <th>Total Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teamStats->sortByDesc('completion_rate') as $stat)
                        <tr>
                            <td class="fw-bold">{{ $stat['team']->name }}</td>
                            <td>{{ $stat['member_count'] }}</td>
                            <td>{{ $stat['total_projects'] }}</td>
                            <td>{{ $stat['active_projects'] }}</td>
                            <td>{{ $stat['completed_tasks'] }}/{{ $stat['total_tasks'] }}</td>
                            <td>
                                <span class="badge bg-{{ $stat['completion_rate'] >= 80 ? 'success' : ($stat['completion_rate'] >= 60 ? 'warning' : 'danger') }}">
                                    {{ $stat['completion_rate'] }}%
                                </span>
                            </td>
                            <td>{{ number_format($stat['total_time'] / 60, 1) }}h</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
