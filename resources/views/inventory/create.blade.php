@extends('layouts.app')
@section('title','New Inventory Item')
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">New Inventory Item</h3>
  <form method="post" action="{{ route('inventory.store') }}" class="card p-3 border-0 shadow-sm">
    @csrf
    <div class="row g-3">
      <div class="col-md-5"><label class="form-label">Name</label><input name="name" class="form-control" required /></div>
      <div class="col-md-3"><label class="form-label">Supplier</label><select name="supplier_id" class="form-select"><option value="">-</option>@foreach($suppliers as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
      <div class="col-md-2"><label class="form-label">Stock</label><input type="number" name="current_stock" class="form-control" value="0" /></div>
      <div class="col-md-2"><label class="form-label">Min</label><input type="number" name="min_stock" class="form-control" value="0" /></div>
      <div class="col-md-2"><label class="form-label">Unit</label><input name="unit" class="form-control" /></div>
      <div class="col-md-3"><label class="form-label">Unit Cost</label><input type="number" step="0.01" name="unit_cost" class="form-control" /></div>
    </div>
    <div class="mt-3"><button class="btn btn-primary">Create</button><a class="btn btn-outline-secondary ms-2" href="{{ route('inventory.index') }}">Cancel</a></div>
  </form>
</div>
@endsection

