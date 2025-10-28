@extends('layouts.app')
@section('title','BOM ' . $bom->product_name)
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">BOM {{ $bom->product_name }}</h3>
    @if(auth()->user()->can('permission','inventory.edit'))<a class="btn btn-outline-primary" href="{{ route('boms.edit',$bom) }}">Edit</a>@endif
  </div>
  <div class="card border-0 shadow-sm"><div class="card-body">
    <div><strong>Version:</strong> {{ $bom->version ?? '-' }}</div>
    <div class="mt-2"><strong>Materials:</strong>
      <pre class="mb-0">{{ json_encode($bom->materials ?? [], JSON_PRETTY_PRINT) }}</pre>
    </div>
  </div></div>
</div>
@endsection

