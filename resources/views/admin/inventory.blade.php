@extends('layouts.app')

@section('title', 'Manajemen Inventori')
@section('page-title', 'Manajemen Inventori')

@section('sidebar')
<a href="{{ route('admin.dashboard') }}" class="nav-link">
    <i class="bi bi-speedometer2"></i> Dashboard
</a>
<a href="{{ route('admin.inventory') }}" class="nav-link active">
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
        <h5 class="mb-0"><i class="bi bi-box-seam"></i> Daftar Sparepart</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInventoryModal">
            <i class="bi bi-plus-circle"></i> Tambah Item
        </button>
    </div>

    <div class="card-body">
        @if($items->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1"></i>
                <p class="mt-3">Belum ada item inventori</p>
            </div>
        @else

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Sparepart</th>
                        <th>Stok</th>
                        <th>Harga</th>
                        <th>Stok Minimum</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td><strong>{{ $item->part_name }}</strong></td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td>{{ $item->low_stock_level }}</td>
                        <td>
                            @if($item->isLowStock())
                                <span class="badge bg-danger">Stok Rendah</span>
                            @else
                                <span class="badge bg-success">Aman</span>
                            @endif
                        </td>
                        <td>
                            <button
                                type="button"
                                class="btn btn-sm btn-warning btn-edit"
                                data-action="{{ route('admin.inventory.update', $item->id) }}"
                                data-name="{{ $item->part_name }}"
                                data-qty="{{ $item->quantity }}"
                                data-price="{{ $item->price }}"
                                data-low="{{ $item->low_stock_level }}"
                                data-bs-toggle="modal"
                                data-bs-target="#editInventoryModal">
                                <i class="bi bi-pencil"></i> Edit
                            </button>

                            <form action="{{ route('admin.inventory.delete', $item->id) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin ingin menghapus item ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $items->links() }}
        </div>

        @endif
    </div>
</div>

<!-- ===================== ADD MODAL ===================== -->
<div class="modal fade" id="addInventoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.inventory.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Item Inventori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Sparepart</label>
                    <input type="text" name="part_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number" name="quantity" class="form-control" min="0" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Harga</label>
                    <input type="number" name="price" class="form-control" min="0" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Stok Minimum</label>
                    <input type="number" name="low_stock_level" class="form-control" min="0" required>
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
<div class="modal fade" id="editInventoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="" class="modal-content">
            @csrf
            @method('PATCH')

            <div class="modal-header">
                <h5 class="modal-title">Edit Item Inventori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Sparepart</label>
                    <input type="text" name="part_name" id="editName" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number" name="quantity" id="editQty" class="form-control" min="0" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Harga</label>
                    <input type="number" name="price" id="editPrice" class="form-control" min="0" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Stok Minimum</label>
                    <input type="number" name="low_stock_level" id="editLow" class="form-control" min="0" required>
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

        const modal = document.getElementById('editInventoryModal');
        const form  = modal.querySelector('form');

        form.action = btn.dataset.action;
        modal.querySelector('#editName').value  = btn.dataset.name;
        modal.querySelector('#editQty').value   = btn.dataset.qty;
        modal.querySelector('#editPrice').value = btn.dataset.price;
        modal.querySelector('#editLow').value   = btn.dataset.low;
    });

});
</script>
@endsection
