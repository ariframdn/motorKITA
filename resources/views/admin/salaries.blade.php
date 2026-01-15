@extends('layouts.app')

@section('title', 'Gaji Karyawan')
@section('page-title', 'Kelola Gaji Karyawan')

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
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSalaryModal">
            <i class="bi bi-plus-circle"></i> Buat Gaji Baru
        </button>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.salaries') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Mekanik</label>
                <select name="mechanic_id" class="form-select">
                    <option value="">Semua Mekanik</option>
                    @foreach($mechanics as $mechanic)
                    <option value="{{ $mechanic->id }}" {{ request('mechanic_id') == $mechanic->id ? 'selected' : '' }}>
                        {{ $mechanic->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Dibayar</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-cash-stack"></i> Daftar Gaji</h5>
    </div>
    <div class="card-body">
        @if($salaries->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Mekanik</th>
                        <th>Periode</th>
                        <th>Hari Kerja</th>
                        <th>Gaji Harian</th>
                        <th>Bonus</th>
                        <th>Total</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salaries as $salary)
                    <tr>
                        <td>{{ $salary->mechanic->name }}</td>
                        <td>
                            {{ $salary->period_start->format('d M Y') }}<br>
                            <small class="text-muted">s/d {{ $salary->period_end->format('d M Y') }}</small>
                        </td>
                        <td>{{ $salary->attendance_days }} hari</td>
                        <td>Rp {{ number_format($salary->daily_rate, 0, ',', '.') }}</td>
                        <td>
                            @if($salary->bonus_amount > 0)
                                <span class="badge bg-success">Rp {{ number_format($salary->bonus_amount, 0, ',', '.') }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td><strong>Rp {{ number_format($salary->total_amount, 0, ',', '.') }}</strong></td>
                        <td>
                            <span class="badge bg-{{ $salary->payment_method == 'transfer' ? 'info' : 'secondary' }}">
                                {{ $salary->payment_method == 'transfer' ? 'Transfer' : 'Cash' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $salary->status == 'paid' ? 'success' : ($salary->status == 'pending' ? 'warning' : 'danger') }}">
                                {{ $salary->status == 'paid' ? 'Dibayar' : ($salary->status == 'pending' ? 'Pending' : 'Dibatalkan') }}
                            </span>
                        </td>
                        <td>
                            @if($salary->status == 'pending')
                            <form action="{{ route('admin.salaries.mark-paid', $salary->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" 
                                        onclick="return confirm('Tandai sebagai dibayar?')">
                                    <i class="bi bi-check-circle"></i> Tandai Dibayar
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $salaries->links() }}
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
            <p class="mt-3">Belum ada data gaji</p>
        </div>
        @endif
    </div>
</div>

<!-- Add Salary Modal -->
<div class="modal fade" id="addSalaryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Gaji Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.salaries.create') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Mekanik <span class="text-danger">*</span></label>
                        <select name="mechanic_id" class="form-select" required>
                            <option value="">-- Pilih Mekanik --</option>
                            @foreach($mechanics as $mechanic)
                            <option value="{{ $mechanic->id }}">{{ $mechanic->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gaji Pokok <span class="text-danger">*</span></label>
                            <input type="number" name="base_salary" class="form-control" required 
                                   step="0.01" min="0" placeholder="5000000">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gaji Harian <span class="text-danger">*</span></label>
                            <input type="number" name="daily_rate" class="form-control" required 
                                   step="0.01" min="0" placeholder="200000">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Mulai Periode <span class="text-danger">*</span></label>
                            <input type="date" name="period_start" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Akhir Periode <span class="text-danger">*</span></label>
                            <input type="date" name="period_end" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bonus (Opsional)</label>
                        <input type="number" name="bonus_amount" class="form-control" 
                               step="0.01" min="0" placeholder="500000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-select" required onchange="toggleProofField()">
                            <option value="cash">Cash</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="proof_field">
                        <label class="form-label">Bukti Transfer <span class="text-danger">*</span></label>
                        <input type="file" name="payment_proof" class="form-control" accept="image/*">
                        <small class="text-muted">Wajib upload foto bukti transfer</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
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
function toggleProofField() {
    const method = document.getElementById('payment_method').value;
    const proofField = document.getElementById('proof_field');
    const proofInput = proofField.querySelector('input');
    
    if (method === 'transfer') {
        proofField.classList.remove('d-none');
        proofInput.required = true;
    } else {
        proofField.classList.add('d-none');
        proofInput.required = false;
    }
}
</script>
@endsection
