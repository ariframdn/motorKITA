@extends('layouts.app')

@section('title', 'Manajemen Harga Service')
@section('page-title', 'Manajemen Harga Service')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="nav-link">
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
    <a href="{{ route('admin.service-prices') }}" class="nav-link active">
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
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-tags"></i> Daftar Harga Service</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
            <i class="bi bi-plus-circle"></i> Tambah Service
        </button>
    </div>

    <div class="card-body">
        @if($services->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1"></i>
                <p class="mt-3">Belum ada service</p>
            </div>
        @else

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Service</th>
                        <th>Deskripsi</th>
                        <th>Harga</th>
                        <th>Total HPP</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $service)
                    <tr>
                        <td><strong>{{ $service->service_name }}</strong></td>
                        <td>{{ $service->description ?? '-' }}</td>
                        <td>Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                        <td>
                            @php $totalHpp = $service->total_hpp ?? 0; @endphp
                            <strong>Rp {{ number_format($totalHpp, 0, ',', '.') }}</strong>
                        </td>
                        <td>
                            @if($service->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.service-hpp.index', $service->id) }}" 
                                   class="btn btn-sm btn-info" 
                                   title="Kelola HPP">
                                    <i class="bi bi-list-ul"></i> HPP
                                </a>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-warning btn-edit"
                                    data-action="{{ route('admin.service-prices.update', $service->id) }}"
                                    data-name="{{ $service->service_name }}"
                                    data-description="{{ $service->description }}"
                                    data-price="{{ $service->price }}"
                                    data-active="{{ $service->is_active }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editServiceModal">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <form action="{{ route('admin.service-prices.delete', $service->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus service ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $services->links() }}
        </div>

        @endif
    </div>
</div>

<!-- ===================== ADD MODAL ===================== -->
<div class="modal fade" id="addServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.service-prices.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Service</label>
                    <input type="text" name="service_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Harga</label>
                    <input type="number" name="price" class="form-control" min="0" required>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                    <label class="form-check-label">Aktif</label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- ===================== EDIT MODAL ===================== -->
<div class="modal fade" id="editServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="" class="modal-content">
            @csrf
            @method('PATCH')

            <div class="modal-header">
                <h5 class="modal-title">Edit Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Service</label>
                    <input type="text" name="service_name" id="editName" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" id="editDescription" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Harga</label>
                    <input type="number" name="price" id="editPrice" class="form-control" min="0" required>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" id="editActive" value="1">
                    <label class="form-check-label">Aktif</label>
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
        const btn = e.target.closest('.btn-edit');
        if (!btn) return;

        const modal = document.getElementById('editServiceModal');
        const form  = modal.querySelector('form');

        form.action = btn.dataset.action;
        modal.querySelector('#editName').value        = btn.dataset.name;
        modal.querySelector('#editDescription').value = btn.dataset.description ?? '';
        modal.querySelector('#editPrice').value       = btn.dataset.price;
        modal.querySelector('#editActive').checked    = btn.dataset.active == 1;
    });

});
</script>
@endsection
