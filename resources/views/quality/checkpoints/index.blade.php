@extends('layouts.app')
@section('title','QC Checkpoints')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">QC Checkpoints</h3>
    @if(auth()->user()->can('permission','qc.create'))<a class="btn btn-primary" href="{{ route('quality.checkpoints.create') }}">New</a>@endif
  </div>
  <div class="card border-0 shadow-sm"><div class="table-responsive">
    <table class="table mb-0"><thead class="table-light"><tr><th>Name</th><th>Project</th><th>Required</th></tr></thead><tbody>@foreach($items as $cp)<tr><td><a href="{{ route('quality.checkpoints.show',$cp) }}">{{ $cp->name }}</a></td><td>{{ $cp->project->name ?? '-' }}</td><td>{{ $cp->required ? 'Yes' : 'No' }}</td></tr>@endforeach</tbody></table>
  </div><div class="card-footer">{{ $items->links() }}</div></div>
</div>
@endsection

