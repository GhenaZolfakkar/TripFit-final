<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Booking;
use App\Models\Trip;
use App\Models\User;

class ReviewController extends Controller
{
public function tripReviews($trip_id)
{
    $reviews = Review::where('trip_id', $trip_id)
        ->with('user:id,name')
        ->latest()
        ->get();

    return response()->json([
        'trip_id' => $trip_id,
        'reviews' => $reviews
    ]);
}
    public function store(Request $request)
    {

        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string'
        ]);

        $user = auth()->user();

        $booking = Booking::find($request->booking_id);


        if ($booking->user_id != $user->id) {
            return response()->json([
                'message' => 'Invalid booking'
            ], 403);
        }

        if ($booking->status != 'confirmed') {
            return response()->json([
                'message' => 'Booking must be confirmed'
            ], 400);
        }

        if (Review::where('booking_id', $booking->id)->exists()) {
            return response()->json([
                'message' => 'Already reviewed'
            ], 400);
        }

        $review = Review::create([
            'user_id' => $user->id,
            'booking_id' => $booking->id,
            'trip_id' => $booking->trip_id,
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment
        ]);

        $trip = Trip::find($booking->trip_id);

        $trip->rating = Review::where('trip_id', $trip->id)->avg('rating');

        $trip->save();

        return response()->json([
            'message' => 'Review submitted successfully',
            'review' => $review
        ]);
    }
}
