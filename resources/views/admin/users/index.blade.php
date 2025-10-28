@extends('layouts.app')
@section('title','Users')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Users</h3>
    <a class="btn btn-primary" href="{{ route('admin.users.create') }}">New User</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <form class="row g-2 mb-3" method="get">
    <div class="col-auto">
      <select name="role" class="form-select" onchange="this.form.submit()">
        <option value="">All Roles</option>
        @foreach($roles as $r)
          <option value="{{ $r }}" {{ $role===$r ? 'selected' : '' }}>{{ $r }}</option>
        @endforeach
      </select>
    </div>
  </form>

  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead class="table-light">
          <tr><th>Name</th><th>Email</th><th>Role</th><th>Created</th><th></th></tr>
        </thead>
        <tbody>
          @forelse($users as $u)
          <tr>
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->roles->pluck('name')->first() ?? '-' }}</td>
            <td>{{ $u->created_at->format('Y-m-d') }}</td>
            <td class="d-flex gap-2">
              <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.users.edit', $u) }}">Edit</a>
              <form method="post" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Delete user?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="5" class="text-center py-4">No users</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $users->withQueryString()->links() }}</div>
  </div>
</div>
@endsection

