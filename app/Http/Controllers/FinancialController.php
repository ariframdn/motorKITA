<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Salary;
use App\Models\Bonus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialController extends Controller
{
    /**
     * Financial reports dashboard (Admin)
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        // Total Income (from completed bookings)
        $totalIncome = Booking::where('status', 'done')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->sum(DB::raw('COALESCE(final_cost, cost, 0)'));

        // Total Expenses (salaries + bonuses)
        $totalExpenses = Salary::where('status', 'paid')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('total_amount');

        // HPP Costs
        $totalHpp = Booking::where('status', 'done')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->sum(DB::raw('COALESCE(hpp_cost, 0)'));

        // Net Profit
        $netProfit = $totalIncome - $totalExpenses - $totalHpp;

        // Monthly income chart data
        $monthlyIncome = Booking::where('status', 'done')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(updated_at, "%Y-%m") as month'),
                DB::raw('SUM(COALESCE(final_cost, cost, 0)) as income')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Monthly expenses chart data
        $monthlyExpenses = Salary::where('status', 'paid')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(payment_date, "%Y-%m") as month'),
                DB::raw('SUM(total_amount) as expenses')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Income details
        $incomeDetails = Booking::where('status', 'done')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->with(['customer', 'mechanic', 'vehicle'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        // Expense details
        $expenseDetails = Salary::where('status', 'paid')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->with(['mechanic'])
            ->orderBy('payment_date', 'desc')
            ->paginate(20);

        return view('admin.financial', compact(
            'startDate',
            'endDate',
            'totalIncome',
            'totalExpenses',
            'totalHpp',
            'netProfit',
            'monthlyIncome',
            'monthlyExpenses',
            'incomeDetails',
            'expenseDetails'
        ));
    }

    /**
     * Get financial data as JSON (for charts)
     */
    public function getChartData(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        $monthlyIncome = Booking::where('status', 'done')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(updated_at, "%Y-%m") as month'),
                DB::raw('SUM(COALESCE(final_cost, cost, 0)) as income')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyExpenses = Salary::where('status', 'paid')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(payment_date, "%Y-%m") as month'),
                DB::raw('SUM(total_amount) as expenses')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'income' => $monthlyIncome,
            'expenses' => $monthlyExpenses,
        ]);
    }
}
