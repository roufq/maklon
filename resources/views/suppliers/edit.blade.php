@extends('layouts.app')
@section('title','Edit Supplier ' . $supplier->name)
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">Edit Supplier</h3>
  <form method="post" action="{{ route('suppliers.update',$supplier) }}" class="card p-3 border-0 shadow-sm">
    @csrf
    @method('PUT')
    <div class="row g-3">
      <div class="col-md-5">
        <label class="form-label">Name</label>
        <input name="name" class="form-control" value="{{ $supplier->name }}" required />
      </div>
      <div class="col-md-3">
        <label class="form-label">Type</label>
        <input name="type" class="form-control" value="{{ $supplier->type }}" />
      </div>
      <div class="col-md-2 form-check mt-4">
        <input class="form-check-input" type="checkbox" name="bpom_certified" value="1" id="bpom_chk" @checked($supplier->bpom_certified)>
        <label class="form-check-label" for="bpom_chk">BPOM Certified</label>
      </div>
      <div class="col-md-2">
        <label class="form-label">Rating (1-5)</label>
        <input type="number" min="1" max="5" name="rating" class="form-control" value="{{ $supplier->rating }}" />
      </div>
      <div class="col-md-4">
        <label class="form-label">Email</label>
        <input type="email" name="contact_email" class="form-control" value="{{ $supplier->contact_info['email'] ?? '' }}" />
      </div>
      <div class="col-md-4">
        <label class="form-label">Phone</label>
        <input name="contact_phone" class="form-control" value="{{ $supplier->contact_info['phone'] ?? '' }}" />
      </div>
      <div class="col-md-4">
        <label class="form-label">Address</label>
        <input name="contact_address" class="form-control" value="{{ $supplier->contact_info['address'] ?? '' }}" />
      </div>
      <div class="col-md-3">
        <label class="form-label">Performance Score</label>
        <input type="number" step="0.01" name="performance_score" class="form-control" value="{{ $supplier->performance_score }}" />
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-primary">Save</button>
      <a class="btn btn-outline-secondary" href="{{ route('suppliers.show', $supplier) }}">Cancel</a>
    </div>
  </form>
</div>
@endsection

