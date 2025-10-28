@extends('layouts.app')
@section('title','Deliveries')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Deliveries</h3>
    @if(auth()->user()->can('permission','delivery.create'))<a class="btn btn-primary" href="{{ route('deliveries.create') }}">New Delivery</a>@endif
  </div>
  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table mb-0"><thead class="table-light"><tr><th>Project</th><th>Batch</th><th>Customer</th><th>Status</th><th>Shipped</th><th></th></tr></thead><tbody>
        @foreach($deliveries as $d)
        <tr>
          <td>{{ $d->project->name ?? '-' }}</td>
          <td>{{ $d->batch->batch_number ?? '-' }}</td>
          <td>{{ $d->customer->name ?? '-' }}</td>
          <td><span class="badge bg-secondary">{{ ucfirst($d->status) }}</span></td>
          <td>{{ optional($d->shipped_at)->format('Y-m-d') ?: '-' }}</td>
          <td><a class="btn btn-sm btn-outline-secondary" href="{{ route('deliveries.show',$d) }}">View</a></td>
        </tr>
        @endforeach
      </tbody></table>
    </div>
    <div class="card-footer">{{ $deliveries->links() }}</div>
  </div>
</div>
@endsection

