@extends('layouts.app')
@section('title','Two-Factor Authentication')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Two-Factor Authentication (2FA)</h3>
  </div>
  @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
  @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      @if(!$user->two_factor_enabled)
        <p class="text-muted">Scan QR dengan aplikasi authenticator (Google Authenticator / Authy) atau masukkan secret secara manual.</p>
        <div class="row g-3">
          <div class="col-md-6">
            <div class="mb-2"><strong>Secret:</strong> <code>{{ $user->two_factor_secret }}</code></div>
            <div class="mb-2"><strong>OTP Auth URI:</strong> <code style="word-break:break-all">{{ $otpauth }}</code></div>
            <div class="mb-3">
              <strong>Scan QR:</strong>
              <div class="mt-2">
                <img alt="2FA QR" width="200" height="200"
                     src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($otpauth) }}">
              </div>
              <small class="text-muted">Jika QR tidak tampil, gunakan Secret di atas secara manual.</small>
            </div>
          </div>
        </div>
        <form method="post" action="{{ route('2fa.enable') }}" class="row g-2">
          @csrf
          <div class="col-md-3"><input class="form-control" name="code" placeholder="Enter 6-digit code" required></div>
          <div class="col-md-3"><button class="btn btn-primary">Enable 2FA</button></div>
        </form>
      @else
        <div class="mb-2"><span class="badge bg-success">Enabled</span></div>
        <div class="mb-2"><strong>Recovery Codes:</strong></div>
        <ul class="list-inline">
          @foreach(($user->two_factor_recovery_codes ?? []) as $code)
            <li class="list-inline-item"><code>{{ $code }}</code></li>
          @endforeach
        </ul>
        <form method="post" action="{{ route('2fa.disable') }}" onsubmit="return confirm('Disable 2FA?')">
          @csrf
          <button class="btn btn-outline-danger">Disable 2FA</button>
        </form>
      @endif
    </div>
  </div>
</div>
@endsection
