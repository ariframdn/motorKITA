<?php

namespace App\Http\Controllers;

use App\Models\Bonus;
use Illuminate\Http\Request;

class BonusController extends Controller
{
    /**
     * List bonuses (Admin)
     */
    public function index()
    {
        $bonuses = Bonus::with('creator')->latest()->paginate(20);
        return view('admin.bonuses', compact('bonuses'));
    }

    /**
     * Store bonus (Admin)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:performance,holiday,custom',
            'min_services' => 'nullable|integer|min:1',
            'bonus_amount' => 'required|numeric|min:0',
            'bonus_type' => 'required|in:fixed,percentage',
            'effective_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'is_active' => 'boolean',
        ]);

        Bonus::create(array_merge($validated, [
            'created_by' => auth()->id(),
        ]));

        return redirect()->route('admin.bonuses')
            ->with('success', 'Bonus berhasil ditambahkan!');
    }

    /**
     * Update bonus (Admin)
     */
    public function update(Request $request, $id)
    {
        $bonus = Bonus::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:performance,holiday,custom',
            'min_services' => 'nullable|integer|min:1',
            'bonus_amount' => 'required|numeric|min:0',
            'bonus_type' => 'required|in:fixed,percentage',
            'effective_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'is_active' => 'boolean',
        ]);

        $bonus->update($validated);

        return redirect()->route('admin.bonuses')
            ->with('success', 'Bonus berhasil diupdate!');
    }

    /**
     * Delete bonus (Admin)
     */
    public function destroy($id)
    {
        Bonus::findOrFail($id)->delete();
        return redirect()->route('admin.bonuses')
            ->with('success', 'Bonus berhasil dihapus!');
    }

    /**
     * Calculate bonus for mechanic (helper method)
     */
    public static function calculateBonusForMechanic($mechanicId, $date, $serviceCount)
    {
        $bonuses = Bonus::where('is_active', true)
            ->where(function($query) use ($date) {
                $query->whereNull('effective_date')
                    ->orWhere('effective_date', '<=', $date);
            })
            ->where(function($query) use ($date) {
                $query->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>=', $date);
            })
            ->get();

        $totalBonus = 0;

        foreach ($bonuses as $bonus) {
            if ($bonus->isApplicable($serviceCount, $date)) {
                if ($bonus->bonus_type === 'fixed') {
                    $totalBonus += $bonus->bonus_amount;
                }
            }
        }

        return $totalBonus;
    }
}
