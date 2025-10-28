@extends('layouts.app')
@section('title','Verify 2FA')
@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card border-0 shadow-sm mt-5">
        <div class="card-body">
          <h5 class="mb-3">Two-Factor Verification</h5>
          @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
          <form method="post" action="{{ route('2fa.verify') }}" class="row g-2">
            @csrf
            <div class="col-12"><input class="form-control" name="code" placeholder="Enter 6-digit code or recovery code" required></div>
            <div class="col-12"><button class="btn btn-primary w-100">Verify</button></div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

