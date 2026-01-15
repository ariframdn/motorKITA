@extends('layouts.app')

@section('title', 'Absensi')
@section('page-title', 'Absensi Harian')

@section('sidebar')
    <a href="{{ route('mechanic.dashboard') }}" class="nav-link">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="{{ route('mechanic.tasks') }}" class="nav-link">
        <i class="bi bi-list-task"></i> Daftar Tugas
    </a>
    <a href="{{ route('mechanic.attendance') }}" class="nav-link active">
        <i class="bi bi-calendar-check"></i> Absensi
    </a>
    <a href="{{ route('mechanic.earnings') }}" class="nav-link">
        <i class="bi bi-wallet2"></i> Penghasilan
    </a>
    <a href="{{ route('mechanic.salaries') }}" class="nav-link">
        <i class="bi bi-cash-stack"></i> Gaji
    </a>
    <a href="{{ route('mechanic.reviews') }}" class="nav-link">
        <i class="bi bi-star"></i> Review
    </a>
    <a href="{{ route('profile.edit') }}" class="nav-link">
        <i class="bi bi-person"></i> Profil
    </a>
@endsection

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Absensi Hari Ini</h5>
            </div>
            <div class="card-body">
                @if($todayAttendance)
                <!-- Already Checked In -->
                <div class="alert alert-info">
                    <h5><i class="bi bi-check-circle"></i> Anda sudah absen masuk hari ini</h5>
                    <hr>
                    <p class="mb-1"><strong>Waktu Check In:</strong> {{ $todayAttendance->check_in_time ?? '-' }}</p>
                    <p class="mb-1"><strong>Waktu Check Out:</strong> {{ $todayAttendance->check_out_time ?? 'Belum check out' }}</p>
                    <p class="mb-0"><strong>Kode Absen:</strong> <code>{{ $todayAttendance->attendanceCode->code ?? '-' }}</code></p>
                </div>

                @if(!$todayAttendance->check_out_time)
                <form action="{{ route('mechanic.attendance.check-out') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-lg w-100">
                        <i class="bi bi-box-arrow-right"></i> Check Out
                    </button>
                </form>
                @else
                <div class="alert alert-success">
                    <i class="bi bi-check-all"></i> Anda sudah check out hari ini
                </div>
                @endif
                @else
                <!-- Check In Form -->
                @if($todayCode)
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle"></i> 
                    <strong>Kode Absen Hari Ini:</strong> <code style="font-size: 1.5rem; letter-spacing: 3px;">{{ $todayCode->code }}</code>
                    <br><small>Masukkan kode ini untuk absen masuk</small>
                </div>

                <form action="{{ route('mechanic.attendance.check-in') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label"><strong>Masukkan Kode Absen (8 digit)</strong></label>
                        <input type="text" name="code" class="form-control form-control-lg text-center" 
                               style="font-size: 1.5rem; letter-spacing: 5px; font-weight: bold;"
                               maxlength="8" required autofocus
                               placeholder="XXXXXXXX">
                        <small class="text-muted">Kode case-insensitive</small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Check In
                    </button>
                </form>
                @else
                <div class="alert alert-warning text-center py-5">
                    <i class="bi bi-exclamation-triangle" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Kode Absen Belum Tersedia</h5>
                    <p class="text-muted">Admin belum generate kode absen untuk hari ini. Silakan hubungi admin.</p>
                </div>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
