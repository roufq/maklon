@extends('layouts.app')

@section('title', 'Edit Risk - Modern Bootstrap Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 fw-bold">Edit Risk</h1>
                    <p class="text-muted mb-0">Update risk information</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('risks.show', $risk) }}" class="btn btn-outline-info">
                        <i class="bi bi-eye me-2"></i>View Risk
                    </a>
                    <a href="{{ route('risks.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Risks
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Risk Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('risks.update', $risk) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Title -->
                            <div class="col-md-12 mb-3">
                                <label for="title" class="form-label fw-bold">Risk Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                       id="title" name="title" value="{{ old('title', $risk->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Project -->
                            <div class="col-md-6 mb-3">
                                <label for="project_id" class="form-label fw-bold">Project <span class="text-danger">*</span></label>
                                <select class="form-select @error('project_id') is-invalid @enderror"
                                        id="project_id" name="project_id" required>
                                    <option value="">Select Project</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}"
                                                {{ old('project_id', $risk->project_id) == $project->id ? 'selected' : '' }}>
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Owner -->
                            <div class="col-md-6 mb-3">
                                <label for="owner_id" class="form-label fw-bold">Risk Owner <span class="text-danger">*</span></label>
                                <select class="form-select @error('owner_id') is-invalid @enderror"
                                        id="owner_id" name="owner_id" required>
                                    <option value="">Select Owner</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}"
                                                {{ old('owner_id', $risk->owner_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('owner_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Probability -->
                            <div class="col-md-6 mb-3">
                                <label for="probability" class="form-label fw-bold">Probability <span class="text-danger">*</span></label>
                                <select class="form-select @error('probability') is-invalid @enderror"
                                        id="probability" name="probability" required>
                                    <option value="">Select Probability</option>
                                    <option value="very_low" {{ old('probability', $risk->probability) == 'very_low' ? 'selected' : '' }}>Very Low</option>
                                    <option value="low" {{ old('probability', $risk->probability) == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('probability', $risk->probability) == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('probability', $risk->probability) == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="very_high" {{ old('probability', $risk->probability) == 'very_high' ? 'selected' : '' }}>Very High</option>
                                </select>
                                @error('probability')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Impact -->
                            <div class="col-md-6 mb-3">
                                <label for="impact" class="form-label fw-bold">Impact <span class="text-danger">*</span></label>
                                <select class="form-select @error('impact') is-invalid @enderror"
                                        id="impact" name="impact" required>
                                    <option value="">Select Impact</option>
                                    <option value="very_low" {{ old('impact', $risk->impact) == 'very_low' ? 'selected' : '' }}>Very Low</option>
                                    <option value="low" {{ old('impact', $risk->impact) == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('impact', $risk->impact) == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('impact', $risk->impact) == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="very_high" {{ old('impact', $risk->impact) == 'very_high' ? 'selected' : '' }}>Very High</option>
                                </select>
                                @error('impact')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="identified" {{ old('status', $risk->status) == 'identified' ? 'selected' : '' }}>Identified</option>
                                    <option value="assessed" {{ old('status', $risk->status) == 'assessed' ? 'selected' : '' }}>Assessed</option>
                                    <option value="mitigated" {{ old('status', $risk->status) == 'mitigated' ? 'selected' : '' }}>Mitigated</option>
                                    <option value="closed" {{ old('status', $risk->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                                    <option value="occurred" {{ old('status', $risk->status) == 'occurred' ? 'selected' : '' }}>Occurred</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Due Date -->
                            <div class="col-md-6 mb-3">
                                <label for="due_date" class="form-label fw-bold">Due Date</label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                       id="due_date" name="due_date" value="{{ old('due_date', $risk->due_date ? $risk->due_date->format('Y-m-d') : '') }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label fw-bold">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="3">{{ old('description', $risk->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Mitigation Plan -->
                            <div class="col-md-12 mb-3">
                                <label for="mitigation_plan" class="form-label fw-bold">Mitigation Plan</label>
                                <textarea class="form-control @error('mitigation_plan') is-invalid @enderror"
                                          id="mitigation_plan" name="mitigation_plan" rows="3">{{ old('mitigation_plan', $risk->mitigation_plan) }}</textarea>
                                @error('mitigation_plan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Contingency Plan -->
                            <div class="col-md-12 mb-3">
                                <label for="contingency_plan" class="form-label fw-bold">Contingency Plan</label>
                                <textarea class="form-control @error('contingency_plan') is-invalid @enderror"
                                          id="contingency_plan" name="contingency_plan" rows="3">{{ old('contingency_plan', $risk->contingency_plan) }}</textarea>
                                @error('contingency_plan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('risks.show', $risk) }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Risk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Risk Assessment Guide & Current Assessment -->
        <div class="col-lg-4">
            <!-- Current Assessment -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Current Assessment</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Probability</label>
                                <div class="h5 mb-0">{{ ucfirst(str_replace('_', ' ', $risk->probability)) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Impact</label>
                                <div class="h5 mb-0">{{ ucfirst(str_replace('_', ' ', $risk->impact)) }}</div>
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

            <!-- Risk Assessment Guide -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Risk Assessment Guide</h5>
                </div>
                <div class="card-body">
                    <h6>Probability Levels:</h6>
                    <ul class="list-unstyled mb-3">
                        <li><strong>Very Low:</strong> < 10% chance</li>
                        <li><strong>Low:</strong> 10-25% chance</li>
                        <li><strong>Medium:</strong> 25-50% chance</li>
                        <li><strong>High:</strong> 50-75% chance</li>
                        <li><strong>Very High:</strong> > 75% chance</li>
                    </ul>

                    <h6>Impact Levels:</h6>
                    <ul class="list-unstyled mb-3">
                        <li><strong>Very Low:</strong> Minimal impact</li>
                        <li><strong>Low:</strong> Minor impact</li>
                        <li><strong>Medium:</strong> Moderate impact</li>
                        <li><strong>High:</strong> Major impact</li>
                        <li><strong>Very High:</strong> Critical impact</li>
                    </ul>

                    <div class="alert alert-info">
                        <small><strong>Risk Score = Probability × Impact</strong></small>
                        <br>
                        <small>1-4: Low, 5-9: Medium, 10-16: High, 17-25: Very High</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
