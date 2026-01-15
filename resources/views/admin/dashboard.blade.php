@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="nav-link active">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="{{ route('admin.financial') }}" class="nav-link">
        <i class="bi bi-graph-up"></i> Laporan Keuangan
    </a>
    <a href="{{ route('admin.inventory') }}" class="nav-link">
        <i class="bi bi-box-seam"></i> Inventori
    </a>
    <a href="{{ route('admin.bookings') }}" class="nav-link">
        <i class="bi bi-calendar-check"></i> Bookings
    </a>
    <a href="{{ route('admin.payments') }}" class="nav-link">
        <i class="bi bi-credit-card"></i> Payments
    </a>
    <a href="{{ route('admin.service-prices') }}" class="nav-link">
        <i class="bi bi-tags"></i> Harga Service
    </a>
    <a href="{{ route('admin.attendance-codes') }}" class="nav-link">
        <i class="bi bi-key"></i> Kode Absen
    </a>
    <a href="{{ route('admin.attendances') }}" class="nav-link">
        <i class="bi bi-calendar-check"></i> Absensi
    </a>
    <a href="{{ route('admin.salaries') }}" class="nav-link">
        <i class="bi bi-cash-stack"></i> Gaji Karyawan
    </a>
    <a href="{{ route('admin.bonuses') }}" class="nav-link">
        <i class="bi bi-gift"></i> Bonus
    </a>
    <a href="{{ route('admin.promo-codes') }}" class="nav-link">
        <i class="bi bi-ticket-perforated"></i> Kode Promo
    </a>
@endsection

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row mb-4">
    <!-- Statistik Cards -->
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Total Booking</h6>
                        <h2 class="mb-0">{{ $stats['total_bookings'] ?? 0 }}</h2>
                    </div>
                    <i class="bi bi-calendar-check" style="font-size: 2.5rem;"></i>
                </div>
                <small>{{ $stats['today_bookings'] ?? 0 }} booking hari ini</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Pendapatan</h6>
                        <h2 class="mb-0">Rp {{ number_format($stats['revenue'] ?? 0, 0, ',', '.') }}</h2>
                    </div>
                    <i class="bi bi-cash-coin" style="font-size: 2.5rem;"></i>
                </div>
                <small>Bulan ini</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Pembayaran Pending</h6>
                        <h2 class="mb-0">{{ $stats['pending_payments'] ?? 0 }}</h2>
                    </div>
                    <i class="bi bi-clock-history" style="font-size: 2.5rem;"></i>
                </div>
                <small>Menunggu konfirmasi</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Stok Rendah</h6>
                        <h2 class="mb-0">{{ $stats['low_stock_items'] ?? 0 }}</h2>
                    </div>
                    <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem;"></i>
                </div>
                <small>Perlu restock</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Booking Terbaru -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Booking Terbaru</h5>
                <a href="{{ route('admin.bookings') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if(isset($recentBookings) && $recentBookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Kendaraan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBookings as $booking)
                            <tr>
                                <td>{{ $booking->booking_date->format('d M Y') }}</td>
                                <td>{{ $booking->customer->name }}</td>
                                <td>{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</td>
                                <td>
                                    @if($booking->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                    @elseif($booking->status == 'in_progress')
                                    <span class="badge bg-info">Dikerjakan</span>
                                    @else
                                    <span class="badge bg-success">Selesai</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.billing', $booking->id) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-receipt"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-calendar-x" style="font-size: 2rem;"></i>
                    <p class="mt-2">Belum ada booking</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Pembayaran Pending -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-credit-card"></i> Pembayaran Pending</h5>
            </div>
            <div class="card-body">
                @if(isset($pendingPayments) && $pendingPayments->count() > 0)
                <div class="list-group">
                    @foreach($pendingPayments as $payment)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">#{{ $payment->booking_id }}</h6>
                                <small class="text-muted">{{ $payment->booking->customer->name }}</small>
                            </div>
                            <div class="text-end">
                                <strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong><br>
                                <a href="{{ route('admin.payments') }}" class="btn btn-sm btn-outline-primary mt-1">
                                    Proses
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                    <p class="mt-2">Tidak ada pembayaran pending</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Stok Rendah -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Stok Rendah</h5>
            </div>
            <div class="card-body">
                @if(isset($lowStockItems) && $lowStockItems->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Sparepart</th>
                                <th>Stok</th>
                                <th>Minimum</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockItems as $item)
                            <tr>
                                <td>{{ $item->part_name }}</td>
                                <td><span class="badge bg-danger">{{ $item->quantity }}</span></td>
                                <td>{{ $item->low_stock_level }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                            data-bs-target="#editInventoryModal{{ $item->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                    <p class="mt-2">Semua stok aman</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Aktivitas Terbaru -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Aktivitas Terbaru</h5>
            </div>
            <div class="card-body">
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-clock-history" style="font-size: 2rem;"></i>
                    <p class="mt-2">Fitur aktivitas terbaru akan datang</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(var(--bs-primary-rgb), 0.1);
    }
    
    .card {
        transition: transform 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
</style>
@endpush