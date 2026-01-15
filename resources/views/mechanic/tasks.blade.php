@extends('layouts.app')

@section('title', 'Daftar Tugas')
@section('page-title', 'Daftar Tugas')

@section('sidebar')
<a href="{{ route('mechanic.dashboard') }}" class="nav-link">
    <i class="bi bi-speedometer2"></i> Dashboard
</a>
<a href="{{ route('mechanic.tasks') }}" class="nav-link active">
    <i class="bi bi-list-task"></i> Daftar Tugas
</a>
<a href="{{ route('profile.edit') }}" class="nav-link">
    <i class="bi bi-person"></i> Profil
</a>
@endsection

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-list-task"></i> Semua Tugas Saya</h5>
    </div>

    <div class="card-body">
        @if($tasks->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1"></i>
                <p class="mt-3">Belum ada tugas</p>
            </div>
        @else

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Kendaraan</th>
                        <th>Jenis Servis</th>
                        <th>Status</th>
                        <th>Biaya</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                    <tr>
                        <td>{{ $task->booking_date->format('d M Y') }}</td>
                        <td>{{ $task->customer->name }}</td>
                        <td>
                            {{ $task->vehicle->brand }} {{ $task->vehicle->model }}<br>
                            <small class="text-muted">{{ $task->vehicle->plate_number }}</small>
                        </td>
                        <td>{{ $task->service_type }}</td>
                        <td>
                            @if($task->status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($task->status === 'in_progress')
                                <span class="badge bg-info">Dikerjakan</span>
                            @else
                                <span class="badge bg-success">Selesai</span>
                            @endif
                        </td>
                        <td>Rp {{ number_format($task->cost, 0, ',', '.') }}</td>
                        <td>
                            <button
                                type="button"
                                class="btn btn-sm btn-primary btn-update"
                                data-action="{{ route('mechanic.tasks.update', $task->id) }}"
                                data-status="{{ $task->status }}"
                                data-notes="{{ $task->notes }}"
                                data-cost="{{ $task->cost }}"
                                data-bs-toggle="modal"
                                data-bs-target="#updateTaskModal">
                                <i class="bi bi-pencil"></i> Update
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $tasks->links() }}
        </div>

        @endif
    </div>
</div>

<!-- ===================== MODAL GLOBAL ===================== -->
<div class="modal fade" id="updateTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="" class="modal-content">
            @csrf
            @method('PATCH')

            <div class="modal-header">
                <h5 class="modal-title">Update Status Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" id="taskStatus" class="form-select" required>
                        <option value="pending">Pending</option>
                        <option value="in_progress">Dikerjakan</option>
                        <option value="done">Selesai</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" id="taskNotes" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Biaya (Rp)</label>
                    <input type="number" name="cost" id="taskCost" class="form-control" min="0">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-update');
        if (!btn) return;

        const modal = document.getElementById('updateTaskModal');
        const form  = modal.querySelector('form');

        // KUNCI UTAMA
        form.action = btn.dataset.action;

        modal.querySelector('#taskStatus').value = btn.dataset.status;
        modal.querySelector('#taskNotes').value  = btn.dataset.notes ?? '';
        modal.querySelector('#taskCost').value   = btn.dataset.cost ?? 0;
    });

});
</script>
@endsection
