@extends('layouts.app')

@section('title', 'Gaji')
@section('page-title', 'Riwayat Gaji')

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
    <a href="{{ route('mechanic.earnings') }}" class="nav-link">
        <i class="bi bi-wallet2"></i> Penghasilan
    </a>
    <a href="{{ route('mechanic.salaries') }}" class="nav-link active">
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
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-cash-stack"></i> Riwayat Gaji</h5>
    </div>
    <div class="card-body">
        @if($salaries->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Hari Kerja</th>
                        <th>Gaji Harian</th>
                        <th>Bonus</th>
                        <th>Total</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Tanggal Bayar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salaries as $salary)
                    <tr>
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
                            @if($salary->payment_date)
                                {{ \Carbon\Carbon::parse($salary->payment_date)->format('d M Y') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('mechanic.salaries.show', $salary->id) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i> Detail
                            </a>
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
            <p class="mt-3">Belum ada riwayat gaji</p>
        </div>
        @endif
    </div>
</div>
@endsection
