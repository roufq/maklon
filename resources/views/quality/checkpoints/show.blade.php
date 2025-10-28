@extends('layouts.app')
@section('title','Checkpoint ' . $checkpoint->name)
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">{{ $checkpoint->name }}</h3>
    @if(auth()->user()->can('permission','qc.edit'))<a class="btn btn-outline-primary" href="{{ route('quality.checkpoints.edit',$checkpoint) }}">Edit</a>@endif
  </div>
  <div class="card border-0 shadow-sm"><div class="card-body">
    <div><strong>Project:</strong> {{ $checkpoint->project->name ?? '-' }}</div>
    <div class="mt-2"><strong>Required:</strong> {{ $checkpoint->required ? 'Yes' : 'No' }}</div>
  </div></div>
</div>
@endsection

