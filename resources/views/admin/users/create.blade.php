@extends('layouts.app')
@section('title','Create User')
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">Create User</h3>
  <form method="post" action="{{ route('admin.users.store') }}" class="card p-3 border-0 shadow-sm">
    @csrf
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Name</label>
        <input class="form-control" name="name" required />
      </div>
      <div class="col-md-4">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email" required />
      </div>
      <div class="col-md-4">
        <label class="form-label">Password</label>
        <input class="form-control" type="password" name="password" required />
      </div>
      <div class="col-md-4">
        <label class="form-label">Role</label>
        <select name="role" class="form-select" required>
          @foreach($roles as $id => $name)
            <option value="{{ $name }}">{{ $name }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-primary">Save</button>
      <a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection

