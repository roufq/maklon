@extends('layouts.app')
@section('title','Batch ' . $batch->batch_number)
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Batch {{ $batch->batch_number }}</h3>
    <div class="d-flex gap-2">
      @if(auth()->user()->can('permission','production.edit'))
        <a class="btn btn-outline-primary" href="{{ route('batches.edit',$batch) }}">Edit</a>
      @endif
      @if(auth()->user()->can('permission','production.delete'))
      <form method="post" action="{{ route('batches.destroy',$batch) }}" onsubmit="return confirm('Delete?')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger">Delete</button>
      </form>
      @endif
    </div>
  </div>
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-6">
          <div><strong>Order:</strong> {{ $batch->project->name ?? 'N/A' }}</div>
          <div><strong>BPOM:</strong> {{ $batch->bpom->registration_number ?? '-' }}</div>
          <div><strong>Expiry:</strong> {{ optional($batch->expiry_date)->format('Y-m-d') ?: '-' }}</div>
        </div>
        <div class="col-md-6">
          <div><strong>Quantity:</strong> {{ $batch->quantity_produced }}</div>
          <div><strong>QC Status:</strong> {{ ucfirst($batch->qc_status) }}</div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

