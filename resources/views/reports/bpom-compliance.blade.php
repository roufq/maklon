@extends('layouts.app')
@section('title','BPOM Compliance Report')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">BPOM Compliance</h3>
  </div>
  <form class="card card-body mb-3" method="get" action="{{ route('reports.bpom') }}">
    <div class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Status</label>
        <select class="form-select" name="status">
          <option value="">All</option>
          @foreach(['draft','active','expired','revoked','pending'] as $s)
            <option value="{{ $s }}" @selected($status===$s)>{{ ucfirst($s) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Expiring Within (days)</label>
        <input type="number" class="form-control" name="within_days" value="{{ $withinDays ?? '' }}" min="0">
      </div>
      <div class="col-md-4">
        <button class="btn btn-primary">Filter</button>
      </div>
    </div>
  </form>

  <div class="card">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead class="table-light">
          <tr>
            <th>Product</th>
            <th>Registration #</th>
            <th>Status</th>
            <th>Approval Date</th>
            <th>Expiry Date</th>
            <th>Days Left</th>
          </tr>
        </thead>
        <tbody>
          @forelse($items as $it)
            @php
              $daysLeft = $it->expiry_date ? now()->diffInDays($it->expiry_date, false) : null;
            @endphp
            <tr>
              <td>{{ $it->product_name }}</td>
              <td>{{ $it->registration_number }}</td>
              <td><span class="badge bg-secondary">{{ ucfirst($it->status) }}</span></td>
              <td>{{ optional($it->approval_date)->format('Y-m-d') ?: '-' }}</td>
              <td>{{ optional($it->expiry_date)->format('Y-m-d') ?: '-' }}</td>
              <td>{{ $daysLeft !== null ? $daysLeft : '-' }}</td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted">No data</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

