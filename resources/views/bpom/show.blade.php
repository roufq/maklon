@extends('layouts.app')
@section('title','BPOM ' . $item->registration_number)
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">BPOM {{ $item->registration_number }}</h3>
    <div class="d-flex gap-2">
      @if(auth()->user()->can('permission','bpom.edit'))
        <a class="btn btn-outline-primary" href="{{ route('bpom.edit',$item) }}">Edit</a>
      @endif
      @if(auth()->user()->can('permission','bpom.delete'))
      <form method="post" action="{{ route('bpom.destroy',$item) }}" onsubmit="return confirm('Delete?')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger">Delete</button>
      </form>
      @endif
    </div>
  </div>
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-6">
          <div><strong>Product:</strong> {{ $item->product_name }}</div>
          <div><strong>Approval:</strong> {{ optional($item->approval_date)->format('Y-m-d') ?: '-' }}</div>
          <div><strong>Expiry:</strong> {{ optional($item->expiry_date)->format('Y-m-d') ?: '-' }}</div>
        </div>
        <div class="col-md-6">
          <div><strong>Status:</strong> {{ ucfirst($item->status) }}</div>
          <div><strong>Document:</strong>
            @if($item->document_path)
              <a href="{{ route('bpom.download',$item) }}">Download</a>
            @else
              -
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

