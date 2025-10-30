@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Tambah Box Type</h1>
  <form method="post" action="{{ route('box-types.store') }}">
    @csrf
    <div class="mb-3">
      <label class="form-label">Nama</label>
      <input type="text" name="name" class="form-control" required />
    </div>
    <div class="mb-3">
      <label class="form-label">Deskripsi</label>
      <textarea name="description" class="form-control"></textarea>
    </div>
    <button class="btn btn-primary">Simpan</button>
  </form>
</div>
@endsection

