@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Edit Box Type</h1>
  <form method="post" action="{{ route('box-types.update',$boxType) }}">
    @csrf
    @method('PUT')
    <div class="mb-3">
      <label class="form-label">Nama</label>
      <input type="text" name="name" class="form-control" value="{{ old('name',$boxType->name) }}" />
    </div>
    <div class="mb-3">
      <label class="form-label">Deskripsi</label>
      <textarea name="description" class="form-control">{{ old('description',$boxType->description) }}</textarea>
    </div>
    <button class="btn btn-primary">Simpan</button>
  </form>
</div>
@endsection

