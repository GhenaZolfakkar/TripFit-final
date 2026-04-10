<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class BookingController extends Controller
{
    public function store(Request $request, $tripId)
    {

        $request->validate([
            'traveler_count' => 'required|integer|min:1'
        ]);

        $trip = Trip::with('agency')->findOrFail($tripId);
        $remainingSeats = $trip->remainingSeats();

        if ($request->traveler_count > $remainingSeats) {
            return response()->json([
                'message' => 'Not enough seats available',
                'remaining_seats' => $remainingSeats
            ], 400);
        }

        if ($trip->status != 'active') {
            return response()->json([
                'message' => 'Trip not active'
            ], 400);
        }

        $currentTravelers = $trip->bookedTravelers();

        if (($currentTravelers + $request->traveler_count) > $trip->max_travelers) {
            return response()->json([
                'message' => 'Trip is full'
            ], 400);
        }

        $pricePerPerson = $trip->price;

        $totalPrice = $pricePerPerson * $request->traveler_count;

        $commissionRate = $trip->agency->commission_rate;

        $commissionAmount = ($totalPrice * $commissionRate) / 100;

        $booking = Booking::create([

            'user_id' => Auth::id(),
            'trip_id' => $trip->id,
            'agency_id' => $trip->agency_id,

            'traveler_count' => $request->traveler_count,

            'price_per_person' => $pricePerPerson,
            'total_price' => $totalPrice,

            'commission_rate' => $commissionRate,
            'commission_amount' => $commissionAmount,

            'status' => 'pending'

        ]);

        return response()->json([
            'message' => 'Booking request sent',
            'booking' => $booking
        ]);
    }

    public function confirm($id)
    {

        $booking = Booking::with('trip', 'user')->findOrFail($id);

        $trip = $booking->trip;

        $currentTravelers = $trip->bookedTravelers();

        if (($currentTravelers + $booking->traveler_count) > $trip->max_travelers) {
            return response()->json([
                'message' => 'Trip capacity exceeded'
            ], 400);
        }

        $booking->update([
            'status' => 'confirmed'
        ]);

        Notification::create([

            'user_id' => $booking->user_id,

            'title' => 'Booking Confirmed',

            'type' => 'booking',

            'message' => 'Your booking for trip ' . $trip->title . ' has been confirmed',

            'link' => '/bookings/' . $booking->id

        ]);

        return response()->json([
            'message' => 'Booking confirmed'
        ]);
    }

    public function cancel($id)
    {

        $booking = Booking::with('trip')->findOrFail($id);

        $booking->update([
            'status' => 'cancelled'
        ]);

        Notification::create([

            'user_id' => $booking->user_id,
            'title' => 'Booking Cancelled',
            'type' => 'booking',
            'message' => 'Your booking for trip ' . $booking->trip->title . ' has been cancelled',
            'link' => '/bookings/' . $booking->id
        ]);

        return response()->json([
            'message' => 'Booking cancelled'
        ]);
    }
}
