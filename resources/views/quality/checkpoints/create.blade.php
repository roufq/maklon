@extends('layouts.app')
@section('title','New Checkpoint')
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">New QC Checkpoint</h3>
  <form method="post" action="{{ route('quality.checkpoints.store') }}" class="card p-3 border-0 shadow-sm">
    @csrf
    <div class="row g-3">
      <div class="col-md-5"><label class="form-label">Name</label><input name="name" class="form-control" required /></div>
      <div class="col-md-4"><label class="form-label">Project</label><select name="project_id" class="form-select"><option value="">-</option>@foreach($projects as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
      <div class="col-md-3 form-check mt-4"><input type="checkbox" class="form-check-input" name="required" value="1" id="req"><label for="req" class="form-check-label">Required</label></div>
    </div>
    <div class="mt-3"><button class="btn btn-primary">Create</button><a class="btn btn-outline-secondary ms-2" href="{{ route('quality.checkpoints.index') }}">Cancel</a></div>
  </form>
</div>
@endsection

