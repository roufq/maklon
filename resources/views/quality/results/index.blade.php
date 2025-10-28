@extends('layouts.app')
@section('title','QC Results')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">QC Results</h3>
    @if(auth()->user()->can('permission','qc.create'))<a class="btn btn-primary" href="{{ route('quality.results.create') }}">New</a>@endif
  </div>
  <div class="card border-0 shadow-sm"><div class="table-responsive">
    <table class="table mb-0"><thead class="table-light"><tr><th>Batch</th><th>Checkpoint</th><th>Status</th><th>By</th></tr></thead><tbody>@foreach($items as $r)<tr><td><a href="{{ route('quality.results.show',$r) }}">{{ $r->batch->batch_number }}</a></td><td>{{ $r->checkpoint->name }}</td><td>{{ strtoupper($r->status) }}</td><td>{{ $r->user->name ?? '-' }}</td></tr>@endforeach</tbody></table>
  </div><div class="card-footer">{{ $items->links() }}</div></div>
</div>
@endsection

