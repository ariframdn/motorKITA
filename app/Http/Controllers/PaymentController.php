<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    // Customer: Submit payment
    public function submitPayment(Request $request, $bookingId)
    {
        $booking = Booking::where('customer_id', auth()->id())->findOrFail($bookingId);

        $validated = $request->validate([
            'payment_method' => 'required|in:cash,qris,transfer',
            'payment_proof' => 'required_if:payment_method,qris,transfer|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle payment proof upload
        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $proof = $request->file('payment_proof');
            $filename = time() . '_' . $bookingId . '.' . $proof->getClientOriginalExtension();
            $proof->move(public_path('storage/payments'), $filename);
            $proofPath = $filename;
        }

        // Update booking
        $booking->update([
            'payment_method' => $validated['payment_method'],
            'payment_proof' => $proofPath,
            'payment_status' => $validated['payment_method'] === 'cash' ? 'paid' : 'pending',
        ]);

        // Create payment record
        Payment::create([
            'booking_id' => $bookingId,
            'payment_method' => $validated['payment_method'],
            'amount' => $booking->cost,
            'payment_proof' => $proofPath,
            'status' => $validated['payment_method'] === 'cash' ? 'approved' : 'pending',
        ]);

        return redirect()->back()->with('success', 'Pembayaran berhasil dikirim!');
    }

    // Admin: Approve payment
    public function approvePayment(Request $request, $paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $booking = $payment->booking;

        $validated = $request->validate([
            'admin_notes' => 'nullable|string',
        ]);

        $payment->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        $booking->update([
            'payment_status' => 'paid',
        ]);

        return redirect()->back()->with('success', 'Pembayaran berhasil disetujui!');
    }

    // Admin: Reject payment
    public function rejectPayment(Request $request, $paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $booking = $payment->booking;

        $validated = $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $payment->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'admin_notes' => $validated['admin_notes'],
        ]);

        $booking->update([
            'payment_status' => 'rejected',
        ]);

        return redirect()->back()->with('success', 'Pembayaran ditolak!');
    }
}
