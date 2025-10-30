@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Edit Ticket</h1>
  <form method="post" action="{{ route('tickets.update',$ticket) }}">
    @csrf
    @method('PUT')
    <div class="mb-3">
      <label class="form-label">Judul</label>
      <input type="text" name="title" class="form-control" value="{{ old('title',$ticket->title) }}" />
    </div>
    <div class="mb-3">
      <label class="form-label">Prioritas</label>
      <select name="priority" class="form-select">
        @foreach(['low','normal','high','urgent'] as $p)
          <option value="{{ $p }}" @if($ticket->priority===$p) selected @endif>{{ $p }}</option>
        @endforeach
      </select>
    </div>
    <button class="btn btn-primary">Simpan</button>
  </form>
</div>
@endsection

