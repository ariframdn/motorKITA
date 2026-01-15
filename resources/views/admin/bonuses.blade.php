@extends('layouts.app')

@section('title', 'Bonus')
@section('page-title', 'Kelola Bonus')

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
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBonusModal">
            <i class="bi bi-plus-circle"></i> Tambah Bonus
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-gift"></i> Daftar Bonus</h5>
    </div>
    <div class="card-body">
        @if($bonuses->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Tipe</th>
                        <th>Min. Service</th>
                        <th>Jenis Bonus</th>
                        <th>Nilai</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bonuses as $bonus)
                    <tr>
                        <td>
                            <strong>{{ $bonus->name }}</strong><br>
                            <small class="text-muted">{{ $bonus->description }}</small>
                        </td>
                        <td>
                            <span class="badge bg-{{ $bonus->type == 'performance' ? 'primary' : ($bonus->type == 'holiday' ? 'warning' : 'info') }}">
                                {{ $bonus->type == 'performance' ? 'Performance' : ($bonus->type == 'holiday' ? 'Holiday' : 'Custom') }}
                            </span>
                        </td>
                        <td>
                            @if($bonus->min_services)
                                {{ $bonus->min_services }} service/hari
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $bonus->bonus_type == 'fixed' ? 'success' : 'info' }}">
                                {{ $bonus->bonus_type == 'fixed' ? 'Fixed' : 'Percentage' }}
                            </span>
                        </td>
                        <td>
                            @if($bonus->bonus_type == 'fixed')
                                Rp {{ number_format($bonus->bonus_amount, 0, ',', '.') }}
                            @else
                                {{ $bonus->bonus_amount }}%
                            @endif
                        </td>
                        <td>
                            @if($bonus->effective_date || $bonus->expiry_date)
                                <small>
                                    @if($bonus->effective_date)
                                        {{ \Carbon\Carbon::parse($bonus->effective_date)->format('d M Y') }}
                                    @endif
                                    @if($bonus->expiry_date)
                                        <br>s/d {{ \Carbon\Carbon::parse($bonus->expiry_date)->format('d M Y') }}
                                    @endif
                                </small>
                            @else
                                <span class="text-muted">Selamanya</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $bonus->is_active ? 'success' : 'secondary' }}">
                                {{ $bonus->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editBonusModal{{ $bonus->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('admin.bonuses.delete', $bonus->id) }}" 
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
                    <div class="modal fade" id="editBonusModal{{ $bonus->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Bonus</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.bonuses.update', $bonus->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-body">
                                        @include('admin.partials.bonus-form', ['bonus' => $bonus])
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
            </table>
        </div>
        <div class="mt-3">
            {{ $bonuses->links() }}
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
            <p class="mt-3">Belum ada bonus</p>
        </div>
        @endif
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addBonusModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Bonus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.bonuses.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @include('admin.partials.bonus-form')
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
