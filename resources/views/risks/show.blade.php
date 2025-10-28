@extends('layouts.app')

@section('title', 'Risk Details - Modern Bootstrap Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 fw-bold">{{ $risk->title }}</h1>
                    <p class="text-muted mb-0">Risk details and management</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('risks.edit', $risk) }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-2"></i>Edit Risk
                    </a>
                    <a href="{{ route('risks.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Risks
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Risk Overview -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Risk Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Project</label>
                                <p class="mb-0">{{ $risk->project->name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Risk Owner</label>
                                <p class="mb-0">{{ $risk->owner->name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Identified By</label>
                                <p class="mb-0">{{ $risk->identifiedBy->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <p class="mb-0">
                                    @switch($risk->status)
                                        @case('identified')
                                            <span class="badge bg-info">Identified</span>
                                            @break
                                        @case('assessed')
                                            <span class="badge bg-primary">Assessed</span>
                                            @break
                                        @case('mitigated')
                                            <span class="badge bg-success">Mitigated</span>
                                            @break
                                        @case('closed')
                                            <span class="badge bg-secondary">Closed</span>
                                            @break
                                        @case('occurred')
                                            <span class="badge bg-danger">Occurred</span>
                                            @break
                                    @endswitch
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Due Date</label>
                                <p class="mb-0">
                                    @if($risk->due_date)
                                        {{ $risk->due_date->format('M d, Y') }}
                                        @if($risk->due_date->isPast() && $risk->status !== 'closed')
                                            <span class="badge bg-danger ms-2">Overdue</span>
                                        @endif
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Created</label>
                                <p class="mb-0">{{ $risk->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk Assessment -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Risk Assessment</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Probability</label>
                                <div class="h4 mb-0">{{ ucfirst(str_replace('_', ' ', $risk->probability)) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Impact</label>
                                <div class="h4 mb-0">{{ ucfirst(str_replace('_', ' ', $risk->impact)) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Risk Score</label>
                                <div class="h4 mb-0 fw-bold">{{ $risk->risk_score }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Risk Level</label>
                                @if($risk->risk_level == 'Low')
                                    <span class="badge bg-success fs-6 px-3 py-2">{{ $risk->risk_level }}</span>
                                @elseif($risk->risk_level == 'Medium')
                                    <span class="badge bg-warning text-dark fs-6 px-3 py-2">{{ $risk->risk_level }}</span>
                                @elseif($risk->risk_level == 'High')
                                    <span class="badge bg-danger fs-6 px-3 py-2">{{ $risk->risk_level }}</span>
                                @else
                                    <span class="badge bg-danger fs-6 px-3 py-2">{{ $risk->risk_level }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Risk Details -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Risk Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Description -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label fw-bold">Description</label>
                            <div class="border rounded p-3 bg-light">
                                @if($risk->description)
                                    {{ $risk->description }}
                                @else
                                    <span class="text-muted">No description provided</span>
                                @endif
                            </div>
                        </div>

                        <!-- Mitigation Plan -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Mitigation Plan</label>
                            <div class="border rounded p-3 bg-light" style="min-height: 100px;">
                                @if($risk->mitigation_plan)
                                    {{ $risk->mitigation_plan }}
                                @else
                                    <span class="text-muted">No mitigation plan provided</span>
                                @endif
                            </div>
                        </div>

                        <!-- Contingency Plan -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Contingency Plan</label>
                            <div class="border rounded p-3 bg-light" style="min-height: 100px;">
                                @if($risk->contingency_plan)
                                    {{ $risk->contingency_plan }}
                                @else
                                    <span class="text-muted">No contingency plan provided</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Actions</h6>
                            <small class="text-muted">Manage this risk</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('risks.edit', $risk) }}" class="btn btn-primary">
                                <i class="bi bi-pencil me-2"></i>Edit Risk
                            </a>
                            <form action="{{ route('risks.destroy', $risk) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this risk? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash me-2"></i>Delete Risk
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
