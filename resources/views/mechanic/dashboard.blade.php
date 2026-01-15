@extends('layouts.app')

@section('title', 'Dashboard Mekanik')
@section('page-title', 'Dashboard Mekanik')

@section('sidebar')
    <a href="{{ route('mechanic.dashboard') }}" class="nav-link active">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="{{ route('mechanic.tasks') }}" class="nav-link">
        <i class="bi bi-list-task"></i> Daftar Tugas
    </a>
    <a href="{{ route('mechanic.attendance') }}" class="nav-link">
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

<!-- Statistik Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <h6 class="text-white-50 mb-2">Pending</h6>
            <h2 class="fw-bold">{{ $stats['pending'] }}</h2>
            <i class="bi bi-hourglass-split" style="font-size: 3rem; opacity: 0.3; position: absolute; right: 20px; top: 20px;"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white" style="padding: 25px; border-radius: 12px;">
            <h6 class="text-white-50 mb-2">Sedang Dikerjakan</h6>
            <h2 class="fw-bold">{{ $stats['in_progress'] }}</h2>
            <i class="bi bi-tools" style="font-size: 3rem; opacity: 0.3; position: absolute; right: 20px; top: 20px;"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white" style="padding: 25px; border-radius: 12px;">
            <h6 class="text-white-50 mb-2">Selesai Hari Ini</h6>
            <h2 class="fw-bold">{{ $stats['done_today'] }}</h2>
            <i class="bi bi-check-circle" style="font-size: 3rem; opacity: 0.3; position: absolute; right: 20px; top: 20px;"></i>
        </div>
    </div>
</div>

<!-- Tugas Hari Ini -->
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-calendar-event"></i> Tugas Hari Ini</h5>
        <a href="{{ route('mechanic.tasks') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-arrow-right"></i> Lihat Semua
        </a>
    </div>
    <div class="card-body">
        @if($todayTasks->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
            <p class="mt-3">Tidak ada tugas untuk hari ini</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Pelanggan</th>
                        <th>Kendaraan</th>
                        <th>Jenis Servis</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($todayTasks as $task)
                    <tr>
                        <td>{{ $task->customer->name }}</td>
                        <td>{{ $task->vehicle->brand }} {{ $task->vehicle->model }}<br>
                            <small class="text-muted">{{ $task->vehicle->plate_number }}</small>
                        </td>
                        <td>{{ $task->service_type }}</td>
                        <td>
                            @if($task->status == 'pending')
                            <span class="badge bg-warning">Pending</span>
                            @elseif($task->status == 'in_progress')
                            <span class="badge bg-info">Dikerjakan</span>
                            @else
                            <span class="badge bg-success">Selesai</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                    data-bs-target="#updateTaskModal{{ $task->id }}">
                                <i class="bi bi-pencil"></i> Update
                            </button>
                        </td>
                    </tr>

                    <!-- Modal Update Task -->
                    <div class="modal fade" id="updateTaskModal{{ $task->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Update Status Tugas</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('mechanic.tasks.update', $task->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select" required>
                                                <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>Dikerjakan</option>
                                                <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Selesai</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Catatan</label>
                                            <textarea name="notes" class="form-control" rows="3" 
                                                      placeholder="Masukkan catatan servis...">{{ $task->notes }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Biaya (Rp)</label>
                                            <input type="number" name="cost" class="form-control" 
                                                   value="{{ $task->cost }}" min="0" step="0.01">
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
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection

