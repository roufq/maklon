@extends('layouts.app')
@section('title','EVM Dashboard')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">EVM Dashboard</h3>
    <form method="get" class="d-flex gap-2">
      <select class="form-select" name="project_id" onchange="this.form.submit()">
        @foreach($projects as $p)
          <option value="{{ $p->id }}" {{ $project && $project->id==$p->id ? 'selected' : '' }}>{{ $p->name }}</option>
        @endforeach
      </select>
    </form>
  </div>

  @if($project)
  <div class="row g-3 mb-3">
    <div class="col-md-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-0">Baselines</div>
        <div class="card-body">
          <form class="row g-2" method="post" action="{{ route('evm.baselines.store') }}">
            @csrf
            <input type="hidden" name="project_id" value="{{ $project->id }}" />
            <div class="col-md-4"><input class="form-control" name="name" placeholder="Name" required></div>
            <div class="col-md-4"><input class="form-control" type="date" name="baseline_date" required></div>
            <div class="col-md-4"><button class="btn btn-primary w-100">Create Baseline</button></div>
            <div class="col-12"><textarea class="form-control" name="description" rows="2" placeholder="Description (optional)"></textarea></div>
          </form>
          <ul class="list-group list-group-flush mt-3">
            @forelse($baselines as $b)
              <li class="list-group-item d-flex justify-content-between"><span>{{ $b->name }} ({{ $b->baseline_date->format('Y-m-d') }})</span></li>
            @empty
              <li class="list-group-item text-muted">No baselines</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-0">Capture EVM Point</div>
        <div class="card-body">
          <form class="row g-2" method="post" action="{{ route('evm.capture') }}">
            @csrf
            <input type="hidden" name="project_id" value="{{ $project->id }}" />
            <div class="col-md-4">
              <select class="form-select" name="baseline_id">
                <option value="">No baseline</option>
                @foreach($baselines as $b)
                  <option value="{{ $b->id }}">{{ $b->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4"><input type="date" class="form-control" name="as_of_date" value="{{ now()->toDateString() }}" required></div>
            <div class="col-md-4"><button class="btn btn-primary w-100">Capture</button></div>
          </form>
          <div class="small text-muted mt-2">PV = BAC * planned %; EV = BAC * completed %; AC = expenses to date.</div>
        </div>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0">S-Curve & Metrics</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table mb-0">
          <thead class="table-light">
            <tr><th>Date</th><th>PV</th><th>EV</th><th>AC</th><th>SPI</th><th>CPI</th></tr>
          </thead>
          <tbody>
            @forelse($points as $pt)
              <tr>
                <td>{{ $pt->as_of_date->format('Y-m-d') }}</td>
                <td>{{ number_format($pt->pv,2) }}</td>
                <td>{{ number_format($pt->ev,2) }}</td>
                <td>{{ number_format($pt->ac,2) }}</td>
                <td>{{ $pt->spi !== null ? number_format($pt->spi,2) : '-' }}</td>
                <td>{{ $pt->cpi !== null ? number_format($pt->cpi,2) : '-' }}</td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center py-4">No EVM points captured</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @else
    <div class="alert alert-warning">No projects found.</div>
  @endif
</div>
@endsection

