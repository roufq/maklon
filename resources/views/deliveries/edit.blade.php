@extends('layouts.app')
@section('title','Edit Delivery')
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">Edit Delivery</h3>
  <form method="post" action="{{ route('deliveries.update',$delivery) }}" class="card p-3 border-0 shadow-sm">
    @csrf @method('PUT')
    <div class="row g-3">
      <div class="col-md-4"><label class="form-label">Project</label><select name="project_id" class="form-select" required>@foreach($projects as $p)<option value="{{ $p->id }}" @selected($p->id==$delivery->project_id)>{{ $p->name }}</option>@endforeach</select></div>
      <div class="col-md-4"><label class="form-label">Batch</label><select name="production_batch_id" class="form-select"><option value="">-</option>@foreach($batches as $b)<option value="{{ $b->id }}" @selected($b->id==$delivery->production_batch_id)>{{ $b->batch_number }} ({{ $b->qc_status }})</option>@endforeach</select></div>
      <div class="col-md-4"><label class="form-label">Customer</label><select name="customer_id" class="form-select"><option value="">-</option>@foreach($customers as $c)<option value="{{ $c->id }}" @selected($c->id==$delivery->customer_id)>{{ $c->name }}</option>@endforeach</select></div>
      <div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select">@foreach(['draft','ready','shipped','delivered','returned','rejected'] as $s)<option value="{{ $s }}" @selected($s==$delivery->status)>{{ ucfirst($s) }}</option>@endforeach</select></div>
      <div class="col-md-3"><label class="form-label">Shipping Provider</label><input name="shipping_provider" class="form-control" value="{{ $delivery->shipping_provider }}" /></div>
      <div class="col-md-3"><label class="form-label">Tracking Number</label><input name="tracking_number" class="form-control" value="{{ $delivery->tracking_number }}" /></div>
      <div class="col-md-6"><label class="form-label">Notes</label><input name="notes" class="form-control" value="{{ $delivery->notes }}" /></div>
      <div class="col-md-4"><label class="form-label">Ship To Name</label><input name="shipping_name" class="form-control" value="{{ $delivery->shipping_address['name'] ?? '' }}" /></div>
      <div class="col-md-4"><label class="form-label">Address</label><input name="shipping_address" class="form-control" value="{{ $delivery->shipping_address['address'] ?? '' }}" /></div>
      <div class="col-md-2"><label class="form-label">City</label><input name="shipping_city" class="form-control" value="{{ $delivery->shipping_address['city'] ?? '' }}" /></div>
      <div class="col-md-2"><label class="form-label">ZIP</label><input name="shipping_zip" class="form-control" value="{{ $delivery->shipping_address['zip'] ?? '' }}" /></div>
      <div class="col-md-3"><label class="form-label">Country</label><input name="shipping_country" class="form-control" value="{{ $delivery->shipping_address['country'] ?? '' }}" /></div>
      <div class="col-md-3"><label class="form-label">Phone</label><input name="shipping_phone" class="form-control" value="{{ $delivery->shipping_address['phone'] ?? '' }}" /></div>
    </div>
    <div class="mt-3"><button class="btn btn-primary">Save</button><a class="btn btn-outline-secondary ms-2" href="{{ route('deliveries.show',$delivery) }}">Cancel</a></div>
  </form>
</div>
@endsection

