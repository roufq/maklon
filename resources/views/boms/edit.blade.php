@extends('layouts.app')
@section('title','Edit BOM ' . $bom->product_name)
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">Edit BOM</h3>
  <form method="post" action="{{ route('boms.update',$bom) }}" class="card p-3 border-0 shadow-sm">
    @csrf @method('PUT')
    <div class="row g-3">
      <div class="col-md-5"><label class="form-label">Product Name</label><input name="product_name" class="form-control" value="{{ $bom->product_name }}" required /></div>
      <div class="col-md-3"><label class="form-label">Version</label><input name="version" class="form-control" value="{{ $bom->version }}" /></div>
    </div>
    <div class="mt-3"><button class="btn btn-primary">Save</button><a class="btn btn-outline-secondary ms-2" href="{{ route('boms.show',$bom) }}">Cancel</a></div>
  </form>
</div>
@endsection

