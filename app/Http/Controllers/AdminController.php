<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Inventory;
use App\Models\User;
use App\Models\Payment;
use App\Models\ServicePrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_revenue' => Booking::where('status', 'done')->sum(DB::raw('COALESCE(final_cost, cost, 0)')),
            'low_stock_items' => Inventory::whereColumn('quantity', '<=', 'low_stock_level')->count(),
        ];

        $recentBookings = Booking::with(['customer', 'vehicle', 'mechanic'])
            ->latest()
            ->take(10)
            ->get();

        $monthlyRevenue = Booking::where('status', 'done')
            ->whereYear('updated_at', now()->year)
            ->select(
                DB::raw('MONTH(updated_at) as month'),
                DB::raw('SUM(COALESCE(final_cost, cost, 0)) as revenue')
            )
            ->groupBy('month')
            ->get();

        // Low stock items
        $lowStockItems = Inventory::whereColumn('quantity', '<=', 'low_stock_level')
            ->take(5)
            ->get();

        // Pending payments
        $pendingPayments = Payment::where('status', 'pending')
            ->with(['booking.customer'])
            ->latest()
            ->take(5)
            ->get();

        // Today bookings count
        $stats['today_bookings'] = Booking::whereDate('booking_date', today())->count();
        $stats['revenue'] = Booking::where('status', 'done')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum(DB::raw('COALESCE(final_cost, cost, 0)'));
        $stats['pending_payments'] = Payment::where('status', 'pending')->count();

        return view('admin.dashboard', compact(
            'stats', 
            'recentBookings', 
            'monthlyRevenue',
            'lowStockItems',
            'pendingPayments'
        ));
    }

    public function inventory()
    {
        $items = Inventory::latest()->paginate(20);
        return view('admin.inventory', compact('items'));
    }

    public function storeInventory(Request $request)
    {
        $validated = $request->validate([
            'part_name' => 'required|string',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'low_stock_level' => 'required|integer|min:0',
        ]);

        Inventory::create($validated);

        return redirect()->route('admin.inventory')
            ->with('success', 'Item berhasil ditambahkan!');
    }

    public function updateInventory(Request $request, $id)
    {
        $item = Inventory::findOrFail($id);

        $validated = $request->validate([
            'part_name' => 'required|string',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'low_stock_level' => 'required|integer|min:0',
        ]);

        $item->update($validated);

        return redirect()->route('admin.inventory')
            ->with('success', 'Item berhasil diupdate!');
    }

    public function deleteInventory($id)
    {
        Inventory::findOrFail($id)->delete();
        return redirect()->route('admin.inventory')
            ->with('success', 'Item berhasil dihapus!');
    }

    public function bookings()
    {
        $bookings = Booking::with(['customer', 'vehicle', 'mechanic', 'payments'])
            ->latest()
            ->paginate(20);

        $mechanics = User::where('role', 'mechanic')->get();

        return view('admin.bookings', compact('bookings', 'mechanics'));
    }

    public function payments()
    {
        $payments = Payment::with(['booking.customer', 'booking.vehicle', 'approver'])
            ->latest()
            ->paginate(20);

        return view('admin.payments', compact('payments'));
    }

    public function servicePrices()
    {
        $services = ServicePrice::latest()->paginate(20);
        return view('admin.service-prices', compact('services'));
    }

    public function storeServicePrice(Request $request)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|unique:service_prices',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        ServicePrice::create($validated);

        return redirect()->route('admin.service-prices')
            ->with('success', 'Harga service berhasil ditambahkan!');
    }

    public function updateServicePrice(Request $request, $id)
    {
        $service = ServicePrice::findOrFail($id);

        $validated = $request->validate([
            'service_name' => 'required|string|unique:service_prices,service_name,' . $id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $service->update($validated);

        return redirect()->route('admin.service-prices')
            ->with('success', 'Harga service berhasil diupdate!');
    }

    public function deleteServicePrice($id)
    {
        ServicePrice::findOrFail($id)->delete();
        return redirect()->route('admin.service-prices')
            ->with('success', 'Harga service berhasil dihapus!');
    }

    public function assignMechanic(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $validated = $request->validate([
            'mechanic_id' => 'required|exists:users,id',
        ]);

        $booking->update([
            'mechanic_id' => $validated['mechanic_id'],
            'status' => 'in_progress',
        ]);

        return redirect()->back()->with('success', 'Mekanik berhasil ditugaskan!');
    }

    public function billing($id)
    {
        $booking = Booking::with(['customer', 'vehicle', 'mechanic'])->findOrFail($id);
        return view('admin.billing', compact('booking'));
    }

    public function updateBilling(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $validated = $request->validate([
            'cost' => 'required|numeric|min:0',
            'promo_code' => 'nullable|string',
        ]);

        $discountAmount = 0;
        $promoCodeId = $booking->promo_code_id;
        
        // Apply promo code if provided
        if (!empty($validated['promo_code'])) {
            $promoCode = \App\Models\PromoCode::where('code', $validated['promo_code'])->first();
            if ($promoCode && $promoCode->isValid($validated['cost'])) {
                $discountAmount = $promoCode->calculateDiscount($validated['cost']);
                $promoCodeId = $promoCode->id;
                $promoCode->incrementUsage();
            }
        }

        $finalCost = $validated['cost'] - $discountAmount;

        // Calculate HPP if not set
        if (!$booking->hpp_cost || $booking->hpp_cost == 0) {
            $hppCost = \App\Http\Controllers\ServiceHppController::calculateHppForBooking($booking);
        } else {
            $hppCost = $booking->hpp_cost;
        }

        $booking->update([
            'cost' => $validated['cost'],
            'promo_code_id' => $promoCodeId,
            'discount_amount' => $discountAmount,
            'hpp_cost' => $hppCost,
            'final_cost' => $finalCost,
        ]);

        return redirect()->back()->with('success', 'Biaya berhasil diupdate!');
    }
}