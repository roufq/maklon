@extends('layouts.app')

@section('title', 'Risk Management - Modern Bootstrap Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 fw-bold">Risk Management</h1>
                    <p class="text-muted mb-0">Monitor and manage project risks</p>
                </div>
                <div class="d-flex gap-2">
                    @can('risks.create')
                    <a href="{{ route('risks.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Add Risk
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('risks.index') }}" class="row g-3">
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
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="identified" {{ request('status') == 'identified' ? 'selected' : '' }}>Identified</option>
                                <option value="assessed" {{ request('status') == 'assessed' ? 'selected' : '' }}>Assessed</option>
                                <option value="mitigated" {{ request('status') == 'mitigated' ? 'selected' : '' }}>Mitigated</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                <option value="occurred" {{ request('status') == 'occurred' ? 'selected' : '' }}>Occurred</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="risk_level" class="form-label">Risk Level</label>
                            <select name="risk_level" id="risk_level" class="form-select">
                                <option value="">All Levels</option>
                                <option value="Low" {{ request('risk_level') == 'Low' ? 'selected' : '' }}>Low</option>
                                <option value="Medium" {{ request('risk_level') == 'Medium' ? 'selected' : '' }}>Medium</option>
                                <option value="High" {{ request('risk_level') == 'High' ? 'selected' : '' }}>High</option>
                                <option value="Very High" {{ request('risk_level') == 'Very High' ? 'selected' : '' }}>Very High</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="bi bi-search me-1"></i>Filter
                            </button>
                            <a href="{{ route('risks.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Risk Matrix -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Risk Assessment Matrix</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>Probability / Impact</th>
                                    <th>Very Low (1)</th>
                                    <th>Low (2)</th>
                                    <th>Medium (3)</th>
                                    <th>High (4)</th>
                                    <th>Very High (5)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th class="table-light">Very Low (1)</th>
                                    <td class="bg-success text-white">1</td>
                                    <td class="bg-success text-white">2</td>
                                    <td class="bg-warning text-dark">3</td>
                                    <td class="bg-warning text-dark">4</td>
                                    <td class="bg-danger text-white">5</td>
                                </tr>
                                <tr>
                                    <th class="table-light">Low (2)</th>
                                    <td class="bg-success text-white">2</td>
                                    <td class="bg-success text-white">4</td>
                                    <td class="bg-warning text-dark">6</td>
                                    <td class="bg-warning text-dark">8</td>
                                    <td class="bg-danger text-white">10</td>
                                </tr>
                                <tr>
                                    <th class="table-light">Medium (3)</th>
                                    <td class="bg-warning text-dark">3</td>
                                    <td class="bg-warning text-dark">6</td>
                                    <td class="bg-warning text-dark">9</td>
                                    <td class="bg-danger text-white">12</td>
                                    <td class="bg-danger text-white">15</td>
                                </tr>
                                <tr>
                                    <th class="table-light">High (4)</th>
                                    <td class="bg-warning text-dark">4</td>
                                    <td class="bg-warning text-dark">8</td>
                                    <td class="bg-danger text-white">12</td>
                                    <td class="bg-danger text-white">16</td>
                                    <td class="bg-danger text-white">20</td>
                                </tr>
                                <tr>
                                    <th class="table-light">Very High (5)</th>
                                    <td class="bg-danger text-white">5</td>
                                    <td class="bg-danger text-white">10</td>
                                    <td class="bg-danger text-white">15</td>
                                    <td class="bg-danger text-white">20</td>
                                    <td class="bg-danger text-white">25</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">
                            <strong>Legend:</strong>
                            <span class="badge bg-success me-2">Low (1-4)</span>
                            <span class="badge bg-warning text-dark me-2">Medium (5-9)</span>
                            <span class="badge bg-danger me-2">High (10-16)</span>
                            <span class="badge bg-danger me-2">Very High (17-25)</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Risks List -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Risk Register ({{ $risks->total() }} risks)</h5>
                    <div class="d-flex gap-2">
                        <span class="badge bg-success">{{ $risks->where('risk_level', 'Low')->count() }} Low</span>
                        <span class="badge bg-warning text-dark">{{ $risks->where('risk_level', 'Medium')->count() }} Medium</span>
                        <span class="badge bg-danger">{{ $risks->where('risk_level', 'High')->count() }} High</span>
                        <span class="badge bg-danger">{{ $risks->where('risk_level', 'Very High')->count() }} Very High</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($risks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Project</th>
                                        <th>Probability</th>
                                        <th>Impact</th>
                                        <th>Risk Score</th>
                                        <th>Level</th>
                                        <th>Status</th>
                                        <th>Owner</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($risks as $risk)
                                        <tr>
                                            <td>
                                                <a href="{{ route('risks.show', $risk) }}" class="text-decoration-none fw-bold">
                                                    {{ $risk->title }}
                                                </a>
                                            </td>
                                            <td>{{ $risk->project->name }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $risk->probability)) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $risk->impact)) }}</span>
                                            </td>
                                            <td>{{ $risk->risk_score }}</td>
                                            <td>
                                                @if($risk->risk_level == 'Low')
                                                    <span class="badge bg-success">{{ $risk->risk_level }}</span>
                                                @elseif($risk->risk_level == 'Medium')
                                                    <span class="badge bg-warning text-dark">{{ $risk->risk_level }}</span>
                                                @elseif($risk->risk_level == 'High')
                                                    <span class="badge bg-danger">{{ $risk->risk_level }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ $risk->risk_level }}</span>
                                                @endif
                                            </td>
                                            <td>
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
                                            </td>
                                            <td>{{ $risk->owner->name }}</td>
                                            <td>
                                                @if($risk->due_date)
                                                    {{ $risk->due_date->format('M d, Y') }}
                                                    @if($risk->due_date->isPast() && $risk->status !== 'closed')
                                                        <br><small class="text-danger">Overdue</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('risks.show', $risk) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('risks.edit', $risk) }}" class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('risks.destroy', $risk) }}" method="POST" class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this risk?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $risks->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-shield-check display-1 text-muted mb-3"></i>
                            <h5 class="text-muted">No risks found</h5>
                            <p class="text-muted">Start by adding your first risk to the register.</p>
                            <a href="{{ route('risks.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Add First Risk
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
