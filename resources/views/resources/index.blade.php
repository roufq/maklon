@extends('layouts.app')

@section('title', 'Resource Allocations')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Resource Allocations</h3>
                    <div class="card-tools">
                        <a href="{{ route('resources.reports') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-chart-bar"></i> Reports
                        </a>
                        @can('resources.create')
                        <a href="{{ route('resources.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add Allocation
                        </a>
                        @endcan
                    </div>
                </div>

                <!-- Filters -->
                <div class="card-body border-bottom">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="project_id" class="form-label">Project</label>
                            <select name="project_id" id="project_id" class="form-select">
                                <option value="">All Projects</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="user_id" class="form-label">User</label>
                            <select name="user_id" id="user_id" class="form-select">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="allocation_type" class="form-label">Type</label>
                            <select name="allocation_type" id="allocation_type" class="form-select">
                                <option value="">All Types</option>
                                <option value="planned" {{ request('allocation_type') == 'planned' ? 'selected' : '' }}>Planned</option>
                                <option value="actual" {{ request('allocation_type') == 'actual' ? 'selected' : '' }}>Actual</option>
                                <option value="forecast" {{ request('allocation_type') == 'forecast' ? 'selected' : '' }}>Forecast</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-secondary btn-sm">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ route('resources.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>User</th>
                                <th>Task</th>
                                <th>Hours</th>
                                <th>Period</th>
                                <th>Type</th>
                                <th>Utilization</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allocations as $allocation)
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
                                    <td>
                                        @if($allocation->task)
                                            <a href="{{ route('tasks.show', $allocation->task) }}">
                                                {{ $allocation->task->title }}
                                            </a>
                                        @else
                                            <span class="text-muted">General</span>
                                        @endif
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
                                        <div class="progress" style="width: 80px;">
                                            <div class="progress-bar
                                                @if($allocation->utilization_percentage > 100) bg-danger
                                                @elseif($allocation->utilization_percentage > 80) bg-warning
                                                @else bg-success
                                                @endif"
                                                style="width: {{ min($allocation->utilization_percentage, 100) }}%"}>
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ number_format($allocation->utilization_percentage, 1) }}%</small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('resources.show', $allocation) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @can('resources.edit')
                                            <a href="{{ route('resources.edit', $allocation) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('resources.delete')
                                            <form action="{{ route('resources.destroy', $allocation) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                        <p>No resource allocations found.</p>
                                        @can('resources.create')
                                        <a href="{{ route('resources.create') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> Create First Allocation
                                        </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($allocations->hasPages())
                    <div class="card-footer">
                        {{ $allocations->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form when filters change
    $('#project_id, #user_id, #allocation_type').change(function() {
        $(this).closest('form').submit();
    });
});
</script>
@endsection
