@extends('layouts.app')

@section('title', 'Booking Servis')
@section('page-title', 'Booking Servis Baru')

@section('sidebar')
    <a href="{{ route('customer.dashboard') }}" class="nav-link">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="{{ route('customer.booking') }}" class="nav-link active">
        <i class="bi bi-calendar-plus"></i> Booking Servis
    </a>
    <a href="{{ route('customer.history') }}" class="nav-link">
        <i class="bi bi-clock-history"></i> Riwayat Servis
    </a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-calendar-plus"></i> Form Booking Servis</h5>
            </div>
            <div class="card-body p-4">
                @if($vehicles->isEmpty())
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> 
                    Anda belum memiliki kendaraan terdaftar. Silakan tambahkan kendaraan terlebih dahulu.
                </div>
                <a href="{{ route('customer.dashboard') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Kendaraan
                </a>
                @else
                <form action="{{ route('customer.booking.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label">Pilih Kendaraan</label>
                        <select name="vehicle_id" class="form-select" required>
                            <option value="">-- Pilih Kendaraan --</option>
                            @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">
                                {{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->plate_number }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Jenis Servis</label>
                        <select name="service_type" id="service_type" class="form-select" required>
                            <option value="">-- Pilih Jenis Servis --</option>
                            @foreach($services as $service)
                            <option value="{{ $service->service_name }}" data-price="{{ $service->price }}">
                                {{ $service->service_name }} - Rp {{ number_format($service->price, 0, ',', '.') }}
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Harga akan otomatis terisi sesuai pilihan</small>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Kode Promo (Opsional)</label>
                        <div class="input-group">
                            <input type="text" name="promo_code" id="promo_code" class="form-control" 
                                   placeholder="Masukkan kode promo">
                            <button type="button" class="btn btn-outline-primary" onclick="validatePromoCode()">
                                <i class="bi bi-check-circle"></i> Validasi
                            </button>
                        </div>
                        <div id="promo_message" class="mt-2"></div>
                    </div>

                    <div class="mb-4">
                        <div class="alert alert-info">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>Estimasi Biaya:</strong> <span id="service_price">Rp 0</span>
                                    <div id="discount_info" class="mt-1" style="display: none;">
                                        <small class="text-success">
                                            Diskon: <span id="discount_amount">Rp 0</span>
                                        </small>
                                    </div>
                                </div>
                                <div>
                                    <strong>Total Setelah Diskon:</strong> 
                                    <span id="final_price" class="text-success">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Tanggal Booking</label>
                        <input type="date" name="booking_date" class="form-control" 
                               min="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea name="notes" class="form-control" rows="3" 
                                  placeholder="Deskripsikan keluhan atau kebutuhan khusus..."></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Informasi:</strong> Tim kami akan menghubungi Anda untuk konfirmasi booking dalam 1x24 jam.
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Buat Booking
                        </button>
                        <a href="{{ route('customer.dashboard') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
let originalPrice = 0;
let discountAmount = 0;
let promoCodeId = null;

document.getElementById('service_type').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    originalPrice = parseFloat(selectedOption.getAttribute('data-price')) || 0;
    updatePrices();
});

function validatePromoCode() {
    const promoCode = document.getElementById('promo_code').value;
    const messageDiv = document.getElementById('promo_message');
    
    if (!promoCode) {
        messageDiv.innerHTML = '<div class="alert alert-warning">Masukkan kode promo</div>';
        return;
    }

    if (originalPrice === 0) {
        messageDiv.innerHTML = '<div class="alert alert-warning">Pilih jenis servis terlebih dahulu</div>';
        return;
    }

    fetch('{{ route("customer.promo-codes.validate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            code: promoCode,
            amount: originalPrice
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.valid) {
            discountAmount = parseFloat(data.discount);
            promoCodeId = data.promo_code.id;
            messageDiv.innerHTML = '<div class="alert alert-success">Kode promo valid! Diskon: Rp ' + discountAmount.toLocaleString('id-ID') + '</div>';
            updatePrices();
        } else {
            discountAmount = 0;
            promoCodeId = null;
            messageDiv.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
            updatePrices();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        messageDiv.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan saat validasi kode promo</div>';
    });
}

function updatePrices() {
    document.getElementById('service_price').textContent = 'Rp ' + originalPrice.toLocaleString('id-ID');
    
    const finalPrice = originalPrice - discountAmount;
    document.getElementById('final_price').textContent = 'Rp ' + finalPrice.toLocaleString('id-ID');
    
    const discountInfo = document.getElementById('discount_info');
    if (discountAmount > 0) {
        discountInfo.style.display = 'block';
        document.getElementById('discount_amount').textContent = 'Rp ' + discountAmount.toLocaleString('id-ID');
    } else {
        discountInfo.style.display = 'none';
    }
}

// Auto-validate when promo code changes
document.getElementById('promo_code').addEventListener('blur', function() {
    if (this.value && originalPrice > 0) {
        validatePromoCode();
    }
});
</script>
@endsection