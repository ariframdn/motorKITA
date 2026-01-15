<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    /**
     * List promo codes (Admin)
     */
    public function index()
    {
        $promoCodes = PromoCode::latest()->paginate(20);
        return view('admin.promo-codes', compact('promoCodes'));
    }

    /**
     * Store promo code (Admin)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:promo_codes,code|max:50',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        PromoCode::create($validated);

        return redirect()->route('admin.promo-codes')
            ->with('success', 'Kode promo berhasil ditambahkan!');
    }

    /**
     * Update promo code (Admin)
     */
    public function update(Request $request, $id)
    {
        $promoCode = PromoCode::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|unique:promo_codes,code,' . $id . '|max:50',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $promoCode->update($validated);

        return redirect()->route('admin.promo-codes')
            ->with('success', 'Kode promo berhasil diupdate!');
    }

    /**
     * Delete promo code (Admin)
     */
    public function destroy($id)
    {
        PromoCode::findOrFail($id)->delete();
        return redirect()->route('admin.promo-codes')
            ->with('success', 'Kode promo berhasil dihapus!');
    }

    /**
     * Validate promo code (Customer)
     */
    public function validateCode(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $promoCode = PromoCode::where('code', $validated['code'])->first();

        if (!$promoCode) {
            return response()->json([
                'valid' => false,
                'message' => 'Kode promo tidak ditemukan!'
            ], 400);
        }

        if (!$promoCode->isValid($validated['amount'])) {
            return response()->json([
                'valid' => false,
                'message' => 'Kode promo tidak berlaku!'
            ], 400);
        }

        $discount = $promoCode->calculateDiscount($validated['amount']);
        $finalAmount = $validated['amount'] - $discount;

        return response()->json([
            'valid' => true,
            'promo_code' => $promoCode,
            'discount' => $discount,
            'final_amount' => $finalAmount,
        ]);
    }
}
