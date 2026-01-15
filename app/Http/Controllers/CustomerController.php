<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\ServicePrice;
use App\Models\PromoCode;
use App\Models\ServiceHppItem;
use App\Http\Controllers\ServiceHppController;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $vehicles = $user->vehicles;
        $upcomingBookings = $user->bookings()
            ->where('status', '!=', 'done')
            ->latest()
            ->take(5)
            ->get();

        return view('customer.dashboard', compact('vehicles', 'upcomingBookings'));
    }

    public function booking()
    {
        $vehicles = auth()->user()->vehicles;
        $mechanics = User::where('role', 'mechanic')->get();
        $services = ServicePrice::where('is_active', true)->get();
        
        return view('customer.booking', compact('vehicles', 'mechanics', 'services'));
    }

    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'service_type' => 'required|string',
            'booking_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'promo_code' => 'nullable|string',
        ]);

        // Get service price
        $service = ServicePrice::where('service_name', $validated['service_type'])->first();
        $cost = $service ? $service->price : 0;

        // Calculate HPP
        $hppCost = 0;
        if ($service) {
            $hppCost = ServiceHppController::calculateHppForBooking((object)['service_type' => $validated['service_type']]);
        }

        // Apply promo code if provided
        $promoCodeId = null;
        $discountAmount = 0;
        $finalCost = $cost;
        
        if (!empty($validated['promo_code'])) {
            $promoCode = PromoCode::where('code', $validated['promo_code'])->first();
            if ($promoCode && $promoCode->isValid($cost)) {
                $discountAmount = $promoCode->calculateDiscount($cost);
                $finalCost = $cost - $discountAmount;
                $promoCodeId = $promoCode->id;
                $promoCode->incrementUsage();
            }
        }

        $booking = Booking::create([
            'customer_id' => auth()->id(),
            'vehicle_id' => $validated['vehicle_id'],
            'service_type' => $validated['service_type'],
            'booking_date' => $validated['booking_date'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
            'cost' => $cost,
            'promo_code_id' => $promoCodeId,
            'discount_amount' => $discountAmount,
            'hpp_cost' => $hppCost,
            'final_cost' => $finalCost,
            'payment_status' => 'pending',
        ]);

        // Notify admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            NotificationController::create(
                $admin->id,
                'booking',
                'Booking Baru',
                'Booking baru dari ' . auth()->user()->name,
                'App\Models\Booking',
                $booking->id
            );
        }

        return redirect()->route('customer.history')
            ->with('success', 'Booking berhasil dibuat!');
    }

    public function history()
    {
        $bookings = auth()->user()->bookings()
            ->with(['vehicle', 'mechanic'])
            ->latest()
            ->get();

        return view('customer.history', compact('bookings'));
    }

    public function addVehicle(Request $request)
    {
        $validated = $request->validate([
            'brand' => 'required|string',
            'model' => 'required|string',
            'plate_number' => 'required|string|unique:vehicles',
        ]);

        auth()->user()->vehicles()->create($validated);

        return redirect()->route('customer.dashboard')
            ->with('success', 'Kendaraan berhasil ditambahkan!');
    }
}