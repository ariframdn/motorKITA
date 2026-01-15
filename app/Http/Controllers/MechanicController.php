<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Attendance;
use App\Models\Salary;
use App\Models\Review;
use App\Models\Bonus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MechanicController extends Controller
{
    public function dashboard()
    {
        $todayTasks = Booking::where('mechanic_id', auth()->id())
            ->whereDate('booking_date', today())
            ->with(['customer', 'vehicle'])
            ->get();

        $stats = [
            'pending' => Booking::where('mechanic_id', auth()->id())
                ->where('status', 'pending')->count(),
            'in_progress' => Booking::where('mechanic_id', auth()->id())
                ->where('status', 'in_progress')->count(),
            'done_today' => Booking::where('mechanic_id', auth()->id())
                ->where('status', 'done')
                ->whereDate('updated_at', today())->count(),
        ];

        return view('mechanic.dashboard', compact('todayTasks', 'stats'));
    }

    public function tasks()
    {
        $tasks = Booking::where('mechanic_id', auth()->id())
            ->with(['customer', 'vehicle'])
            ->latest()
            ->paginate(15);

        return view('mechanic.tasks', compact('tasks'));
    }

    public function updateTask(Request $request, $id)
    {
        $booking = Booking::where('mechanic_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,done',
            'notes' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
        ]);

        $wasDone = $booking->status === 'done';
        $isNowDone = $validated['status'] === 'done';

        $booking->update($validated);

        if ($isNowDone && !$wasDone) {
            // Update vehicle service date
            if ($booking->vehicle) {
                $booking->vehicle->update([
                    'last_service_date' => now(),
                ]);
            }

            // Deduct inventory
            \App\Http\Controllers\ServiceHppController::deductInventory($booking);

            // Calculate and apply bonus if applicable
            $servicesToday = Booking::where('mechanic_id', auth()->id())
                ->where('status', 'done')
                ->whereDate('updated_at', today())
                ->count();

            if ($servicesToday > 5) {
                $bonus = \App\Models\Bonus::where('is_active', true)
                    ->where('type', 'performance')
                    ->where('min_services', '<=', $servicesToday)
                    ->first();

                if ($bonus && $bonus->isApplicable($servicesToday, today())) {
                    // Notify mechanic about bonus
                    \App\Http\Controllers\NotificationController::create(
                        auth()->id(),
                        'bonus',
                        'Bonus Diterima!',
                        'Anda mendapat bonus karena menyelesaikan lebih dari 5 service hari ini!',
                        'App\Models\Bonus',
                        $bonus->id
                    );
                }
            }

            // Notify customer
            \App\Http\Controllers\NotificationController::create(
                $booking->customer_id,
                'booking',
                'Service Selesai',
                'Service kendaraan Anda telah selesai.',
                'App\Models\Booking',
                $booking->id
            );
        }

        return redirect()->back()->with('success', 'Status berhasil diupdate!');
    }

    public function submitReport(Request $request, $id)
    {
        $booking = Booking::where('mechanic_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'notes' => 'required|string',
            'cost' => 'required|numeric|min:0',
        ]);

        // Calculate HPP if not set
        $hppCost = $booking->hpp_cost ?? 0;
        if ($hppCost == 0) {
            $hppCost = \App\Http\Controllers\ServiceHppController::calculateHppForBooking($booking);
        }

        $finalCost = $booking->final_cost ?? ($validated['cost'] - ($booking->discount_amount ?? 0));
        
        $booking->update([
            'notes' => $validated['notes'],
            'cost' => $validated['cost'],
            'hpp_cost' => $hppCost,
            'final_cost' => $finalCost,
            'status' => 'done',
        ]);

        $booking->vehicle->update([
            'last_service_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Laporan servis berhasil disubmit!');
    }

    /**
     * Show earnings dashboard (Mechanic)
     */
    public function earnings()
    {
        $userId = auth()->id();
        
        // Total days worked
        $totalDaysWorked = Attendance::where('mechanic_id', $userId)
            ->count();
        
        // Total earnings (all time)
        $totalEarnings = Salary::where('mechanic_id', $userId)
            ->where('status', 'paid')
            ->sum('total_amount');
        
        // Monthly earnings
        $monthlyEarnings = Salary::where('mechanic_id', $userId)
            ->where('status', 'paid')
            ->whereYear('payment_date', now()->year)
            ->select(
                DB::raw('MONTH(payment_date) as month'),
                DB::raw('SUM(total_amount) as earnings')
            )
            ->groupBy('month')
            ->get();
        
        // Recent salaries
        $recentSalaries = Salary::where('mechanic_id', $userId)
            ->with('processor')
            ->latest()
            ->paginate(10);
        
        // Services completed today
        $servicesToday = Booking::where('mechanic_id', $userId)
            ->where('status', 'done')
            ->whereDate('updated_at', today())
            ->count();
        
        // Check for bonus eligibility (>5 services today)
        $bonus = Bonus::where('is_active', true)
            ->where('type', 'performance')
            ->where('min_services', '<=', $servicesToday)
            ->first();
        
        $bonusAmount = 0;
        if ($bonus && $bonus->isApplicable($servicesToday, today())) {
            $bonusAmount = $bonus->calculateBonus();
        }

        return view('mechanic.earnings', compact(
            'totalDaysWorked',
            'totalEarnings',
            'monthlyEarnings',
            'recentSalaries',
            'servicesToday',
            'bonus',
            'bonusAmount'
        ));
    }

    /**
     * Show salaries list (Mechanic)
     */
    public function salaries()
    {
        $salaries = Salary::where('mechanic_id', auth()->id())
            ->with('processor')
            ->latest()
            ->paginate(20);

        return view('mechanic.salaries', compact('salaries'));
    }

    /**
     * Show reviews for mechanic
     */
    public function reviews()
    {
        $reviews = Review::where('mechanic_id', auth()->id())
            ->with(['customer', 'booking.vehicle'])
            ->latest()
            ->paginate(10);

        $avgRating = Review::where('mechanic_id', auth()->id())
            ->avg('rating_mechanic') ?? 0;

        $totalReviews = $reviews->total();

        return view('mechanic.reviews', compact('reviews', 'avgRating', 'totalReviews'));
    }
}