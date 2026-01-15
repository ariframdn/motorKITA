@extends('layouts.app')

@section('title', 'Billing & Invoice')
@section('page-title', 'Billing & Invoice')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="nav-link">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="{{ route('admin.inventory') }}" class="nav-link">
        <i class="bi bi-box-seam"></i> Inventori
    </a>
    <a href="{{ route('admin.bookings') }}" class="nav-link">
        <i class="bi bi-calendar-check"></i> Bookings
    </a>
@endsection

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-receipt"></i> Invoice Booking</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Informasi Pelanggan</h6>
                        <p class="mb-1"><strong>{{ $booking->customer->name }}</strong></p>
                        <p class="mb-1">{{ $booking->customer->email }}</p>
                        <p class="mb-0">{{ $booking->customer->phone }}</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <h6 class="text-muted">Informasi Booking</h6>
                        <p class="mb-1"><strong>No. Booking:</strong> #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</p>
                        <p class="mb-1"><strong>Tanggal:</strong> {{ $booking->booking_date->format('d M Y') }}</p>
                        <p class="mb-0"><strong>Status:</strong> 
                            @if($booking->status == 'pending')
                            <span class="badge bg-warning">Pending</span>
                            @elseif($booking->status == 'in_progress')
                            <span class="badge bg-info">Dikerjakan</span>
                            @else
                            <span class="badge bg-success">Selesai</span>
                            @endif
                        </p>
                    </div>
                </div>

                <hr>

                <div class="mb-4">
                    <h6 class="text-muted">Detail Kendaraan</h6>
                    <p class="mb-1"><strong>Kendaraan:</strong> {{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</p>
                    <p class="mb-1"><strong>Plat Nomor:</strong> {{ $booking->vehicle->plate_number }}</p>
                    <p class="mb-0"><strong>Jenis Servis:</strong> {{ $booking->service_type }}</p>
                </div>

                @if($booking->mechanic)
                <div class="mb-4">
                    <h6 class="text-muted">Mekanik</h6>
                    <p class="mb-0">{{ $booking->mechanic->name }}</p>
                </div>
                @endif

                @if($booking->notes)
                <div class="mb-4">
                    <h6 class="text-muted">Catatan</h6>
                    <p class="mb-0">{{ $booking->notes }}</p>
                </div>
                @endif

                <hr>

                <form action="{{ route('admin.billing.update', $booking->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label"><strong>Total Biaya (Rp)</strong></label>
                        <input type="number" name="cost" class="form-control form-control-lg" 
                               value="{{ $booking->cost }}" min="0" step="0.01" required>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Biaya
                        </button>
                        <a href="{{ route('admin.bookings') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-cash-coin"></i> Ringkasan</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span>Subtotal:</span>
                    <strong>Rp {{ number_format($booking->cost, 0, ',', '.') }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span><strong>Total:</strong></span>
                    <strong class="text-primary" style="font-size: 1.2rem;">
                        Rp {{ number_format($booking->cost, 0, ',', '.') }}
                    </strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

