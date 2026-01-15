@extends('layouts.app')

@section('title', 'Manajemen Bookings')
@section('page-title', 'Manajemen Bookings')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="nav-link">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="{{ route('admin.inventory') }}" class="nav-link">
        <i class="bi bi-box-seam"></i> Inventori
    </a>
    <a href="{{ route('admin.bookings') }}" class="nav-link active">
        <i class="bi bi-calendar-check"></i> Bookings
    </a>
    <a href="{{ route('admin.payments') }}" class="nav-link">
        <i class="bi bi-credit-card"></i> Payments
    </a>
    <a href="{{ route('admin.service-prices') }}" class="nav-link">
        <i class="bi bi-tags"></i> Harga Service
    </a>
@endsection

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Semua Booking</h5>
    </div>
    <div class="card-body">
        @if($bookings->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
            <p class="mt-3">Belum ada booking</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Kendaraan</th>
                        <th>Jenis Servis</th>
                        <th>Mekanik</th>
                        <th>Status</th>
                        <th>Pembayaran</th>
                        <th>Biaya</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr>
                        <td>{{ $booking->booking_date->format('d M Y') }}</td>
                        <td>{{ $booking->customer->name }}</td>
                        <td>{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}<br>
                            <small class="text-muted">{{ $booking->vehicle->plate_number }}</small>
                        </td>
                        <td>{{ $booking->service_type }}</td>
                        <td>
                            @if($booking->mechanic)
                            {{ $booking->mechanic->name }}
                            @else
                            <form action="{{ route('admin.bookings.assign', $booking->id) }}" method="POST" class="d-inline">
                                @csrf
                                <select name="mechanic_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">Pilih Mekanik</option>
                                    @foreach($mechanics as $mechanic)
                                    <option value="{{ $mechanic->id }}">{{ $mechanic->name }}</option>
                                    @endforeach
                                </select>
                            </form>
                            @endif
                        </td>
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
                            @if($booking->payment_status == 'paid')
                            <span class="badge bg-success">Lunas</span>
                            @elseif($booking->payment_status == 'rejected')
                            <span class="badge bg-danger">Ditolak</span>
                            @else
                            <span class="badge bg-warning">Belum Bayar</span>
                            @endif
                            @if($booking->payment_method)
                            <br><small class="text-muted">{{ ucfirst($booking->payment_method) }}</small>
                            @endif
                        </td>
                        <td>Rp {{ number_format($booking->cost, 0, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('admin.billing', $booking->id) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-receipt"></i> Invoice
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

