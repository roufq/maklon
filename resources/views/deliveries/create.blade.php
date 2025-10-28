@extends('layouts.app')
@section('title','New Delivery')
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">New Delivery</h3>
  <form method="post" action="{{ route('deliveries.store') }}" class="card p-3 border-0 shadow-sm">
    @csrf
    <div class="row g-3">
      <div class="col-md-4"><label class="form-label">Project</label><select name="project_id" class="form-select" required>@foreach($projects as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
      <div class="col-md-4"><label class="form-label">Batch (QC Passed)</label><select name="production_batch_id" class="form-select"><option value="">-</option>@foreach($batches as $b)<option value="{{ $b->id }}">{{ $b->batch_number }}</option>@endforeach</select></div>
      <div class="col-md-4"><label class="form-label">Customer</label><select name="customer_id" class="form-select"><option value="">-</option>@foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
      <div class="col-md-3"><label class="form-label">Shipping Provider</label><input name="shipping_provider" class="form-control" /></div>
      <div class="col-md-3"><label class="form-label">Tracking Number</label><input name="tracking_number" class="form-control" /></div>
      <div class="col-md-6"><label class="form-label">Notes</label><input name="notes" class="form-control" /></div>
      <div class="col-md-4"><label class="form-label">Ship To Name</label><input name="shipping_name" class="form-control" /></div>
      <div class="col-md-4"><label class="form-label">Address</label><input name="shipping_address" class="form-control" /></div>
      <div class="col-md-2"><label class="form-label">City</label><input name="shipping_city" class="form-control" /></div>
      <div class="col-md-2"><label class="form-label">ZIP</label><input name="shipping_zip" class="form-control" /></div>
      <div class="col-md-3"><label class="form-label">Country</label><input name="shipping_country" class="form-control" /></div>
      <div class="col-md-3"><label class="form-label">Phone</label><input name="shipping_phone" class="form-control" /></div>
    </div>
    <div class="mt-3"><button class="btn btn-primary">Create</button><a class="btn btn-outline-secondary ms-2" href="{{ route('deliveries.index') }}">Cancel</a></div>
  </form>
</div>
@endsection

