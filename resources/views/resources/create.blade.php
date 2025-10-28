@extends('layouts.app')

@section('title', 'Create Resource Allocation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create Resource Allocation</h3>
                    <div class="card-tools">
                        <a href="{{ route('resources.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <form action="{{ route('resources.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="project_id">Project <span class="text-danger">*</span></label>
                                    <select name="project_id" id="project_id" class="form-control @error('project_id') is-invalid @enderror" required>
                                        <option value="">Select Project</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" {{ ($selectedProject && $selectedProject->id == $project->id) ? 'selected' : '' }}>
                                                {{ $project->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id">User <span class="text-danger">*</span></label>
                                    <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                                        <option value="">Select User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">
                                                {{ $user->name }} ({{ $user->position ?? 'No Position' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="task_id">Task (Optional)</label>
                                    <select name="task_id" id="task_id" class="form-control @error('task_id') is-invalid @enderror">
                                        <option value="">General Allocation (No Specific Task)</option>
                                        @foreach($tasks as $task)
                                            <option value="{{ $task->id }}">
                                                {{ $task->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('task_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="allocation_type">Allocation Type <span class="text-danger">*</span></label>
                                    <select name="allocation_type" id="allocation_type" class="form-control @error('allocation_type') is-invalid @enderror" required>
                                        <option value="">Select Type</option>
                                        <option value="planned">Planned</option>
                                        <option value="actual">Actual</option>
                                        <option value="forecast">Forecast</option>
                                    </select>
                                    @error('allocation_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="allocated_hours">Allocated Hours <span class="text-danger">*</span></label>
                                    <input type="number" name="allocated_hours" id="allocated_hours" class="form-control @error('allocated_hours') is-invalid @enderror"
                                           value="{{ old('allocated_hours') }}" step="0.5" min="0.5" max="24" required>
                                    <small class="form-text text-muted">Hours per day (0.5 - 24)</small>
                                    @error('allocated_hours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror"
                                           value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_date">End Date <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror"
                                           value="{{ old('end_date') }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                                      placeholder="Additional notes about this allocation...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Allocation
                        </button>
                        <a href="{{ route('resources.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Load tasks when project changes
    $('#project_id').change(function() {
        var projectId = $(this).val();
        if (projectId) {
            $.get('{{ route("resources.getTasks") }}', { project_id: projectId })
                .done(function(data) {
                    $('#task_id').empty();
                    $('#task_id').append('<option value="">General Allocation (No Specific Task)</option>');
                    $.each(data, function(key, task) {
                        $('#task_id').append('<option value="' + task.id + '">' + task.title + '</option>');
                    });
                });
        } else {
            $('#task_id').empty();
            $('#task_id').append('<option value="">General Allocation (No Specific Task)</option>');
        }
    });

    // Set minimum end date to start date
    $('#start_date').change(function() {
        $('#end_date').attr('min', $(this).val());
        if ($('#end_date').val() && $('#end_date').val() < $(this).val()) {
            $('#end_date').val($(this).val());
        }
    });
});
</script>
@endsection
