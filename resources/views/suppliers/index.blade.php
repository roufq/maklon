@extends('layouts.app')
@section('title','Suppliers')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Suppliers</h3>
    @if(auth()->user()->can('permission','supplier.create'))
      <a class="btn btn-primary" href="{{ route('suppliers.create') }}">New Supplier</a>
    @endif
  </div>
  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead class="table-light">
          <tr><th>Name</th><th>Type</th><th>BPOM</th><th>Rating</th><th>Score</th><th></th></tr>
        </thead>
        <tbody>
          @foreach($suppliers as $s)
          <tr>
            <td><a href="{{ route('suppliers.show',$s) }}">{{ $s->name }}</a></td>
            <td>{{ $s->type ?: '-' }}</td>
            <td>{!! $s->bpom_certified ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
            <td>{{ $s->rating ?: '-' }}</td>
            <td>{{ $s->performance_score ?: '-' }}</td>
            <td></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $suppliers->links() }}</div>
  </div>
</div>
@endsection

