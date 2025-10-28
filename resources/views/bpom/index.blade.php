@extends('layouts.app')
@section('title','BPOM Registrations')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">BPOM Registrations</h3>
    @if(auth()->user()->can('permission','bpom.create'))
      <a class="btn btn-primary" href="{{ route('bpom.create') }}">New Registration</a>
    @endif
  </div>
  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table mb-0">
        <thead class="table-light">
          <tr><th>No</th><th>Product</th><th>Approval</th><th>Expiry</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
          @foreach($items as $r)
          <tr>
            <td><a href="{{ route('bpom.show',$r) }}">{{ $r->registration_number }}</a></td>
            <td>{{ $r->product_name }}</td>
            <td>{{ optional($r->approval_date)->format('Y-m-d') ?: '-' }}</td>
            <td>{{ optional($r->expiry_date)->format('Y-m-d') ?: '-' }}</td>
            <td><span class="badge bg-secondary">{{ ucfirst($r->status) }}</span></td>
            <td>
              @if($r->document_path)
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('bpom.download',$r) }}">Document</a>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $items->links() }}</div>
  </div>
</div>
@endsection

