@extends('layouts.app')
@section('title','New Work Station')
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">New Work Station</h3>
  <form method="post" action="{{ route('work-stations.store') }}" class="card card-body">
    @csrf
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Capacity per hour</label>
      <input type="number" name="capacity_per_hour" class="form-control" value="{{ old('capacity_per_hour',0) }}" min="0" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select" required>
        @foreach(['active','maintenance','offline'] as $s)
          <option value="{{ $s }}" @selected(old('status')===$s)>{{ ucfirst($s) }}</option>
        @endforeach
      </select>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-primary" type="submit">Save</button>
      <a href="{{ route('work-stations.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection

