@extends('layouts.app')

@section('title', 'Absensi')
@section('page-title', 'Data Absensi Mekanik')

@include('admin.sidebar')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.attendances') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Tanggal</label>
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
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
        <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Daftar Absensi</h5>
    </div>
    <div class="card-body">
        @if($attendances->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Mekanik</th>
                        <th>Kode Absen</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Durasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->date->format('d M Y') }}</td>
                        <td>{{ $attendance->mechanic->name }}</td>
                        <td><code>{{ $attendance->attendanceCode->code }}</code></td>
                        <td>
                            @if($attendance->check_in_time)
                                {{ $attendance->check_in_time }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($attendance->check_out_time)
                                {{ $attendance->check_out_time }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($attendance->check_in_time && $attendance->check_out_time)
                                {{ $attendance->work_hours }} jam
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $attendances->links() }}
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
            <p class="mt-3">Tidak ada data absensi</p>
        </div>
        @endif
    </div>
</div>
@endsection
