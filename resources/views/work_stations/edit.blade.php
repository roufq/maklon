@extends('layouts.app')
@section('title','Edit Work Station')
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">Edit Work Station</h3>
  <form method="post" action="{{ route('work-stations.update',$item) }}" class="card card-body mb-3">
    @csrf
    @method('PUT')
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" value="{{ old('name',$item->name) }}" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Capacity per hour</label>
      <input type="number" name="capacity_per_hour" class="form-control" value="{{ old('capacity_per_hour',$item->capacity_per_hour) }}" min="0" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select" required>
        @foreach(['active','maintenance','offline'] as $s)
          <option value="{{ $s }}" @selected(old('status',$item->status)===$s)>{{ ucfirst($s) }}</option>
        @endforeach
      </select>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-primary" type="submit">Update</button>
      <a href="{{ route('work-stations.show',$item) }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
  @can('production.delete')
  <form method="post" action="{{ route('work-stations.destroy',$item) }}" onsubmit="return confirm('Delete?')" class="card card-body">
    @csrf
    @method('DELETE')
    <button class="btn btn-outline-danger" type="submit">Delete</button>
  </form>
  @endcan
</div>
@endsection
