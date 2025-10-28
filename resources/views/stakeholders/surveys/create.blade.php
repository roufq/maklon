@extends('layouts.app')
@section('title','Create Survey')
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">Create Survey</h3>
  <form method="post" action="{{ route('surveys.store') }}" class="card p-3 border-0 shadow-sm">
    @csrf
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Project</label>
        <select name="project_id" class="form-select" required>
          @foreach($projects as $p)
          <option value="{{ $p->id }}">{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Title</label>
        <input class="form-control" name="title" required />
      </div>
      <div class="col-12">
        <label class="form-label">Questions</label>
        <div id="qwrap" class="vstack gap-2">
          <div class="row g-2">
            <div class="col-md-6"><input class="form-control" name="questions[0][label]" placeholder="Question label" required></div>
            <div class="col-md-4"><input class="form-control" name="questions[0][key]" placeholder="key_0" required></div>
          </div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="addQ()">Add Question</button>
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-primary">Save</button>
      <a class="btn btn-outline-secondary" href="{{ route('surveys.index') }}">Cancel</a>
    </div>
  </form>
</div>
<script>
let q = 1;
function addQ(){
  const w = document.getElementById('qwrap');
  const row = document.createElement('div');
  row.className='row g-2';
  row.innerHTML = `<div class="col-md-6"><input class="form-control" name="questions[${q}][label]" placeholder="Question label" required></div>
                   <div class="col-md-4"><input class="form-control" name="questions[${q}][key]" placeholder="key_${q}" required></div>`;
  w.appendChild(row); q++;
}
</script>
@endsection

