@extends('layouts.app')
@section('title','New Batch')
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">New Production Batch</h3>
  <form method="post" action="{{ route('batches.store') }}" class="card p-3 border-0 shadow-sm">
    @csrf
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Production Order</label>
        <select name="project_id" class="form-select" required>
          @foreach($projects as $p)
            <option value="{{ $p->id }}">{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">BPOM Registration</label>
        <select name="bpom_registration_id" class="form-select">
          <option value="">-</option>
          @foreach($bpoms as $r)
            <option value="{{ $r->id }}">{{ $r->product_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Quantity</label>
        <input type="number" name="quantity_produced" class="form-control" min="0" />
      </div>
      <div class="col-md-2">
        <label class="form-label">Expiry Date</label>
        <input type="date" name="expiry_date" class="form-control" />
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-primary">Create</button>
      <a class="btn btn-outline-secondary" href="{{ route('batches.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection

