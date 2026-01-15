@extends('layouts.app')

@section('title', 'Detail Gaji')
@section('page-title', 'Detail Gaji')

@section('sidebar')
    @if(auth()->user()->isMechanic())
    <a href="{{ route('mechanic.dashboard') }}" class="nav-link">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="{{ route('mechanic.salaries') }}" class="nav-link active">
        <i class="bi bi-cash-stack"></i> Gaji
    </a>
    @else
    <a href="{{ route('admin.dashboard') }}" class="nav-link">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="{{ route('admin.salaries') }}" class="nav-link active">
        <i class="bi bi-cash-stack"></i> Gaji Karyawan
    </a>
    @endif
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-receipt-cutoff"></i> Detail Gaji</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Mekanik</h6>
                        <p class="fs-5">{{ $salary->mechanic->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Periode</h6>
                        <p class="fs-5">
                            {{ $salary->period_start->format('d M Y') }} - {{ $salary->period_end->format('d M Y') }}
                        </p>
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Gaji Pokok</h6>
                        <p class="fs-5">Rp {{ number_format($salary->base_salary, 0, ',', '.') }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Gaji Harian</h6>
                        <p class="fs-5">Rp {{ number_format($salary->daily_rate, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Hari Kerja</h6>
                        <p class="fs-5">{{ $salary->attendance_days }} hari</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Bonus</h6>
                        <p class="fs-5">
                            @if($salary->bonus_amount > 0)
                                Rp {{ number_format($salary->bonus_amount, 0, ',', '.') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </p>
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6 class="text-muted">Total Gaji</h6>
                        <h2 class="text-success">Rp {{ number_format($salary->total_amount, 0, ',', '.') }}</h2>
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Metode Pembayaran</h6>
                        <p class="fs-5">
                            <span class="badge bg-{{ $salary->payment_method == 'transfer' ? 'info' : 'secondary' }}">
                                {{ $salary->payment_method == 'transfer' ? 'Transfer' : 'Cash' }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Status</h6>
                        <p class="fs-5">
                            <span class="badge bg-{{ $salary->status == 'paid' ? 'success' : ($salary->status == 'pending' ? 'warning' : 'danger') }}">
                                {{ $salary->status == 'paid' ? 'Dibayar' : ($salary->status == 'pending' ? 'Pending' : 'Dibatalkan') }}
                            </span>
                        </p>
                    </div>
                </div>

                @if($salary->payment_proof)
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6 class="text-muted">Bukti Transfer</h6>
                        <img src="{{ asset('storage/salaries/' . $salary->payment_proof) }}" 
                             alt="Payment Proof" 
                             class="img-thumbnail" 
                             style="max-width: 400px;">
                    </div>
                </div>
                @endif

                @if($salary->notes)
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6 class="text-muted">Catatan</h6>
                        <p>{{ $salary->notes }}</p>
                    </div>
                </div>
                @endif

                @if($salary->payment_date)
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6 class="text-muted">Tanggal Pembayaran</h6>
                        <p>{{ \Carbon\Carbon::parse($salary->payment_date)->format('d M Y H:i:s') }}</p>
                    </div>
                </div>
                @endif

                @if($salary->processor)
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6 class="text-muted">Diproses Oleh</h6>
                        <p>{{ $salary->processor->name }}</p>
                    </div>
                </div>
                @endif

                <div class="mt-4">
                    <a href="{{ auth()->user()->isMechanic() ? route('mechanic.salaries') : route('admin.salaries') }}" 
                       class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
