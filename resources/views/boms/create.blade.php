@extends('layouts.app')
@section('title','New BOM')
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">New BOM</h3>
  <form method="post" action="{{ route('boms.store') }}" class="card p-3 border-0 shadow-sm">
    @csrf
    <div class="row g-3">
      <div class="col-md-5"><label class="form-label">Product Name</label><input name="product_name" class="form-control" required /></div>
      <div class="col-md-3"><label class="form-label">Version</label><input name="version" class="form-control" /></div>
    </div>
    <div class="mt-3"><button class="btn btn-primary">Create</button><a class="btn btn-outline-secondary ms-2" href="{{ route('boms.index') }}">Cancel</a></div>
  </form>
</div>
@endsection

