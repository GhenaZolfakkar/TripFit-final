<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BookingController extends Controller
{

    public function store(Request $request, $tripId)
    {
        $request->validate([
            'traveler_count' => 'required|integer|min:1'
        ]);

        $trip = Trip::findOrFail($tripId);

        if ($trip->status !== 'active') {
            return response()->json(['message' => 'Trip not active'], 400);
        }

        $remainingSeats = $trip->remainingSeats();

        if ($request->traveler_count > $remainingSeats) {
            return response()->json([
                'message' => 'Not enough seats available',
                'remaining_seats' => $remainingSeats
            ], 400);
        }

        $pricePerPerson = $trip->price;
        $totalPrice = $pricePerPerson * $request->traveler_count;

        $tier = $trip->tier;
        $config = config("commission.$tier");

        $agencyRate = $config['agency_rate'];
        $customerFee = $config['customer_fee'];

        $agencyCommission = ($totalPrice * $agencyRate) / 100;
        $customerFeeAmount = ($totalPrice * $customerFee) / 100;

        $finalPrice = $totalPrice + $customerFeeAmount;

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'trip_id' => $trip->id,
            'agency_id' => $trip->agency_id,
            'traveler_count' => $request->traveler_count,
            'price_per_person' => $pricePerPerson,
            'total_price' => $totalPrice,
            'agency_commission_rate' => $agencyRate,
            'agency_commission_amount' => $agencyCommission,
            'customer_fee_rate' => $customerFee,
            'customer_fee_amount' => $customerFeeAmount,
            'final_price' => $finalPrice,
            'status' => 'pending',
            'payment_status' => 'unpaid'
        ]);

        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'Booking Created',
            'type' => 'booking',
            'message' => 'Booking created. Please proceed to payment.',
            'link' => '/bookings/' . $booking->id
        ]);

        return response()->json([
            'message' => 'Booking created successfully',
            'booking' => $booking
        ]);
    }

    public function paymentSummary($id)
    {
        $booking = Booking::with('trip')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return response()->json([
            'booking_id' => $booking->id,
            'trip_title' => $booking->trip->title,
            'traveler_count' => $booking->traveler_count,
            'price_per_person' => $booking->price_per_person,
            'total_price' => $booking->total_price,
            'service_fee' => $booking->customer_fee_amount,
            'final_price' => $booking->final_price,
            'payment_status' => $booking->payment_status
        ]);
    }

    public function cancel($id)
    {
        $booking = Booking::with('trip')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $booking->update([
            'status' => 'cancelled'
        ]);

        Notification::create([
            'user_id' => $booking->user_id,
            'title' => 'Booking Cancelled',
            'type' => 'booking',
            'message' => 'Your booking has been cancelled',
            'link' => '/bookings/' . $booking->id
        ]);

        return response()->json([
            'message' => 'Booking cancelled'
        ]);
    }

    public function myBookings()
    {
        return Booking::where('user_id', auth()->id())
            ->with(['trip', 'payment'])
            ->latest()
            ->get();
    }

    public function approve($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->payment_status !== 'paid') {
            return response()->json([
                'message' => 'Cannot approve unpaid booking'
            ], 400);
        }

        $booking->update([
            'status' => 'approved'
        ]);

        Notification::create([
            'user_id' => $booking->user_id,
            'title' => 'Booking Approved',
            'type' => 'booking',
            'message' => 'Your booking has been approved',
            'link' => '/bookings/' . $booking->id
        ]);

        return response()->json([
            'message' => 'Booking approved'
        ]);
    }
}