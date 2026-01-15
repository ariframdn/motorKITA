@extends('layouts.app')

@section('title', 'Manajemen Pembayaran')
@section('page-title', 'Manajemen Pembayaran')

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
<a href="{{ route('admin.payments') }}" class="nav-link active">
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

<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-credit-card"></i> Semua Pembayaran</h5>
    </div>

    <div class="card-body">
        @if($payments->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1"></i>
                <p class="mt-3">Belum ada pembayaran</p>
            </div>
        @else

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Pelanggan</th>
                        <th>Metode</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td>#{{ $payment->booking_id }}</td>
                        <td>{{ $payment->booking->customer->name }}</td>
                        <td>{{ ucfirst($payment->payment_method) }}</td>
                        <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td>
                            @if($payment->status === 'approved')
                                <span class="badge bg-success">Disetujui</span>
                            @elseif($payment->status === 'rejected')
                                <span class="badge bg-danger">Ditolak</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td>
                            @if($payment->payment_proof)
                                <a href="{{ asset('storage/payments/' . $payment->payment_proof) }}"
                                   target="_blank" class="btn btn-sm btn-info">
                                    <i class="bi bi-image"></i> Lihat
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($payment->status === 'pending')
                                <button
                                    type="button"
                                    class="btn btn-sm btn-success btn-approve"
                                    data-action="{{ route('admin.payments.approve', $payment->id) }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#approveModal">
                                    <i class="bi bi-check"></i> Setujui
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-danger btn-reject"
                                    data-action="{{ route('admin.payments.reject', $payment->id) }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                                    <i class="bi bi-x"></i> Tolak
                                </button>
                            @else
                                <small class="text-muted">
                                    @if($payment->approver)
                                        Oleh: {{ $payment->approver->name }}
                                    @endif
                                </small>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $payments->links() }}
        </div>

        @endif
    </div>
</div>

<!-- ===================== APPROVE MODAL ===================== -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Setujui Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>Yakin ingin menyetujui pembayaran ini?</p>
                <div class="mb-3">
                    <label class="form-label">Catatan (Opsional)</label>
                    <textarea name="admin_notes" class="form-control" rows="3"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Setujui</button>
            </div>
        </form>
    </div>
</div>

<!-- ===================== REJECT MODAL ===================== -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tolak Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>Yakin ingin menolak pembayaran ini?</p>
                <div class="mb-3">
                    <label class="form-label">
                        Alasan Penolakan <span class="text-danger">*</span>
                    </label>
                    <textarea name="admin_notes" class="form-control" rows="3" required></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Tolak</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.addEventListener('click', function (e) {

        const approveBtn = e.target.closest('.btn-approve');
        const rejectBtn  = e.target.closest('.btn-reject');

        if (approveBtn) {
            document.querySelector('#approveModal form').action =
                approveBtn.dataset.action;
        }

        if (rejectBtn) {
            document.querySelector('#rejectModal form').action =
                rejectBtn.dataset.action;
        }

    });

});
</script>
@endsection
