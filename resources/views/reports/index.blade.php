@extends('layouts.app')

@section('title', 'Reports Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Reports Dashboard</h1>
            <p class="text-muted mb-0">Comprehensive project management reports and analytics</p>
        </div>
    </div>

    <!-- Report Cards -->
    <div class="row">
        <!-- Project Progress Report -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-bar-chart-line-fill text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title fw-bold">Project Progress</h5>
                    <p class="card-text text-muted">Track project completion rates, task progress, and team performance across all projects.</p>
                    <a href="{{ route('reports.projectProgress') }}" class="btn btn-primary">
                        <i class="bi bi-eye me-2"></i>View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Time Tracking Report -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-clock-history text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title fw-bold">Time Tracking</h5>
                    <p class="card-text text-muted">Analyze time spent on projects and tasks, including billable vs non-billable hours.</p>
                    <a href="{{ route('reports.timeTracking') }}" class="btn btn-success">
                        <i class="bi bi-clock me-2"></i>View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Team Performance Report -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-people-fill text-info" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title fw-bold">Team Performance</h5>
                    <p class="card-text text-muted">Monitor team productivity, project completion rates, and resource utilization.</p>
                    <a href="{{ route('reports.teamPerformance') }}" class="btn btn-info">
                        <i class="bi bi-people me-2"></i>View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Resource Utilization Report -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-graph-up text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title fw-bold">Resource Utilization</h5>
                    <p class="card-text text-muted">Monitor team resource allocation and utilization rates across projects.</p>
                    <a href="{{ route('resources.utilization') }}" class="btn btn-warning">
                        <i class="bi bi-graph-up me-2"></i>View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- BPOM Compliance Report -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-shield-check text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title fw-bold">BPOM Compliance</h5>
                    <p class="card-text text-muted">Monitor BPOM registration status and upcoming expiries across products.</p>
                    <a href="{{ route('reports.bpom') }}" class="btn btn-danger">
                        <i class="bi bi-eye me-2"></i>View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Batch & QC Status Report -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-clipboard2-check text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title fw-bold">Batch & QC Status</h5>
                    <p class="card-text text-muted">See QC pass/fail and eligibility for delivery per batch.</p>
                    <a href="{{ route('reports.batchQc') }}" class="btn btn-primary">
                        <i class="bi bi-eye me-2"></i>View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Inventory Health Report -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-box-seam text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title fw-bold">Inventory Health</h5>
                    <p class="card-text text-muted">Low stock detection and basic turnover over a time window.</p>
                    <a href="{{ route('reports.inventory') }}" class="btn btn-warning">
                        <i class="bi bi-eye me-2"></i>View Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-folder-fill text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Total Projects</h6>
                            <h4 class="mb-0 fw-bold">{{ \App\Models\Project::count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Completed Tasks</h6>
                            <h4 class="mb-0 fw-bold">{{ \App\Models\Task::where('status', 'completed')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-clock-fill text-warning" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Total Hours</h6>
                            <h4 class="mb-0 fw-bold">{{ number_format(\App\Models\TimeEntry::sum('duration_minutes') / 60, 1) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-people-fill text-info" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Active Teams</h6>
                            <h4 class="mb-0 fw-bold">{{ \App\Models\Team::count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
