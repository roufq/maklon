@extends('layouts.app')
@section('title','New Supplier')
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">New Supplier</h3>
  <form method="post" action="{{ route('suppliers.store') }}" class="card p-3 border-0 shadow-sm">
    @csrf
    <div class="row g-3">
      <div class="col-md-5">
        <label class="form-label">Name</label>
        <input name="name" class="form-control" required />
      </div>
      <div class="col-md-3">
        <label class="form-label">Type</label>
        <input name="type" class="form-control" placeholder="raw_material, packaging, ..." />
      </div>
      <div class="col-md-2 form-check mt-4">
        <input class="form-check-input" type="checkbox" name="bpom_certified" value="1" id="bpom_chk">
        <label class="form-check-label" for="bpom_chk">BPOM Certified</label>
      </div>
      <div class="col-md-2">
        <label class="form-label">Rating (1-5)</label>
        <input type="number" min="1" max="5" name="rating" class="form-control" />
      </div>
      <div class="col-md-4">
        <label class="form-label">Email</label>
        <input type="email" name="contact_email" class="form-control" />
      </div>
      <div class="col-md-4">
        <label class="form-label">Phone</label>
        <input name="contact_phone" class="form-control" />
      </div>
      <div class="col-md-4">
        <label class="form-label">Address</label>
        <input name="contact_address" class="form-control" />
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-primary">Create</button>
      <a class="btn btn-outline-secondary" href="{{ route('suppliers.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection

