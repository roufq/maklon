@extends('layouts.app')

@section('title', 'Resource Reports')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Resource Reports</h1>
            <p class="text-muted mb-0">Comprehensive resource allocation and utilization reports</p>
        </div>
        <div>
            <a href="{{ route('resources.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Resources
            </a>
        </div>
    </div>

    <!-- Report Cards -->
    <div class="row">
        <!-- Resource Utilization Report -->
        <div class="col-lg-6 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-graph-up text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title fw-bold">Resource Utilization</h5>
                    <p class="card-text text-muted">Monitor team resource allocation and utilization rates across projects.</p>
                    <a href="{{ route('resources.utilization') }}" class="btn btn-warning btn-lg">
                        <i class="bi bi-graph-up me-2"></i>View Utilization Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Resource Allocation Summary -->
        <div class="col-lg-6 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-calendar-check text-info" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="card-title fw-bold">Allocation Summary</h5>
                    <p class="card-text text-muted">View detailed resource allocation summaries and project assignments.</p>
                    <a href="{{ route('resources.index') }}" class="btn btn-info btn-lg">
                        <i class="bi bi-calendar-check me-2"></i>View Allocations
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
                            <i class="bi bi-people-fill text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Total Allocations</h6>
                            <h4 class="mb-0 fw-bold">{{ \App\Models\ResourceAllocation::count() }}</h4>
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
                            <i class="bi bi-clock-fill text-success" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Total Hours</h6>
                            <h4 class="mb-0 fw-bold">{{ number_format(\App\Models\ResourceAllocation::sum('allocated_hours'), 1) }}</h4>
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
                            <i class="bi bi-folder-fill text-warning" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Active Projects</h6>
                            <h4 class="mb-0 fw-bold">{{ \App\Models\ResourceAllocation::distinct('project_id')->count() }}</h4>
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
                            <i class="bi bi-person-fill text-info" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">Allocated Users</h6>
                            <h4 class="mb-0 fw-bold">{{ \App\Models\ResourceAllocation::distinct('user_id')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Allocations -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Resource Allocations</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Project</th>
                                    <th>User</th>
                                    <th>Hours</th>
                                    <th>Period</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(\App\Models\ResourceAllocation::with(['project', 'user'])->latest()->take(5)->get() as $allocation)
                                    <tr>
                                        <td>
                                            <a href="{{ route('projects.show', $allocation->project) }}">
                                                {{ $allocation->project->name }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($allocation->user->avatar)
                                                    <img src="{{ asset('storage/' . $allocation->user->avatar) }}" class="img-circle elevation-2" alt="User Image" style="width: 30px; height: 30px; margin-right: 10px;">
                                                @else
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; margin-right: 10px; font-size: 12px;">
                                                        {{ substr($allocation->user->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                {{ $allocation->user->name }}
                                            </div>
                                        </td>
                                        <td>{{ $allocation->allocated_hours }}h</td>
                                        <td>
                                            {{ $allocation->start_date->format('M d') }} - {{ $allocation->end_date->format('M d, Y') }}
                                        </td>
                                        <td>
                                            <span class="badge
                                                @if($allocation->allocation_type == 'planned') badge-warning
                                                @elseif($allocation->allocation_type == 'actual') badge-success
                                                @else badge-info
                                                @endif">
                                                {{ ucfirst($allocation->allocation_type) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($allocation->end_date->isPast())
                                                <span class="badge badge-secondary">Completed</span>
                                            @elseif($allocation->start_date->isPast() && $allocation->end_date->isFuture())
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-primary">Upcoming</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p>No resource allocations found.</p>
                                            <a href="{{ route('resources.create') }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus"></i> Create First Allocation
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
