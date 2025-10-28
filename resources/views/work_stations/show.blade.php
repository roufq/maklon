@extends('layouts.app')
@section('title','Work Station: '.$item->name)
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">{{ $item->name }}</h3>
    @can('production.edit')
      <a href="{{ route('work-stations.edit',$item) }}" class="btn btn-secondary">Edit</a>
    @endcan
  </div>
  <div class="card card-body">
    <div class="row">
      <div class="col-md-4"><strong>Capacity per hour</strong><div>{{ $item->capacity_per_hour }}</div></div>
      <div class="col-md-4"><strong>Status</strong><div>{{ ucfirst($item->status) }}</div></div>
    </div>
  </div>
</div>
@endsection

