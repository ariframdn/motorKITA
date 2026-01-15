@extends('layouts.app')

@section('title', 'Dashboard Customer')
@section('page-title', 'Dashboard Customer')

@section('sidebar')
    <a href="{{ route('customer.dashboard') }}" class="nav-link active">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="{{ route('customer.booking') }}" class="nav-link">
        <i class="bi bi-calendar-plus"></i> Booking Servis
    </a>
    <a href="{{ route('customer.history') }}" class="nav-link">
        <i class="bi bi-clock-history"></i> Riwayat Servis
    </a>
    <a href="{{ route('profile.edit') }}" class="nav-link">
        <i class="bi bi-person"></i> Profil
    </a>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <h6 class="text-white-50 mb-2">Total Kendaraan</h6>
            <h2 class="fw-bold">{{ $vehicles->count() }}</h2>
            <i class="bi bi-bicycle" style="font-size: 3rem; opacity: 0.3; position: absolute; right: 20px; top: 20px;"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white" style="padding: 25px; border-radius: 12px;">
            <h6 class="text-white-50 mb-2">Booking Aktif</h6>
            <h2 class="fw-bold">{{ $upcomingBookings->count() }}</h2>
            <i class="bi bi-calendar-check" style="font-size: 3rem; opacity: 0.3; position: absolute; right: 20px; top: 20px;"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-white" style="padding: 25px; border-radius: 12px;">
            <h6 class="text-white-50 mb-2">Menunggu Servis</h6>
            <h2 class="fw-bold">{{ $upcomingBookings->where('status', 'pending')->count() }}</h2>
            <i class="bi bi-hourglass-split" style="font-size: 3rem; opacity: 0.3; position: absolute; right: 20px; top: 20px;"></i>
        </div>
    </div>
</div>

<!-- Kendaraan Saya -->
<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-bicycle"></i> Kendaraan Saya</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
            <i class="bi bi-plus-circle"></i> Tambah Kendaraan
        </button>
    </div>
    <div class="card-body">
        @if($vehicles->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
            <p class="mt-3">Belum ada kendaraan terdaftar</p>
        </div>
        @else
        <div class="row">
            @foreach($vehicles as $vehicle)
            <div class="col-md-6 mb-3">
                <div class="card border">
                    <div class="card-body">
                        <h5 class="card-title">{{ $vehicle->brand }} {{ $vehicle->model }}</h5>
                        <p class="mb-1"><strong>Plat Nomor:</strong> {{ $vehicle->plate_number }}</p>
                        <p class="mb-0 text-muted small">
                            <i class="bi bi-calendar"></i> 
                            Servis Terakhir: {{ $vehicle->last_service_date ? $vehicle->last_service_date->format('d M Y') : 'Belum pernah' }}
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<!-- Booking Mendatang -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-calendar-event"></i> Booking Mendatang</h5>
    </div>
    <div class="card-body">
        @if($upcomingBookings->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
            <p class="mt-3">Belum ada booking aktif</p>
            <a href="{{ route('customer.booking') }}" class="btn btn-primary mt-2">
                <i class="bi bi-calendar-plus"></i> Buat Booking Sekarang
            </a>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kendaraan</th>
                        <th>Jenis Servis</th>
                        <th>Mekanik</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($upcomingBookings as $booking)
                    <tr>
                        <td>{{ $booking->booking_date->format('d M Y') }}</td>
                        <td>{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</td>
                        <td>{{ $booking->service_type }}</td>
                        <td>{{ $booking->mechanic ? $booking->mechanic->name : 'Belum ditugaskan' }}</td>
                        <td>
                            @if($booking->status == 'pending')
                            <span class="badge bg-warning">Pending</span>
                            @elseif($booking->status == 'in_progress')
                            <span class="badge bg-info">Dikerjakan</span>
                            @else
                            <span class="badge bg-success">Selesai</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Kendaraan -->
<div class="modal fade" id="addVehicleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kendaraan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('customer.vehicle.add') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Merek</label>
                        <input type="text" name="brand" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Model</label>
                        <input type="text" name="model" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Plat Nomor</label>
                        <input type="text" name="plate_number" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection