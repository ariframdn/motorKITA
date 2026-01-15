@extends('layouts.app')

@section('title', 'Penghasilan')
@section('page-title', 'Penghasilan & Statistik')

@section('sidebar')
    <a href="{{ route('mechanic.dashboard') }}" class="nav-link">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="{{ route('mechanic.tasks') }}" class="nav-link">
        <i class="bi bi-list-task"></i> Daftar Tugas
    </a>
    <a href="{{ route('mechanic.attendance') }}" class="nav-link">
        <i class="bi bi-calendar-check"></i> Absensi
    </a>
    <a href="{{ route('mechanic.earnings') }}" class="nav-link active">
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
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="text-white-50 mb-2">Total Hari Kerja</h6>
                <h2 class="fw-bold">{{ $totalDaysWorked }}</h2>
                <small>Hari</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="text-white-50 mb-2">Total Penghasilan</h6>
                <h2 class="fw-bold">Rp {{ number_format($totalEarnings, 0, ',', '.') }}</h2>
                <small>Keseluruhan</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="text-white-50 mb-2">Service Hari Ini</h6>
                <h2 class="fw-bold">{{ $servicesToday }}</h2>
                <small>Service</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6 class="text-white-50 mb-2">Bonus Potensial</h6>
                <h2 class="fw-bold">Rp {{ number_format($bonusAmount, 0, ',', '.') }}</h2>
                <small>
                    @if($servicesToday > 5 && $bonus)
                        <i class="bi bi-check-circle"></i> Memenuhi syarat
                    @else
                        Butuh {{ max(0, 6 - $servicesToday) }} service lagi
                    @endif
                </small>
            </div>
        </div>
    </div>
</div>

@if($bonus && $servicesToday > 5)
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-gift"></i> 
    <strong>Selamat!</strong> Anda mendapat bonus {{ $bonus->name }} karena menyelesaikan lebih dari 5 service hari ini.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-graph-up"></i> Penghasilan Bulanan</h5>
            </div>
            <div class="card-body">
                <canvas id="earningsChart" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-cash-stack"></i> Riwayat Gaji</h5>
                <a href="{{ route('mechanic.salaries') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if($recentSalaries->count() > 0)
                <div class="list-group">
                    @foreach($recentSalaries as $salary)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Periode {{ $salary->period_start->format('d M') }} - {{ $salary->period_end->format('d M Y') }}</h6>
                                <small class="text-muted">
                                    {{ $salary->attendance_days }} hari kerja
                                    @if($salary->bonus_amount > 0)
                                        + Bonus Rp {{ number_format($salary->bonus_amount, 0, ',', '.') }}
                                    @endif
                                </small>
                            </div>
                            <div class="text-end">
                                <strong>Rp {{ number_format($salary->total_amount, 0, ',', '.') }}</strong><br>
                                <span class="badge bg-{{ $salary->status == 'paid' ? 'success' : 'warning' }}">
                                    {{ $salary->status == 'paid' ? 'Dibayar' : 'Pending' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                    <p class="mt-2">Belum ada riwayat gaji</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('earningsChart').getContext('2d');
const monthlyEarnings = @json($monthlyEarnings);

const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
const earningsData = Array(12).fill(0);

monthlyEarnings.forEach(item => {
    earningsData[item.month - 1] = parseFloat(item.earnings);
});

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: months,
        datasets: [{
            label: 'Penghasilan (Rp)',
            data: earningsData,
            backgroundColor: 'rgba(37, 99, 235, 0.6)',
            borderColor: 'rgba(37, 99, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});
</script>
@endsection
