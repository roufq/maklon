@extends('layouts.app')

@section('title', 'Resource Allocation Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Resource Allocation Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('resources.edit', $resource) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('resources.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Project:</label>
                                <p>
                                    <a href="{{ route('projects.show', $resource->project) }}">
                                        {{ $resource->project->name }}
                                    </a>
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">User:</label>
                                <p>
                                    <div class="d-flex align-items-center">
                                        @if($resource->user->avatar)
                                            <img src="{{ asset('storage/' . $resource->user->avatar) }}" class="img-circle elevation-2" alt="User Image" style="width: 30px; height: 30px; margin-right: 10px;">
                                        @else
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; margin-right: 10px; font-size: 12px;">
                                                {{ substr($resource->user->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <a href="#">{{ $resource->user->name }}</a>
                                        @if($resource->user->position)
                                            <small class="text-muted ml-2">({{ $resource->user->position }})</small>
                                        @endif
                                    </div>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Task:</label>
                                <p>
                                    @if($resource->task)
                                        <a href="{{ route('tasks.show', $resource->task) }}">
                                            {{ $resource->task->title }}
                                        </a>
                                    @else
                                        <span class="text-muted">General Allocation (No Specific Task)</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Allocation Type:</label>
                                <p>
                                    <span class="badge
                                        @if($resource->allocation_type == 'planned') badge-warning
                                        @elseif($resource->allocation_type == 'actual') badge-success
                                        @else badge-info
                                        @endif">
                                        {{ ucfirst($resource->allocation_type) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Allocated Hours:</label>
                                <p>{{ $resource->allocated_hours }} hours per day</p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Start Date:</label>
                                <p>{{ $resource->start_date->format('M d, Y') }}</p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">End Date:</label>
                                <p>{{ $resource->end_date->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Duration:</label>
                                <p>{{ $resource->start_date->diffInDays($resource->end_date) + 1 }} days</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Utilization:</label>
                                <p>
                                    <div class="progress" style="width: 200px;">
                                        <div class="progress-bar
                                            @if($resource->utilization_percentage > 100) bg-danger
                                            @elseif($resource->utilization_percentage > 80) bg-warning
                                            @else bg-success
                                            @endif"
                                            style="width: {{ min($resource->utilization_percentage, 100) }}%">
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ number_format($resource->utilization_percentage, 1) }}% of available time</small>
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($resource->notes)
                        <div class="form-group">
                            <label class="font-weight-bold">Notes:</label>
                            <p>{{ $resource->notes }}</p>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Created:</label>
                                <p>{{ $resource->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Last Updated:</label>
                                <p>{{ $resource->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="btn-group">
                        <a href="{{ route('resources.edit', $resource) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Allocation
                        </a>
                        <form action="{{ route('resources.destroy', $resource) }}" method="POST" class="d-inline ml-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this resource allocation?')">
                                <i class="fas fa-trash"></i> Delete Allocation
                            </button>
                        </form>
                    </div>
                    <a href="{{ route('resources.index') }}" class="btn btn-secondary float-right">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
