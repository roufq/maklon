@extends('layouts.app')
@section('title','Supplier ' . $supplier->name)
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Supplier {{ $supplier->name }}</h3>
    <div class="d-flex gap-2">
      @if(auth()->user()->can('permission','supplier.edit'))
        <a class="btn btn-outline-primary" href="{{ route('suppliers.edit',$supplier) }}">Edit</a>
      @endif
      @if(auth()->user()->can('permission','supplier.delete'))
      <form method="post" action="{{ route('suppliers.destroy',$supplier) }}" onsubmit="return confirm('Delete?')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger">Delete</button>
      </form>
      @endif
    </div>
  </div>
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-6">
          <div><strong>Type:</strong> {{ $supplier->type ?: '-' }}</div>
          <div><strong>BPOM Certified:</strong> {{ $supplier->bpom_certified ? 'Yes' : 'No' }}</div>
          <div><strong>Rating:</strong> {{ $supplier->rating ?: '-' }}</div>
        </div>
        <div class="col-md-6">
          <div><strong>Email:</strong> {{ $supplier->contact_info['email'] ?? '-' }}</div>
          <div><strong>Phone:</strong> {{ $supplier->contact_info['phone'] ?? '-' }}</div>
          <div><strong>Address:</strong> {{ $supplier->contact_info['address'] ?? '-' }}</div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

