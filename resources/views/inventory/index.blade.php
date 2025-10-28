@extends('layouts.app')
@section('title','Inventory')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Inventory</h3>
    @if(auth()->user()->can('permission','inventory.create'))
      <a class="btn btn-primary" href="{{ route('inventory.create') }}">New Item</a>
    @endif
  </div>
  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead class="table-light"><tr><th>Name</th><th>Supplier</th><th>Stock</th><th>Min</th><th>Unit</th><th>Cost</th></tr></thead>
        <tbody>
          @foreach($items as $i)
            <tr>
              <td><a href="{{ route('inventory.show',$i) }}">{{ $i->name }}</a></td>
              <td>{{ $i->supplier->name ?? '-' }}</td>
              <td>{{ $i->current_stock }}</td>
              <td>{{ $i->min_stock }}</td>
              <td>{{ $i->unit ?? '-' }}</td>
              <td>Rp {{ number_format($i->unit_cost ?? 0,0,',','.') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $items->links() }}</div>
  </div>
</div>
@endsection

