@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Buat Ticket</h1>
  <form method="post" action="{{ route('tickets.store') }}">
    @csrf
    <div class="mb-3">
      <label class="form-label">Project</label>
      <select name="project_id" class="form-select" required>
        @foreach($projects as $p)
          <option value="{{ $p->id }}">{{ $p->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Judul</label>
      <input type="text" name="title" class="form-control" required />
    </div>
    <div class="mb-3">
      <label class="form-label">Prioritas</label>
      <select name="priority" class="form-select">
        <option value="low">low</option>
        <option value="normal" selected>normal</option>
        <option value="high">high</option>
        <option value="urgent">urgent</option>
      </select>
    </div>
    <button class="btn btn-primary">Simpan</button>
  </form>
</div>
@endsection

