@extends('layouts.app')

@section('title', 'Create Project - Project Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Create Project</h1>
            <p class="text-muted mb-0">Add a new project to your portfolio</p>
        </div>
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Projects
        </a>
    </div>

    <!-- Create Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('projects.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label fw-bold">Project Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="planning" {{ old('status') == 'planning' ? 'selected' : '' }}>Planning</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="priority" class="form-label fw-bold">Priority <span class="text-danger">*</span></label>
                                <select class="form-select @error('priority') is-invalid @enderror"
                                        id="priority" name="priority" required>
                                    <option value="">Select Priority</option>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="start_date" class="form-label fw-bold">Start Date</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                       id="start_date" name="start_date" value="{{ old('start_date') }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label fw-bold">End Date</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                       id="end_date" name="end_date" value="{{ old('end_date') }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="budget" class="form-label fw-bold">Budget</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('budget') is-invalid @enderror"
                                           id="budget" name="budget" value="{{ old('budget') }}" step="0.01" min="0">
                                    @error('budget')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="team_id" class="form-label fw-bold">Team</label>
                                <select class="form-select @error('team_id') is-invalid @enderror"
                                        id="team_id" name="team_id">
                                    <option value="">Select Team</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>
                                            {{ $team->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('team_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="customer_id" class="form-label fw-bold">Customer</label>
                                <select class="form-select" id="customer_id" name="customer_id">
                                    <option value="">Select Customer</option>
                                    @isset($clients)
                                        @foreach($clients as $c)
                                            <option value="{{ $c->id }}" @selected(old('customer_id')==$c->id)>{{ $c->name }} ({{ $c->email }})</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="order_quantity" class="form-label fw-bold">Order Quantity</label>
                                <input type="number" class="form-control" id="order_quantity" name="order_quantity" value="{{ old('order_quantity') }}" min="0">
                            </div>
                            <div class="col-md-3">
                                <label for="due_date" class="form-label fw-bold">Due Date</label>
                                <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="production_status" class="form-label fw-bold">Production Status</label>
                                <select class="form-select" id="production_status" name="production_status">
                                    @foreach(['draft','scheduled','in_progress','blocked','completed'] as $s)
                                        <option value="{{ $s }}" @selected(old('production_status')==$s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="bpom_registration_id" class="form-label fw-bold">BPOM Registration</label>
                                <select class="form-select" id="bpom_registration_id" name="bpom_registration_id">
                                    <option value="">Select Registration</option>
                                    @isset($bpoms)
                                        @foreach($bpoms as $b)
                                            <option value="{{ $b->id }}" @selected(old('bpom_registration_id')==$b->id)>
                                                {{ $b->product_name }} — {{ $b->registration_number }} ({{ ucfirst($b->status) }})
                                            </option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Create Project
                            </button>
                            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Project Guidelines</h5>
                </div>
                <div class="card-body">
                    <h6>Project Status</h6>
                    <ul class="small mb-3">
                        <li><strong>Planning:</strong> Initial project setup</li>
                        <li><strong>Active:</strong> Currently in development</li>
                        <li><strong>On Hold:</strong> Temporarily paused</li>
                        <li><strong>Completed:</strong> Finished successfully</li>
                        <li><strong>Cancelled:</strong> Terminated early</li>
                    </ul>

                    <h6>Priority Levels</h6>
                    <ul class="small mb-0">
                        <li><strong>Low:</strong> Can be done later</li>
                        <li><strong>Medium:</strong> Standard priority</li>
                        <li><strong>High:</strong> Should be done soon</li>
                        <li><strong>Urgent:</strong> Requires immediate attention</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
