@extends('layouts.auth')

@section('title', 'Login - Modern Bootstrap Admin')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <div class="text-center">
            <img src="/assets/images/logo.svg" alt="Logo" height="48" class="mb-3">
            <h2 class="h4 mb-0 fw-bold">Welcome Back</h2>
            <p class="text-white-50 mb-0">Sign in to your account</p>
        </div>
    </div>
    <div class="card-body p-4 p-md-5">
        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('status'))
            <div class="alert alert-success border-0 shadow-sm">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="user-form">
            @csrf

            <div class="mb-4">
                <label for="email" class="form-label fw-semibold">Email Address</label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text border-end-0 bg-light">
                        <i class="bi bi-envelope text-muted"></i>
                    </span>
                    <input type="email"
                           class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="Enter your email"
                           required
                           autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-semibold">Password</label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text border-end-0 bg-light">
                        <i class="bi bi-lock text-muted"></i>
                    </span>
                    <input type="password"
                           class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror"
                           id="password"
                           name="password"
                           placeholder="Enter your password"
                           required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4 d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label text-muted" for="remember">
                        Remember me
                    </label>
                </div>
                <a href="#" class="text-decoration-none small text-primary fw-semibold">
                    Forgot password?
                </a>
            </div>

            <div class="d-grid mb-4">
                <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    Sign In
                </button>
            </div>

            <div class="text-center">
                <p class="text-muted mb-3">Or sign in with</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-google me-1"></i>
                        Google
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-facebook me-1"></i>
                        Facebook
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-github me-1"></i>
                        GitHub
                    </button>
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer bg-light border-0 text-center py-4">
        <p class="mb-0 text-muted">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-primary fw-semibold text-decoration-none">
                Create one here
            </a>
        </p>
    </div>
</div>
@endsection
