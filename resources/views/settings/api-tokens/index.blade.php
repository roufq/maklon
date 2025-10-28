@extends('layouts.app')
@section('title','API Tokens')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">API Tokens</h3>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('plain_token'))
    <div class="alert alert-warning">
      <strong>Plain Token:</strong>
      <code>{{ session('plain_token') }}</code>
      <div class="small text-muted">Salin dan simpan sekarang. Tidak akan ditampilkan lagi.</div>
    </div>
  @endif

  <div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-0">Buat Token Baru</div>
    <div class="card-body">
      <form method="post" action="{{ route('settings.api-tokens.store') }}" class="row g-2">
        @csrf
        <div class="col-md-4">
          <label class="form-label">User</label>
          <select name="user_id" class="form-select" required>
            @foreach($users as $u)
              <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Nama Token</label>
          <input class="form-control" name="name" required placeholder="e.g. Integration Key" />
        </div>
        <div class="col-md-4 d-flex align-items-end">
          <button class="btn btn-primary">Generate</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead class="table-light">
          <tr><th>Name</th><th>User</th><th>Created</th><th>Token Hash (partial)</th><th></th></tr>
        </thead>
        <tbody>
          @forelse($tokens as $t)
            <tr>
              <td>{{ $t->name }}</td>
              <td>{{ $t->user->name ?? 'N/A' }}</td>
              <td>{{ $t->created_at->diffForHumans() }}</td>
              <td><code>{{ substr($t->token_hash,0,12) }}…</code></td>
              <td>
                <form method="post" action="{{ route('settings.api-tokens.destroy',$t) }}" onsubmit="return confirm('Delete this token?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center py-4">No tokens</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $tokens->links() }}</div>
  </div>
</div>
@endsection

