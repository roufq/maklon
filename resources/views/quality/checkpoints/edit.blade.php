@extends('layouts.app')
@section('title','Edit Checkpoint ' . $checkpoint->name)
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">Edit QC Checkpoint</h3>
  <form method="post" action="{{ route('quality.checkpoints.update',$checkpoint) }}" class="card p-3 border-0 shadow-sm">
    @csrf @method('PUT')
    <div class="row g-3">
      <div class="col-md-5"><label class="form-label">Name</label><input name="name" class="form-control" value="{{ $checkpoint->name }}" required /></div>
      <div class="col-md-4"><label class="form-label">Project</label><select name="project_id" class="form-select"><option value="">-</option>@foreach($projects as $p)<option value="{{ $p->id }}" @selected($p->id==$checkpoint->project_id)>{{ $p->name }}</option>@endforeach</select></div>
      <div class="col-md-3 form-check mt-4"><input type="checkbox" class="form-check-input" name="required" value="1" id="req" @checked($checkpoint->required)><label for="req" class="form-check-label">Required</label></div>
    </div>
    <div class="mt-3"><button class="btn btn-primary">Save</button><a class="btn btn-outline-secondary ms-2" href="{{ route('quality.checkpoints.show',$checkpoint) }}">Cancel</a></div>
  </form>
</div>
@endsection

