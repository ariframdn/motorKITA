<?php

namespace App\Http\Controllers;

use App\Models\ServiceHppItem;
use App\Models\ServicePrice;
use App\Models\Inventory;
use Illuminate\Http\Request;

class ServiceHppController extends Controller
{
    /**
     * List HPP items for service (Admin)
     */
    public function index($serviceId)
    {
        $service = ServicePrice::findOrFail($serviceId);
        $hppItems = ServiceHppItem::where('service_price_id', $serviceId)
            ->with('inventory')
            ->get();
        $inventories = Inventory::all();

        return view('admin.service-hpp', compact('service', 'hppItems', 'inventories'));
    }

    /**
     * Store HPP item (Admin)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_price_id' => 'required|exists:service_prices,id',
            'inventory_id' => 'required|exists:inventory,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $totalCost = $validated['quantity'] * $validated['unit_price'];

        ServiceHppItem::create([
            'service_price_id' => $validated['service_price_id'],
            'inventory_id' => $validated['inventory_id'],
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'total_cost' => $totalCost,
        ]);

        return redirect()->back()
            ->with('success', 'Item HPP berhasil ditambahkan!');
    }

    /**
     * Update HPP item (Admin)
     */
    public function update(Request $request, $id)
    {
        $hppItem = ServiceHppItem::findOrFail($id);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $totalCost = $validated['quantity'] * $validated['unit_price'];

        $hppItem->update([
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'total_cost' => $totalCost,
        ]);

        return redirect()->back()
            ->with('success', 'Item HPP berhasil diupdate!');
    }

    /**
     * Delete HPP item (Admin)
     */
    public function destroy($id)
    {
        ServiceHppItem::findOrFail($id)->delete();
        return redirect()->back()
            ->with('success', 'Item HPP berhasil dihapus!');
    }

    /**
     * Calculate HPP for booking
     */
    public static function calculateHppForBooking($booking)
    {
        $service = ServicePrice::where('service_name', $booking->service_type)->first();
        if (!$service) {
            return 0;
        }

        $hppItems = ServiceHppItem::where('service_price_id', $service->id)->get();
        $totalHpp = $hppItems->sum('total_cost');

        return $totalHpp;
    }

    /**
     * Deduct inventory when booking is done
     */
    public static function deductInventory($booking)
    {
        $service = ServicePrice::where('service_name', $booking->service_type)->first();
        if (!$service) {
            return;
        }

        $hppItems = ServiceHppItem::where('service_price_id', $service->id)->get();

        foreach ($hppItems as $item) {
            $inventory = Inventory::find($item->inventory_id);
            if ($inventory) {
                $inventory->decrement('quantity', $item->quantity);
            }
        }
    }
}
