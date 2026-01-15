<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    /**
     * List salaries (Admin)
     */
    public function index(Request $request)
    {
        $query = Salary::with(['mechanic', 'processor']);

        if ($request->has('mechanic_id')) {
            $query->where('mechanic_id', $request->mechanic_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $salaries = $query->latest()->paginate(20);
        $mechanics = User::where('role', 'mechanic')->get();

        return view('admin.salaries', compact('salaries', 'mechanics'));
    }

    /**
     * Create salary (Admin)
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'mechanic_id' => 'required|exists:users,id',
            'base_salary' => 'required|numeric|min:0',
            'daily_rate' => 'required|numeric|min:0',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
            'bonus_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:transfer,cash',
            'payment_proof' => 'required_if:payment_method,transfer|nullable|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string',
        ]);

        // Calculate attendance days
        $attendanceDays = Attendance::where('mechanic_id', $validated['mechanic_id'])
            ->whereBetween('date', [$validated['period_start'], $validated['period_end']])
            ->count();

        // Calculate total
        $totalAmount = ($validated['daily_rate'] * $attendanceDays) + ($validated['bonus_amount'] ?? 0);

        $salaryData = [
            'mechanic_id' => $validated['mechanic_id'],
            'base_salary' => $validated['base_salary'],
            'attendance_days' => $attendanceDays,
            'daily_rate' => $validated['daily_rate'],
            'bonus_amount' => $validated['bonus_amount'] ?? 0,
            'total_amount' => $totalAmount,
            'payment_method' => $validated['payment_method'],
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'notes' => $validated['notes'] ?? null,
            'processed_by' => auth()->id(),
        ];

        // Handle payment proof upload
        if ($request->hasFile('payment_proof')) {
            $photo = $request->file('payment_proof');
            $filename = time() . '_' . $validated['mechanic_id'] . '.' . $photo->getClientOriginalExtension();
            if (!file_exists(public_path('storage/salaries'))) {
                mkdir(public_path('storage/salaries'), 0777, true);
            }
            $photo->move(public_path('storage/salaries'), $filename);
            $salaryData['payment_proof'] = $filename;
        }

        $salary = Salary::create($salaryData);

        // Notify mechanic
        \App\Http\Controllers\NotificationController::create(
            $validated['mechanic_id'],
            'salary',
            'Gaji Baru',
            'Gaji periode ' . $validated['period_start'] . ' - ' . $validated['period_end'] . ' telah dibuat.',
            'App\Models\Salary',
            $salary->id
        );

        return redirect()->route('admin.salaries')
            ->with('success', 'Gaji berhasil dibuat!');
    }

    /**
     * Mark salary as paid (Admin)
     */
    public function markAsPaid($id)
    {
        $salary = Salary::findOrFail($id);
        $salary->markAsPaid();

        // Notify mechanic
        \App\Http\Controllers\NotificationController::create(
            $salary->mechanic_id,
            'salary',
            'Gaji Dibayar',
            'Gaji periode ' . $salary->period_start . ' - ' . $salary->period_end . ' telah dibayar.',
            'App\Models\Salary',
            $salary->id
        );

        return redirect()->back()->with('success', 'Gaji berhasil ditandai sebagai dibayar!');
    }

    /**
     * Show salary detail (Mechanic)
     */
    public function show($id)
    {
        $salary = Salary::with(['mechanic', 'processor'])->findOrFail($id);

        // Check if mechanic owns this salary
        if (auth()->user()->isMechanic() && $salary->mechanic_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized!');
        }

        return view('salary.show', compact('salary'));
    }
}
