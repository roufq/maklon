@extends('layouts.app')
@section('title','Edit User')
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">Edit User</h3>
  <form method="post" action="{{ route('admin.users.update', $user) }}" class="card p-3 border-0 shadow-sm">
    @csrf @method('PUT')
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Name</label>
        <input class="form-control" name="name" value="{{ $user->name }}" required />
      </div>
      <div class="col-md-4">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email" value="{{ $user->email }}" required />
      </div>
      <div class="col-md-4">
        <label class="form-label">Password (leave blank to keep)</label>
        <input class="form-control" type="password" name="password" />
      </div>
      <div class="col-md-4">
        <label class="form-label">Role</label>
        <select name="role" class="form-select" required>
          @foreach($roles as $id => $name)
            <option value="{{ $name }}" {{ $currentRole===$name ? 'selected' : '' }}>{{ $name }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-primary">Update</button>
      <a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">Cancel</a>
    </div>
  </form>
</div>
@endsection

