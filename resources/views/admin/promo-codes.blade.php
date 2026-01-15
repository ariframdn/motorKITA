@extends('layouts.app')

@section('title', 'Kode Promo')
@section('page-title', 'Kelola Kode Promo')

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
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPromoModal">
            <i class="bi bi-plus-circle"></i> Tambah Kode Promo
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-ticket-perforated"></i> Daftar Kode Promo</h5>
    </div>
    <div class="card-body">
        @if($promoCodes->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Tipe Diskon</th>
                        <th>Nilai Diskon</th>
                        <th>Min. Belanja</th>
                        <th>Periode</th>
                        <th>Penggunaan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($promoCodes as $promo)
                    <tr>
                        <td><code>{{ $promo->code }}</code></td>
                        <td>{{ $promo->name }}</td>
                        <td>
                            <span class="badge bg-{{ $promo->discount_type == 'percentage' ? 'info' : 'secondary' }}">
                                {{ $promo->discount_type == 'percentage' ? 'Persentase' : 'Nominal' }}
                            </span>
                        </td>
                        <td>
                            @if($promo->discount_type == 'percentage')
                                {{ $promo->discount_value }}%
                            @else
                                Rp {{ number_format($promo->discount_value, 0, ',', '.') }}
                            @endif
                        </td>
                        <td>
                            @if($promo->min_purchase)
                                Rp {{ number_format($promo->min_purchase, 0, ',', '.') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <small>
                                {{ \Carbon\Carbon::parse($promo->start_date)->format('d M Y') }}<br>
                                s/d {{ \Carbon\Carbon::parse($promo->end_date)->format('d M Y') }}
                            </small>
                        </td>
                        <td>
                            {{ $promo->used_count }} / {{ $promo->usage_limit ?? '∞' }}
                        </td>
                        <td>
                            <span class="badge bg-{{ $promo->is_active ? 'success' : 'secondary' }}">
                                {{ $promo->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editPromoModal{{ $promo->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('admin.promo-codes.delete', $promo->id) }}" 
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
                    <div class="modal fade" id="editPromoModal{{ $promo->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Kode Promo</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.promo-codes.update', $promo->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-body">
                                        @include('admin.partials.promo-code-form', ['promo' => $promo, 'edit' => true])
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
            {{ $promoCodes->links() }}
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
            <p class="mt-3">Belum ada kode promo</p>
        </div>
        @endif
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addPromoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kode Promo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.promo-codes.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @include('admin.partials.promo-code-form')
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
