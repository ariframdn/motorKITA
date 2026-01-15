@extends('layouts.app')

@section('title', 'Laporan Keuangan')
@section('page-title', 'Laporan Keuangan')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="nav-link">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="{{ route('admin.financial') }}" class="nav-link active">
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
    <a href="{{ route('admin.service-prices') }}" class="nav-link">
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
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Filter Date Range -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-calendar-range"></i> Filter Periode</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.financial') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required>
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

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="text-white-50 mb-2">Total Pemasukan</h6>
                <h2 class="fw-bold">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h2>
                <small>Dari service selesai</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="text-white-50 mb-2">Total Pengeluaran</h6>
                <h2 class="fw-bold">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</h2>
                <small>Gaji karyawan</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6 class="text-white-50 mb-2">Total HPP</h6>
                <h2 class="fw-bold">Rp {{ number_format($totalHpp, 0, ',', '.') }}</h2>
                <small>Cost of goods sold</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="text-white-50 mb-2">Profit Bersih</h6>
                <h2 class="fw-bold">Rp {{ number_format($netProfit, 0, ',', '.') }}</h2>
                <small>Keuntungan bersih</small>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Grafik Pemasukan vs Pengeluaran</h5>
            </div>
            <div class="card-body">
                <canvas id="financialChart" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Income Details -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-arrow-down-circle text-success"></i> Detail Pemasukan</h5>
            </div>
            <div class="card-body">
                @if($incomeDetails->count() > 0)
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($incomeDetails as $income)
                            <tr>
                                <td>{{ $income->updated_at->format('d M Y') }}</td>
                                <td>{{ $income->customer->name }}</td>
                                <td>{{ $income->service_type }}</td>
                                <td><strong class="text-success">Rp {{ number_format($income->final_cost ?? $income->cost ?? 0, 0, ',', '.') }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-2">
                    {{ $incomeDetails->links() }}
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                    <p class="mt-2">Tidak ada data pemasukan</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-arrow-up-circle text-danger"></i> Detail Pengeluaran</h5>
            </div>
            <div class="card-body">
                @if($expenseDetails->count() > 0)
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Mekanik</th>
                                <th>Hari</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expenseDetails as $expense)
                            <tr>
                                <td>{{ $expense->payment_date->format('d M Y') }}</td>
                                <td>{{ $expense->mechanic->name }}</td>
                                <td>{{ $expense->attendance_days }} hari</td>
                                <td><strong class="text-danger">Rp {{ number_format($expense->total_amount, 0, ',', '.') }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-2">
                    {{ $expenseDetails->links() }}
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                    <p class="mt-2">Tidak ada data pengeluaran</p>
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
const ctx = document.getElementById('financialChart').getContext('2d');
const monthlyIncome = @json($monthlyIncome);
const monthlyExpenses = @json($monthlyExpenses);

// Combine and sort months
const allMonths = new Set();
monthlyIncome.forEach(item => allMonths.add(item.month));
monthlyExpenses.forEach(item => allMonths.add(item.month));
const sortedMonths = Array.from(allMonths).sort();

const incomeMap = {};
monthlyIncome.forEach(item => incomeMap[item.month] = parseFloat(item.income));

const expenseMap = {};
monthlyExpenses.forEach(item => expenseMap[item.month] = parseFloat(item.expenses));

const incomeData = sortedMonths.map(month => incomeMap[month] || 0);
const expenseData = sortedMonths.map(month => expenseMap[month] || 0);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: sortedMonths,
        datasets: [
            {
                label: 'Pemasukan',
                data: incomeData,
                borderColor: 'rgb(40, 167, 69)',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4
            },
            {
                label: 'Pengeluaran',
                data: expenseData,
                borderColor: 'rgb(220, 53, 69)',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.4
            }
        ]
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
                        return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});
</script>
@endsection
