@extends('layouts.app')
@section('title','New QC Result')
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">New QC Result</h3>
  <form method="post" action="{{ route('quality.results.store') }}" class="card p-3 border-0 shadow-sm">
    @csrf
    <div class="row g-3">
      <div class="col-md-4"><label class="form-label">Batch</label><select name="production_batch_id" class="form-select" required>@foreach($batches as $b)<option value="{{ $b->id }}">{{ $b->batch_number }}</option>@endforeach</select></div>
      <div class="col-md-4"><label class="form-label">Checkpoint</label><select name="quality_checkpoint_id" class="form-select" required>@foreach($checkpoints as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
      <div class="col-md-2"><label class="form-label">Status</label><select name="status" class="form-select" required><option value="pass">PASS</option><option value="fail">FAIL</option></select></div>
      <div class="col-md-12"><label class="form-label">Notes</label><input name="notes" class="form-control" /></div>
    </div>
    <div class="mt-3"><button class="btn btn-primary">Create</button><a class="btn btn-outline-secondary ms-2" href="{{ route('quality.results.index') }}">Cancel</a></div>
  </form>
</div>
@endsection

