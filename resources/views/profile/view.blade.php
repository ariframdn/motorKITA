@extends('layouts.app')

@section('title', 'Profil - ' . $user->name)
@section('page-title', 'Profil')

@section('sidebar')
    @if(auth()->user()->isAdmin())
    <a href="{{ route('admin.dashboard') }}" class="nav-link">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    @elseif(auth()->user()->isMechanic())
    <a href="{{ route('mechanic.dashboard') }}" class="nav-link">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    @else
    <a href="{{ route('customer.dashboard') }}" class="nav-link">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    @endif
    <a href="{{ route('profile.edit') }}" class="nav-link">
        <i class="bi bi-person"></i> Edit Profil
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                @if($user->photo)
                <img src="{{ asset('storage/photos/' . $user->photo) }}" 
                     alt="Profile Photo" 
                     class="img-fluid rounded-circle mb-3" 
                     style="width: 150px; height: 150px; object-fit: cover;">
                @else
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 150px; height: 150px; font-size: 3rem;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                @endif
                <h4>{{ $user->name }}</h4>
                <p class="text-muted mb-2">{{ $user->email }}</p>
                @if($user->phone)
                <p class="text-muted"><i class="bi bi-telephone"></i> {{ $user->phone }}</p>
                @endif
                <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'mechanic' ? 'info' : 'success') }}">
                    {{ ucfirst($user->role) }}
                </span>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        @if($user->isMechanic())
        <!-- Mechanic Stats -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h6>Rating Rata-rata</h6>
                        <h2>{{ number_format($avgRating, 1) }}</h2>
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
                        <h6>Total Review</h6>
                        <h2>{{ $totalReviews }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h6>Total Booking</h6>
                        <h2>{{ $totalBookings }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews -->
        @if(isset($reviews) && $reviews->count() > 0)
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-star"></i> Review dari Customer</h5>
            </div>
            <div class="card-body">
                @foreach($reviews as $review)
                <div class="border-bottom pb-3 mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <h6 class="mb-0 me-3">{{ $review->customer->name }}</h6>
                        <div>
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= ($review->rating_mechanic ?? 0) ? '-fill text-warning' : '' }}"></i>
                            @endfor
                        </div>
                        <small class="text-muted ms-3">{{ $review->created_at->diffForHumans() }}</small>
                    </div>
                    @if($review->comment)
                    <p class="mb-2">{{ $review->comment }}</p>
                    @endif
                    @if($review->photo)
                    <img src="{{ asset('storage/reviews/' . $review->photo) }}" 
                         alt="Review Photo" 
                         class="img-thumbnail" 
                         style="max-width: 200px;">
                    @endif
                </div>
                @endforeach
                {{ $reviews->links() }}
            </div>
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
