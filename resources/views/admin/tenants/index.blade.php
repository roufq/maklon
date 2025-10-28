@extends('layouts.app')
@section('title','Tenants')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Tenants</h3>
  </div>
  @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

  <div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-0">Switch Tenant</div>
    <div class="card-body">
      <form method="post" action="{{ route('admin.tenants.switch') }}" class="row g-2">
        @csrf
        <div class="col-md-6">
          <select class="form-select" name="tenant_id">
            <option value="">— Use my default tenant —</option>
            @foreach(\App\Models\Tenant::orderBy('name')->get() as $t)
              <option value="{{ $t->id }}" {{ (string)$currentId === (string)$t->id ? 'selected' : '' }}>{{ $t->name }} {{ $t->domain ? '(' . $t->domain . ')' : '' }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3"><button class="btn btn-primary w-100">Switch</button></div>
      </form>
      <div class="small text-muted mt-2">Switcher ini hanya mempengaruhi sesi Anda dan tidak mengubah tenant default user.</div>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0">Create Tenant</div>
    <div class="card-body">
      <form method="post" action="{{ route('admin.tenants.store') }}" class="row g-2">
        @csrf
        <div class="col-md-4"><input class="form-control" name="name" placeholder="Tenant Name" required></div>
        <div class="col-md-4"><input class="form-control" name="domain" placeholder="domain.example.com (optional)"></div>
        <div class="col-md-2"><button class="btn btn-outline-primary w-100">Create</button></div>
      </form>
    </div>
  </div>

  <div class="card border-0 shadow-sm mt-3">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead class="table-light"><tr><th>Name</th><th>Domain</th><th>Created</th></tr></thead>
        <tbody>
          @foreach($tenants as $t)
            <tr>
              <td>{{ $t->name }}</td>
              <td>{{ $t->domain ?? '-' }}</td>
              <td>{{ $t->created_at->format('Y-m-d') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $tenants->links() }}</div>
  </div>
</div>
@endsection

