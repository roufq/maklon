@extends('layouts.app')

@section('title', 'Edit Team - ' . $team->name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Edit Team</h1>
            <p class="text-muted mb-0">Update team information</p>
        </div>
        <a href="{{ route('teams.show', $team) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Team
        </a>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('teams.update', $team) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label fw-bold">Team Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $team->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="type" class="form-label fw-bold">Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror"
                                        id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="project_team" {{ old('type', $team->type) == 'project_team' ? 'selected' : '' }}>Project Team</option>
                                    <option value="department" {{ old('type', $team->type) == 'department' ? 'selected' : '' }}>Department</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description', $team->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="leader_id" class="form-label fw-bold">Team Leader <span class="text-danger">*</span></label>
                            <select class="form-select @error('leader_id') is-invalid @enderror"
                                    id="leader_id" name="leader_id" required>
                                <option value="">Select Team Leader</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('leader_id', $team->leader_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('leader_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Team Members</label>
                            <div class="border rounded p-3">
                                @foreach($users as $user)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           id="member_{{ $user->id }}" name="member_ids[]"
                                           value="{{ $user->id }}"
                                           {{ in_array($user->id, old('member_ids', $selectedMembers)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="member_{{ $user->id }}">
                                        {{ $user->name }}
                                        @if($user->id === $team->leader_id)
                                        <span class="badge bg-warning ms-2">Current Leader</span>
                                        @endif
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            @error('member_ids')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Team
                            </button>
                            <a href="{{ route('teams.show', $team) }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Team Guidelines</h5>
                </div>
                <div class="card-body">
                    <h6>Team Types</h6>
                    <ul class="small mb-3">
                        <li><strong>Project Team:</strong> Temporary team for specific projects</li>
                        <li><strong>Department:</strong> Permanent organizational unit</li>
                    </ul>

                    <h6>Team Leader Responsibilities</h6>
                    <ul class="small mb-0">
                        <li>Coordinate team activities</li>
                        <li>Communicate with stakeholders</li>
                        <li>Ensure project deliverables</li>
                        <li>Mentor team members</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
