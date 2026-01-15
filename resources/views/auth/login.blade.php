@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="card shadow-lg">
    <div class="card-body p-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary">
                <i class="bi bi-tools"></i> MotorKita
            </h2>
            <p class="text-muted">Sistem Manajemen Bengkel Motor</p>
        </div>
        
        <h4 class="mb-4">Login</h4>
        
        @if($errors->any())
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle"></i> {{ $errors->first() }}
        </div>
        @endif
        
        <form action="{{ route('login') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" name="email" class="form-control" 
                           value="{{ old('email') }}" required autofocus>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 mb-3">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </button>
        </form>
        
        <div class="text-center">
            <p class="mb-0">Belum punya akun? 
                <a href="{{ route('register') }}" class="text-primary fw-bold">Daftar Sekarang</a>
            </p>
        </div>
        
        <hr class="my-4">
        
    </div>
</div>
@endsection