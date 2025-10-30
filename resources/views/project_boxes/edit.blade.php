@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Edit Project Box</h1>
  <form method="post" action="{{ route('project-boxes.update',$projectBox) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="mb-3">
      <label class="form-label">Project</label>
      <select name="project_id" class="form-select">
        @foreach($projects as $p)
          <option value="{{ $p->id }}" @if($projectBox->project_id==$p->id) selected @endif>{{ $p->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Box Type</label>
      <select name="box_type_id" class="form-select">
        @foreach($boxTypes as $t)
          <option value="{{ $t->id }}" @if($projectBox->box_type_id==$t->id) selected @endif>{{ $t->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Size</label>
      <input type="text" name="size" class="form-control" value="{{ old('size',$projectBox->size) }}" />
    </div>
    <div class="mb-3">
      <label class="form-label">Shape</label>
      <input type="text" name="shape" class="form-control" value="{{ old('shape',$projectBox->shape) }}" />
    </div>
    <div class="mb-3">
      <label class="form-label">Mockup (upload untuk ganti)</label>
      <input type="file" name="mockup" class="form-control" />
      @if($projectBox->mockup_path)
      <div class="mt-2"><a target="_blank" href="{{ asset('storage/'.$projectBox->mockup_path) }}">Mockup saat ini</a></div>
      @endif
    </div>
    <button class="btn btn-primary">Simpan</button>
  </form>
</div>
@endsection

