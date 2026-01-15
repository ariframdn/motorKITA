@extends('layouts.app')

@section('title', 'Kode Absen')
@section('page-title', 'Generate & Kelola Kode Absen')

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
        <form action="{{ route('admin.attendance-codes.generate') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-key"></i> Generate Kode Absen Hari Ini
            </button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-key"></i> Daftar Kode Absen</h5>
    </div>
    <div class="card-body">
        @if($codes->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kode</th>
                        <th>Status</th>
                        <th>Digunakan Pada</th>
                        <th>Dibuat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($codes as $code)
                    <tr>
                        <td>{{ $code->date->format('d M Y') }}</td>
                        <td>
                            <code style="font-size: 1.2rem; letter-spacing: 3px; font-weight: bold;">
                                {{ $code->code }}
                            </code>
                        </td>
                        <td>
                            <span class="badge bg-{{ $code->is_used ? 'success' : 'warning' }}">
                                {{ $code->is_used ? 'Digunakan' : 'Belum Digunakan' }}
                            </span>
                        </td>
                        <td>
                            @if($code->used_at)
                                {{ \Carbon\Carbon::parse($code->used_at)->format('d M Y H:i:s') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $code->creator->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $codes->links() }}
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
            <p class="mt-3">Belum ada kode absen. Generate kode untuk hari ini.</p>
        </div>
        @endif
    </div>
</div>
@endsection
