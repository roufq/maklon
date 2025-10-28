@extends('layouts.app')
@section('title','Production Batches')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Production Batches</h3>
    @if(auth()->user()->can('permission','production.create'))
      <a class="btn btn-primary" href="{{ route('batches.create') }}">New Batch</a>
    @endif
  </div>
  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead class="table-light">
          <tr><th>Batch No</th><th>Order</th><th>BPOM</th><th>Qty</th><th>Expiry</th><th>QC</th><th></th></tr>
        </thead>
        <tbody>
          @foreach($batches as $b)
          <tr>
            <td><a href="{{ route('batches.show',$b) }}">{{ $b->batch_number }}</a></td>
            <td>{{ $b->project->name ?? 'N/A' }}</td>
            <td>{{ $b->bpom->registration_number ?? '-' }}</td>
            <td>{{ $b->quantity_produced }}</td>
            <td>{{ optional($b->expiry_date)->format('Y-m-d') ?: '-' }}</td>
            <td><span class="badge bg-secondary">{{ ucfirst($b->qc_status) }}</span></td>
            <td></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $batches->links() }}</div>
  </div>
</div>
@endsection

