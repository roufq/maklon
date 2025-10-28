@extends('layouts.app')
@section('title','BOMs')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">BOMs</h3>
    @if(auth()->user()->can('permission','inventory.create'))
      <a class="btn btn-primary" href="{{ route('boms.create') }}">New BOM</a>
    @endif
  </div>
  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table mb-0"><thead class="table-light"><tr><th>Product</th><th>Version</th><th>Total Cost</th></tr></thead><tbody>@foreach($boms as $b)<tr><td><a href="{{ route('boms.show',$b) }}">{{ $b->product_name }}</a></td><td>{{ $b->version ?? '-' }}</td><td>Rp {{ number_format($b->total_cost ?? 0,0,',','.') }}</td></tr>@endforeach</tbody></table>
    </div>
    <div class="card-footer">{{ $boms->links() }}</div>
  </div>
</div>
@endsection

