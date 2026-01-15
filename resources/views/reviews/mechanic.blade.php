@extends('layouts.app')

@section('title', 'Review Mekanik - ' . $mechanic->name)
@section('page-title', 'Review Mekanik')

@section('sidebar')
    <a href="{{ route('customer.dashboard') }}" class="nav-link">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="{{ route('customer.booking') }}" class="nav-link">
        <i class="bi bi-calendar-plus"></i> Booking Servis
    </a>
    <a href="{{ route('customer.history') }}" class="nav-link">
        <i class="bi bi-clock-history"></i> Riwayat Servis
    </a>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                @if($mechanic->photo)
                <img src="{{ asset('storage/photos/' . $mechanic->photo) }}" 
                     alt="Profile Photo" 
                     class="img-fluid rounded-circle mb-3" 
                     style="width: 100px; height: 100px; object-fit: cover;">
                @else
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 100px; height: 100px; font-size: 2rem;">
                    {{ strtoupper(substr($mechanic->name, 0, 1)) }}
                </div>
                @endif
                <h4>{{ $mechanic->name }}</h4>
                <div class="mb-3">
                    <h2 class="mb-0">{{ number_format($avgRating, 1) }}</h2>
                    <div>
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= round($avgRating) ? '-fill text-warning' : '' }}"></i>
                        @endfor
                    </div>
                    <small class="text-muted">Berdasarkan {{ $totalReviews }} review</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
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
                                        <span class="ms-2">{{ $review->rating_mechanic ?? 0 }}/5</span>
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
                                    Service: {{ $review->booking->service_type }} - {{ $review->booking->booking_date->format('d M Y') }}
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
                    <p class="mt-3">Belum ada review untuk mekanik ini</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
