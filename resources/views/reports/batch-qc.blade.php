@extends('layouts.app')
@section('title','Batch & QC Status')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Batch & QC Status</h3>
  </div>
  <form class="card card-body mb-3" method="get" action="{{ route('reports.batchQc') }}">
    <div class="row g-2 align-items-end">
      <div class="col-md-6">
        <label class="form-label">Project</label>
        <select name="project_id" class="form-select">
          <option value="">All</option>
          @foreach($projects as $p)
            <option value="{{ $p->id }}" @selected((int)($projectId ?? 0) === $p->id)>{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">QC Status</label>
        <select name="qc_status" class="form-select">
          <option value="">All</option>
          @foreach(['pending','passed','failed'] as $s)
            <option value="{{ $s }}" @selected(($status ?? '')===$s)>{{ ucfirst($s) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <button class="btn btn-primary">Filter</button>
      </div>
    </div>
  </form>

  <div class="card">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead class="table-light">
          <tr>
            <th>Project</th>
            <th>Batch #</th>
            <th>BPOM</th>
            <th>Qty</th>
            <th>Expiry</th>
            <th>QC Status</th>
            <th>Checks</th>
            <th>Eligible for Delivery</th>
          </tr>
        </thead>
        <tbody>
          @forelse($batches as $b)
            <tr>
              <td>{{ $b->project->name ?? '-' }}</td>
              <td>{{ $b->batch_number }}</td>
              <td>{{ optional($b->bpom)->registration_number ?: '-' }}</td>
              <td>{{ $b->quantity_produced }}</td>
              <td>{{ optional($b->expiry_date)->format('Y-m-d') ?: '-' }}</td>
              <td><span class="badge bg-secondary">{{ ucfirst($b->qc_status) }}</span></td>
              <td>{{ $b->pass_count }}/{{ $b->total_checks }} pass ({{ $b->fail_count }} fail)</td>
              <td>
                @if($b->qc_status === 'passed')
                  <span class="badge bg-success">Yes</span>
                @elseif($b->qc_status === 'failed')
                  <span class="badge bg-danger">No</span>
                @else
                  <span class="badge bg-warning text-dark">Pending</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center text-muted">No data</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-body">{{ $batches->links() }}</div>
  </div>
</div>
@endsection

