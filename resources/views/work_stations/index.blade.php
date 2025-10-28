@extends('layouts.app')
@section('title','Work Stations')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Work Stations</h3>
    @can('production.create')
      <a href="{{ route('work-stations.create') }}" class="btn btn-primary">New Work Station</a>
    @endcan
  </div>
  <div class="card">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead class="table-light">
          <tr><th>Name</th><th>Capacity/hour</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
        @forelse($items as $it)
          <tr>
            <td><a href="{{ route('work-stations.show',$it) }}">{{ $it->name }}</a></td>
            <td>{{ $it->capacity_per_hour }}</td>
            <td><span class="badge bg-secondary">{{ ucfirst($it->status) }}</span></td>
            <td class="text-end">
              @can('production.edit')
                <a href="{{ route('work-stations.edit',$it) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
              @endcan
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="text-center text-muted">No data</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-body">{{ $items->links() }}</div>
  </div>
</div>
@endsection

