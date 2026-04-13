<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Notification;
use Illuminate\Support\Str;
use App\Models\RefundRequest;

class PaymentController extends Controller
{
    public function initiate(Request $request, $bookingId)
    {
        $request->validate([
            'method' => 'required|in:card,bank_transfer'
        ]);

        $booking = Booking::with('payment')
            ->where('user_id', auth()->id())
            ->findOrFail($bookingId);

        if ($booking->payment_status === 'paid') {
            return response()->json(['message' => 'Already paid'], 400);
        }

        $payment = Payment::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'amount' => $booking->final_price,
                'currency' => 'EGP',
                'method' => $request->method,
                'status' => 'pending',
                'transaction_ref' => 'txn_' . strtoupper(Str::random(10)),
            ]
        );

        return response()->json([
            'message' => 'Payment initialized',
            'payment' => $payment,
            'booking' => $booking
        ]);
    }

    public function pay($bookingId)
    {
        $booking = Booking::with('payment', 'trip')
            ->findOrFail($bookingId);

        if ($booking->payment_status === 'paid') {
            return response()->json(['message' => 'Already paid'], 400);
        }

        $success = true; 

        if (!$success) {
            $booking->payment->update(['status' => 'failed']);
            $booking->update(['payment_status' => 'failed']);

            return response()->json(['message' => 'Payment failed'], 400);
        }

        $booking->payment->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);

        $booking->update([
            'payment_status' => 'paid',
            'status' => 'confirmed'
        ]);

        Notification::create([
            'user_id' => $booking->user_id,
            'title' => 'Payment Successful',
            'type' => 'payment',
            'message' => 'Payment completed. Waiting for agency approval.',
            'link' => '/bookings/' . $booking->id
        ]);

        return response()->json([
            'message' => 'Payment successful'
        ]);
    }

    public function requestRefund(Request $request, $bookingId)
{
    $request->validate([
        'reason' => 'required|string',
        'amount' => 'required|numeric|min:1'
    ]);

    $booking = Booking::with('payment')
        ->where('user_id', auth()->id())
        ->findOrFail($bookingId);

    if (!$booking->payment || $booking->payment->status !== 'paid') {
        return response()->json(['message' => 'Cannot refund unpaid booking'], 400);
    }

    $refund =RefundRequest::create([
        'booking_id' => $booking->id,
        'user_id' => auth()->id(),
        'amount' => $request->amount,
        'reason' => $request->reason,
        'status' => 'pending'
    ]);

    Notification::create([
        'user_id' => 1, 
        'title' => 'Refund Request',
        'type' => 'refund',
        'message' => 'New refund request for booking #' . $booking->id,
        'link' => '/admin/refunds/' . $refund->id
    ]);

    return response()->json([
        'message' => 'Refund request submitted',
        'refund_request' => $refund
    ]);
}
public function approveRefund($refundId)
{
    $refund =RefundRequest::with('booking.payment')
        ->findOrFail($refundId);

    $booking = $refund->booking;

    $refund->update([
        'status' => 'approved'
    ]);

    $booking->payment->update([
        'refund_amount' => $refund->amount,
        'refund_status' => 'approved'
    ]);

    $booking->update([
        'status' => 'cancelled'
    ]);

    Notification::create([
        'user_id' => $booking->user_id,
        'title' => 'Refund Approved',
        'type' => 'refund',
        'message' => 'Your refund has been approved',
        'link' => '/bookings/' . $booking->id
    ]);

    return response()->json([
        'message' => 'Refund approved successfully'
    ]);
}

public function rejectRefund(Request $request, $refundId)
{
    $request->validate([
        'admin_reason' => 'required|string'
    ]);

    $refund =RefundRequest::with('booking')
        ->findOrFail($refundId);

    $refund->update([
        'status' => 'rejected',
        'admin_reason' => $request->admin_reason
    ]);

    Notification::create([
        'user_id' => $refund->booking->user_id,
        'title' => 'Refund Rejected',
        'type' => 'refund',
        'message' => 'Your refund was rejected: ' . $request->admin_reason,
        'link' => '/bookings/' . $refund->booking_id
    ]);

    return response()->json([
        'message' => 'Refund rejected'
    ]);
}
public function paymentHistory()
{
    return Booking::where('user_id', auth()->id())
        ->with('payment')
        ->latest()
        ->get();
}

}