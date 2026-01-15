@extends('layouts.app')

@section('title', 'Review')
@section('page-title', 'Review dari Customer')

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
    <a href="{{ route('mechanic.salaries') }}" class="nav-link">
        <i class="bi bi-cash-stack"></i> Gaji
    </a>
    <a href="{{ route('mechanic.reviews') }}" class="nav-link active">
        <i class="bi bi-star"></i> Review
    </a>
    <a href="{{ route('profile.edit') }}" class="nav-link">
        <i class="bi bi-person"></i> Profil
    </a>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h6 class="text-white-50 mb-2">Rating Rata-rata</h6>
                <h1 class="fw-bold">{{ number_format($avgRating, 1) }}</h1>
                <div>
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star{{ $i <= round($avgRating) ? '-fill' : '' }}"></i>
                    @endfor
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h6 class="text-white-50 mb-2">Total Review</h6>
                <h1 class="fw-bold">{{ $totalReviews }}</h1>
                <small>Review</small>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-star"></i> Semua Review</h5>
    </div>
    <div class="card-body">
        @if($reviews->count() > 0)
        <div class="list-group">
            @foreach($reviews as $review)
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="mb-0 me-3">{{ $review->customer->name }}</h6>
                            <div>
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= ($review->rating_mechanic ?? 0) ? '-fill text-warning' : '' }}"></i>
                                @endfor
                                <span class="ms-2 text-muted">{{ $review->rating_mechanic ?? 0 }}/5</span>
                            </div>
                            <small class="text-muted ms-3">{{ $review->created_at->diffForHumans() }}</small>
                        </div>
                        
                        @if($review->comment)
                        <p class="mb-2">{{ $review->comment }}</p>
                        @endif

                        @if($review->photo)
                        <div class="mt-2">
                            <img src="{{ asset('storage/reviews/' . $review->photo) }}" 
                                 alt="Review Photo" 
                                 class="img-thumbnail" 
                                 style="max-width: 200px;">
                        </div>
                        @endif

                        <small class="text-muted">
                            Booking: {{ $review->booking->service_type }} - {{ $review->booking->booking_date->format('d M Y') }}
                        </small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-3">
            {{ $reviews->links() }}
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-star" style="font-size: 3rem;"></i>
            <p class="mt-3">Belum ada review</p>
        </div>
        @endif
    </div>
</div>
@endsection
