@extends('layouts.app')

@section('title', 'Riwayat Servis')
@section('page-title', 'Riwayat Servis')

@section('sidebar')
<a href="{{ route('customer.dashboard') }}" class="nav-link">
    <i class="bi bi-speedometer2"></i> Dashboard
</a>
<a href="{{ route('customer.booking') }}" class="nav-link">
    <i class="bi bi-calendar-plus"></i> Booking Servis
</a>
<a href="{{ route('customer.history') }}" class="nav-link active">
    <i class="bi bi-clock-history"></i> Riwayat Servis
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
        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Semua Riwayat Servis</h5>
    </div>

    <div class="card-body">

        @if($bookings->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-1"></i>
            <p class="mt-3">Belum ada riwayat servis</p>
            <a href="{{ route('customer.booking') }}" class="btn btn-primary">
                <i class="bi bi-calendar-plus"></i> Buat Booking
            </a>
        </div>
        @else

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kendaraan</th>
                        <th>Jenis Servis</th>
                        <th>Mekanik</th>
                        <th>Status</th>
                        <th>Biaya</th>
                        <th>Pembayaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr>
                        <td>{{ $booking->booking_date->format('d M Y') }}</td>
                        <td>
                            <strong>{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</strong><br>
                            <small class="text-muted">{{ $booking->vehicle->plate_number }}</small>
                        </td>
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
                        <td>
                            @if($booking->final_cost > 0)
                                <strong>Rp {{ number_format($booking->final_cost, 0, ',', '.') }}</strong>
                                @if($booking->discount_amount > 0)
                                    <br><small class="text-success">
                                        Diskon: Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}
                                    </small>
                                @endif
                            @elseif($booking->cost > 0)
                                <strong>Rp {{ number_format($booking->cost, 0, ',', '.') }}</strong>
                            @else
                                <span class="text-muted">-</span>
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
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                @if($booking->cost > 0 && $booking->payment_status != 'paid')
                                <button
                                    class="btn btn-sm btn-primary btn-pay"
                                    data-id="{{ $booking->id }}"
                                    data-cost="{{ $booking->final_cost > 0 ? number_format($booking->final_cost, 0, ',', '.') : number_format($booking->cost, 0, ',', '.') }}"
                                    data-action="{{ route('customer.payment.submit', $booking->id) }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#paymentModal">
                                    <i class="bi bi-credit-card"></i> Bayar
                                </button>
                                @endif
                                
                                @if($booking->status == 'done' && !$booking->review)
                                <button class="btn btn-sm btn-success" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#reviewModal{{ $booking->id }}">
                                    <i class="bi bi-star"></i> Review
                                </button>
                                @endif

                                @if($booking->mechanic && $booking->mechanic_id)
                                <a href="{{ route('customer.reviews.mechanic', $booking->mechanic_id) }}" 
                                   class="btn btn-sm btn-info">
                                    <i class="bi bi-person-check"></i> Lihat Mekanik
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @endif
    </div>
</div>

@foreach($bookings as $booking)
@if($booking->status == 'done' && !$booking->review)
<!-- Review Modal -->
<div class="modal fade" id="reviewModal{{ $booking->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Beri Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('customer.reviews.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                    
                    <div class="mb-3">
                        <label class="form-label">Tipe Review</label>
                        <select name="review_type" id="review_type{{ $booking->id }}" class="form-select" required onchange="toggleReviewFields({{ $booking->id }})">
                            <option value="workshop">Review Bengkel Saja</option>
                            <option value="mechanic">Review Mekanik Saja</option>
                            <option value="both" selected>Review Bengkel & Mekanik</option>
                        </select>
                    </div>

                    <div id="workshop_rating{{ $booking->id }}">
                        <label class="form-label">Rating Bengkel</label>
                        <div class="mb-2">
                            <select name="rating_workshop" class="form-select" id="rating_workshop{{ $booking->id }}">
                                <option value="">-- Pilih Rating --</option>
                                @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}">{{ $i }} Bintang</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div id="mechanic_rating{{ $booking->id }}">
                        <label class="form-label">Rating Mekanik</label>
                        <div class="mb-2">
                            <select name="rating_mechanic" class="form-select" id="rating_mechanic{{ $booking->id }}">
                                <option value="">-- Pilih Rating --</option>
                                @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}">{{ $i }} Bintang</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Komentar (Opsional)</label>
                        <textarea name="comment" class="form-control" rows="3" 
                                  placeholder="Bagikan pengalaman Anda..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Foto (Opsional)</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        <small class="text-muted">Format: JPEG, PNG, JPG (Max: 2MB)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Review</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

<!-- ===================== MODAL GLOBAL ===================== -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Pembayaran Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Total Pembayaran</label>
                    <input type="text" id="paymentCost" class="form-control" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Metode Pembayaran</label>
                    <select name="payment_method" id="paymentMethod" class="form-select" required>
                        <option value="">-- Pilih Metode --</option>
                        <option value="cash">Cash</option>
                        <option value="qris">QRIS</option>
                        <option value="transfer">Transfer Bank</option>
                    </select>
                </div>

                <div class="mb-3 d-none" id="proofField">
                    <label class="form-label">Bukti Transfer</label>
                    <input type="file" name="payment_proof" class="form-control" accept="image/*">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Kirim</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-pay')) {
        const btn = e.target.closest('.btn-pay');
        const modal = document.getElementById('paymentModal');
        modal.querySelector('form').action = btn.dataset.action;
        modal.querySelector('#paymentCost').value = 'Rp ' + btn.dataset.cost;
    }
});

document.getElementById('paymentMethod').addEventListener('change', function () {
    const proof = document.getElementById('proofField');
    const input = proof.querySelector('input');

    if (this.value === 'qris' || this.value === 'transfer') {
        proof.classList.remove('d-none');
        input.required = true;
    } else {
        proof.classList.add('d-none');
        input.required = false;
    }
});

function toggleReviewFields(bookingId) {
    const reviewType = document.getElementById('review_type' + bookingId).value;
    const workshopRating = document.getElementById('workshop_rating' + bookingId);
    const mechanicRating = document.getElementById('mechanic_rating' + bookingId);
    const ratingWorkshop = document.getElementById('rating_workshop' + bookingId);
    const ratingMechanic = document.getElementById('rating_mechanic' + bookingId);

    if (reviewType === 'workshop') {
        workshopRating.style.display = 'block';
        mechanicRating.style.display = 'none';
        ratingWorkshop.required = true;
        ratingMechanic.required = false;
    } else if (reviewType === 'mechanic') {
        workshopRating.style.display = 'none';
        mechanicRating.style.display = 'block';
        ratingWorkshop.required = false;
        ratingMechanic.required = true;
    } else {
        workshopRating.style.display = 'block';
        mechanicRating.style.display = 'block';
        ratingWorkshop.required = true;
        ratingMechanic.required = true;
    }
}
</script>
@endsection
