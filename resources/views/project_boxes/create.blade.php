@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Tambah Project Box</h1>
  <form method="post" action="{{ route('project-boxes.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
      <label class="form-label">Project</label>
      <select name="project_id" class="form-select">
        @foreach($projects as $p)
          <option value="{{ $p->id }}" @if(isset($projectId) && $projectId==$p->id) selected @endif>{{ $p->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Box Type</label>
      <select name="box_type_id" class="form-select">
        @foreach($boxTypes as $t)
          <option value="{{ $t->id }}">{{ $t->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Size</label>
      <input type="text" name="size" class="form-control" placeholder="cth: 10x10x5 cm" />
    </div>
    <div class="mb-3">
      <label class="form-label">Shape</label>
      <input type="text" name="shape" class="form-control" placeholder="rectangular/cylindrical" />
    </div>
    <div class="mb-3">
      <label class="form-label">Mockup</label>
      <input type="file" name="mockup" class="form-control" />
    </div>
    <button class="btn btn-primary">Simpan</button>
  </form>
</div>
@endsection

