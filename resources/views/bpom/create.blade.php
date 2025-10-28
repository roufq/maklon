@extends('layouts.app')
@section('title','New BPOM Registration')
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">New BPOM Registration</h3>
  <form method="post" action="{{ route('bpom.store') }}" enctype="multipart/form-data" class="card p-3 border-0 shadow-sm">
    @csrf
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Product Name</label>
        <input name="product_name" class="form-control" required />
      </div>
      <div class="col-md-6">
        <label class="form-label">Registration Number</label>
        <input name="registration_number" class="form-control" required />
      </div>
      <div class="col-md-3">
        <label class="form-label">Approval Date</label>
        <input type="date" name="approval_date" class="form-control" />
      </div>
      <div class="col-md-3">
        <label class="form-label">Expiry Date</label>
        <input type="date" name="expiry_date" class="form-control" />
      </div>
      <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
          @foreach(['pending','draft','active','expired','revoked'] as $s)
            <option value="{{ $s }}">{{ ucfirst($s) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Document (PDF/JPG/PNG)</label>
        <input type="file" name="document" class="form-control" />
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-primary">Create</button>
      <a class="btn btn-outline-secondary" href="{{ route('bpom.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection

