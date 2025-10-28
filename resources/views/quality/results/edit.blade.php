@extends('layouts.app')
@section('title','Edit QC Result ' . $result->id)
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">Edit QC Result</h3>
  <form method="post" action="{{ route('quality.results.update',$result) }}" class="card p-3 border-0 shadow-sm">
    @csrf @method('PUT')
    <div class="row g-3">
      <div class="col-md-4"><label class="form-label">Batch</label><select name="production_batch_id" class="form-select" required>@foreach($batches as $b)<option value="{{ $b->id }}" @selected($b->id==$result->production_batch_id)>{{ $b->batch_number }}</option>@endforeach</select></div>
      <div class="col-md-4"><label class="form-label">Checkpoint</label><select name="quality_checkpoint_id" class="form-select" required>@foreach($checkpoints as $c)<option value="{{ $c->id }}" @selected($c->id==$result->quality_checkpoint_id)>{{ $c->name }}</option>@endforeach</select></div>
      <div class="col-md-2"><label class="form-label">Status</label><select name="status" class="form-select" required><option value="pass" @selected($result->status==='pass')>PASS</option><option value="fail" @selected($result->status==='fail')>FAIL</option></select></div>
      <div class="col-md-12"><label class="form-label">Notes</label><input name="notes" class="form-control" value="{{ $result->notes }}" /></div>
    </div>
    <div class="mt-3"><button class="btn btn-primary">Save</button><a class="btn btn-outline-secondary ms-2" href="{{ route('quality.results.show',$result) }}">Cancel</a></div>
  </form>
</div>
@endsection

