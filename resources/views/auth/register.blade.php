@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<div class="card shadow-lg">
    <div class="card-body p-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary">
                <i class="bi bi-tools"></i> MotorKita
            </h2>
            <p class="text-muted">Daftar Akun Baru</p>
        </div>
        
        <h4 class="mb-4">Register</h4>
        
        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        <form action="{{ route('register') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" 
                       value="{{ old('name') }}" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" 
                       value="{{ old('email') }}" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">No. Telepon</label>
                <input type="text" name="phone" class="form-control" 
                       value="{{ old('phone') }}" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            
            <div class="mb-4">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 mb-3">
                <i class="bi bi-person-plus"></i> Daftar
            </button>
        </form>
        
        <div class="text-center">
            <p class="mb-0">Sudah punya akun? 
                <a href="{{ route('login') }}" class="text-primary fw-bold">Login</a>
            </p>
        </div>
    </div>
</div>
@endsection