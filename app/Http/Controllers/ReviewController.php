<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Store review (Customer)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'review_type' => 'required|in:workshop,mechanic,both',
            'rating_workshop' => 'required_if:review_type,workshop,both|nullable|integer|min:1|max:5',
            'rating_mechanic' => 'required_if:review_type,mechanic,both|nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $booking = Booking::findOrFail($validated['booking_id']);

        // Check if customer owns the booking
        if ($booking->customer_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized!');
        }

        // Check if review already exists
        if ($booking->review) {
            return redirect()->back()->with('error', 'Anda sudah memberikan review untuk booking ini!');
        }

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = time() . '_' . $booking->id . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('storage/reviews'), $filename);
            $photoPath = $filename;
        }

        $review = Review::create([
            'booking_id' => $booking->id,
            'customer_id' => auth()->id(),
            'mechanic_id' => $booking->mechanic_id,
            'review_type' => $validated['review_type'],
            'rating_workshop' => $validated['rating_workshop'] ?? null,
            'rating_mechanic' => $validated['rating_mechanic'] ?? null,
            'comment' => $validated['comment'] ?? null,
            'photo' => $photoPath,
        ]);

        // Notify mechanic if reviewed
        if ($booking->mechanic_id && ($validated['review_type'] === 'mechanic' || $validated['review_type'] === 'both')) {
            NotificationController::create(
                $booking->mechanic_id,
                'review',
                'Review Baru',
                'Anda mendapat review baru dari ' . auth()->user()->name,
                'App\Models\Review',
                $review->id
            );
        }

        // Notify admin
        $admin = \App\Models\User::where('role', 'admin')->first();
        if ($admin) {
            NotificationController::create(
                $admin->id,
                'review',
                'Review Baru',
                auth()->user()->name . ' memberikan review untuk booking #' . $booking->id,
                'App\Models\Review',
                $review->id
            );
        }

        return redirect()->back()->with('success', 'Review berhasil dikirim!');
    }

    /**
     * Show reviews for mechanic
     */
    public function mechanicReviews($mechanicId)
    {
        $mechanic = \App\Models\User::findOrFail($mechanicId);
        
        if (!$mechanic->isMechanic()) {
            return redirect()->back()->with('error', 'User bukan mekanik!');
        }

        $reviews = Review::where('mechanic_id', $mechanicId)
            ->with(['customer', 'booking.vehicle'])
            ->latest()
            ->paginate(10);

        $avgRating = Review::where('mechanic_id', $mechanicId)
            ->avg('rating_mechanic') ?? 0;

        $totalReviews = $reviews->total();

        return view('reviews.mechanic', compact('mechanic', 'reviews', 'avgRating', 'totalReviews'));
    }

    /**
     * Show reviews for workshop
     */
    public function workshopReviews()
    {
        $reviews = Review::whereNotNull('rating_workshop')
            ->with(['customer', 'booking.vehicle', 'mechanic'])
            ->latest()
            ->paginate(10);

        $avgRating = Review::whereNotNull('rating_workshop')
            ->avg('rating_workshop') ?? 0;

        $totalReviews = $reviews->total();

        return view('reviews.workshop', compact('reviews', 'avgRating', 'totalReviews'));
    }
}
