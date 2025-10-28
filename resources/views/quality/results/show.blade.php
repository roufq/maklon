@extends('layouts.app')
@section('title','QC Result ' . $result->id)
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">QC Result #{{ $result->id }}</h3>
    @if(auth()->user()->can('permission','qc.edit'))<a class="btn btn-outline-primary" href="{{ route('quality.results.edit',$result) }}">Edit</a>@endif
  </div>
  <div class="card border-0 shadow-sm"><div class="card-body">
    <div><strong>Batch:</strong> {{ $result->batch->batch_number }}</div>
    <div><strong>Checkpoint:</strong> {{ $result->checkpoint->name }}</div>
    <div><strong>Status:</strong> {{ strtoupper($result->status) }}</div>
    <div><strong>By:</strong> {{ $result->user->name ?? '-' }}</div>
    <div class="mt-2"><strong>Notes:</strong> {{ $result->notes ?? '-' }}</div>
  </div></div>
</div>
@endsection

