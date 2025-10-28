@extends('layouts.app')
@section('title','Edit Batch ' . $batch->batch_number)
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">Edit Batch {{ $batch->batch_number }}</h3>
  <form method="post" action="{{ route('batches.update', $batch) }}" class="card p-3 border-0 shadow-sm">
    @csrf
    @method('PUT')
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Production Order</label>
        <select name="project_id" class="form-select" required>
          @foreach($projects as $p)
            <option value="{{ $p->id }}" @selected($p->id==$batch->project_id)>{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">BPOM Registration</label>
        <select name="bpom_registration_id" class="form-select">
          <option value="">-</option>
          @foreach($bpoms as $r)
            <option value="{{ $r->id }}" @selected($r->id==$batch->bpom_registration_id)>{{ $r->product_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Quantity</label>
        <input type="number" name="quantity_produced" class="form-control" min="0" value="{{ $batch->quantity_produced }}" />
      </div>
      <div class="col-md-2">
        <label class="form-label">Expiry Date</label>
        <input type="date" name="expiry_date" class="form-control" value="{{ optional($batch->expiry_date)->toDateString() }}" />
      </div>
      <div class="col-md-3">
        <label class="form-label">QC Status</label>
        <select name="qc_status" class="form-select" required>
          @foreach(['pending','passed','failed'] as $s)
            <option value="{{ $s }}" @selected($batch->qc_status===$s)>{{ ucfirst($s) }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-primary">Save</button>
      <a class="btn btn-outline-secondary" href="{{ route('batches.show', $batch) }}">Cancel</a>
    </div>
  </form>
</div>
@endsection

