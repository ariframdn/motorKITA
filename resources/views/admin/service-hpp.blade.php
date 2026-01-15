@extends('layouts.app')

@section('title', 'HPP Service')
@section('page-title', 'Harga Pokok Penjualan (HPP) - ' . $service->service_name)

@include('admin.sidebar')

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5>Informasi Service</h5>
                <p class="mb-0"><strong>Nama:</strong> {{ $service->service_name }}</p>
                <p class="mb-0"><strong>Harga Jual:</strong> Rp {{ number_format($service->price, 0, ',', '.') }}</p>
                <p class="mb-0"><strong>Total HPP:</strong> Rp {{ number_format($hppItems->sum('total_cost'), 0, ',', '.') }}</p>
                @php $totalHpp = $hppItems->sum('total_cost'); @endphp
                <p class="mb-0"><strong>Profit:</strong> Rp {{ number_format($service->price - $totalHpp, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHppModal">
            <i class="bi bi-plus-circle"></i> Tambah Item HPP
        </button>
        <a href="{{ route('admin.service-prices') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-list-ul"></i> Daftar Item HPP</h5>
    </div>
    <div class="card-body">
        @if($hppItems->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Item Inventori</th>
                        <th>Quantity</th>
                        <th>Harga Satuan</th>
                        <th>Total Cost</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hppItems as $item)
                    <tr>
                        <td>{{ $item->inventory->part_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td><strong>Rp {{ number_format($item->total_cost, 0, ',', '.') }}</strong></td>
                        <td>
                            <button class="btn btn-sm btn-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editHppModal{{ $item->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('admin.service-hpp.delete', $item->id) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirm('Yakin ingin menghapus?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editHppModal{{ $item->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Item HPP</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.service-hpp.update', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-body">
                                        <input type="hidden" name="service_price_id" value="{{ $service->id }}">
                                        <input type="hidden" name="inventory_id" value="{{ $item->inventory_id }}">
                                        <div class="mb-3">
                                            <label class="form-label">Item: {{ $item->inventory->part_name }}</label>
                                            <p class="text-muted">Stok tersedia: {{ $item->inventory->quantity }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                            <input type="number" name="quantity" class="form-control" 
                                                   value="{{ $item->quantity }}" required min="1">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Harga Satuan (Rp) <span class="text-danger">*</span></label>
                                            <input type="number" name="unit_price" class="form-control" 
                                                   value="{{ $item->unit_price }}" required step="0.01" min="0">
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
                <tfoot>
                    <tr class="table-info">
                        <th colspan="3" class="text-end">Total HPP:</th>
                        <th>Rp {{ number_format($hppItems->sum('total_cost'), 0, ',', '.') }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
            <p class="mt-3">Belum ada item HPP. Tambahkan item untuk menghitung HPP service ini.</p>
        </div>
        @endif
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addHppModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Item HPP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.service-hpp.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="service_price_id" value="{{ $service->id }}">
                    <div class="mb-3">
                        <label class="form-label">Item Inventori <span class="text-danger">*</span></label>
                        <select name="inventory_id" id="inventory_select" class="form-select" required onchange="updatePrice()">
                            <option value="">-- Pilih Item --</option>
                            @foreach($inventories as $inventory)
                            <option value="{{ $inventory->id }}" 
                                    data-price="{{ $inventory->price }}" 
                                    data-stock="{{ $inventory->quantity }}">
                                {{ $inventory->part_name }} (Stok: {{ $inventory->quantity }}, Harga: Rp {{ number_format($inventory->price, 0, ',', '.') }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="quantity" class="form-control" 
                               required min="1" onchange="calculateTotal()" value="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Satuan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="unit_price" id="unit_price" class="form-control" 
                               required step="0.01" min="0" onchange="calculateTotal()">
                    </div>
                    <div class="alert alert-info">
                        <strong>Total Cost:</strong> <span id="total_cost">Rp 0</span>
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

@section('scripts')
<script>
function updatePrice() {
    const select = document.getElementById('inventory_select');
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption.value) {
        const price = selectedOption.getAttribute('data-price');
        document.getElementById('unit_price').value = price;
        calculateTotal();
    }
}

function calculateTotal() {
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const unitPrice = parseFloat(document.getElementById('unit_price').value) || 0;
    const total = quantity * unitPrice;
    document.getElementById('total_cost').textContent = 'Rp ' + total.toLocaleString('id-ID');
}
</script>
@endsection
