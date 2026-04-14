<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;


class TripController extends Controller
{
     private function agencyId()
    {
        return auth()->user()->agency_id;
    }
 

public function index()
{
    $user = auth()->user();

    $query = Trip::with('agency');

    if (!in_array($user->type, ['admin', 'user'])) {
        $query->where('agency_id', $user->agency_id);
    }

    if ($user->tier !== 'exclusive') {
        $query->where('tier', '!=', 'exclusive');
    }

    $trips = $query->get();

    return response()->json([
        'trips' => $trips,
        'user_tier' => $user->tier
    ]);
}
 
   
  public function show($id)
{
    $user = auth()->user();

    $query = Trip::with('agency')->where('id', $id);

    if (!in_array($user->type, ['admin', 'user'])) {
        $query->where('agency_id', $user->agency_id);
    }

    $trip = $query->firstOrFail();

    if ($trip->tier === 'exclusive' && $user->tier !== 'exclusive') {
        return response()->json([
            'message' => 'This trip is available only for exclusive users'
        ], 403);
    }

    return response()->json([
        'trip' => $trip,
        'remaining_seats' => $trip->remainingSeats()
    ]);
}
 
 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'destination' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'max_traveler' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'rating' => 'nullable|numeric|min:0|max:5',
            'trip_category_id' => 'required|exists:trip_categories,id',
            'status' => 'required|string',
            'featured' => 'nullable|boolean',
        ]);
 
        $validated['agency_id'] = $this->agencyId();
        $validated['featured'] = $request->has('featured');
 
        $trip = Trip::create($validated);
 
        return response()->json([
            'message' => 'Trip created successfully',
            'data' => $trip
        ], 201);
    }
 
    public function update(Request $request, $id)
    {
        $user = $this->user();
 
        if (in_array($user->type, ['user', 'agency_owner'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
 
        $query = Trip::where('id', $id);
 
        if ($user->type !== 'admin') {
            $query->where('agency_id', $user->agency_id);
        }
 
        $trip = $query->firstOrFail();
 
        $trip->update($request->all());
 
        return response()->json(['message' => 'Updated']);
    }
 
    public function destroy($id)
    {
        $user = $this->user();
 
        if (in_array($user->type, ['user', 'agency_owner'])) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
 
        $query = Trip::where('id', $id);
 
        if ($user->type !== 'admin') {
            $query->where('agency_id', $user->agency_id);
        }
 
        $trip = $query->firstOrFail();
 
        $trip->delete();
 
        return response()->json(['message' => 'Deleted']);
    }

    public function premiumFeaturedTrips()
{
    $user = auth()->user();

    if (!in_array($user->tier, ['premium', 'exclusive'])) {
        return response()->json([
            'message' => 'This section is available for premium users only.'
        ], 403);
    }

    $trips = Trip::with('agency')
        ->where('status', 'active')
        ->where('featured', true)
        ->get()
        ->map(function ($trip) use ($user) {

            $score = 0;

            $score += 50;

            if ($user->tier === 'premium') {
                $score += 20;
            }

            if ($user->tier === 'exclusive') {
                $score += 30;
            }

            $trip->priority_exposure = [
                'homepage_boost' => true,
                'top_search_placement' => true,
                'recommended_section' => true,
            ];

            $trip->score = $score;

            return $trip;
        })
        ->sortByDesc('score')
        ->values();

    return response()->json([
        'type' => 'premium_featured',
        'message' => 'These featured trips reflect your premium priority exposure across homepage, search, and recommendations.',
        'explanation' => [
            'As a premium user, you get higher visibility for featured trips.',
            'These trips are boosted across homepage, search results, and recommendations.'
        ],
        'trips' => $trips
    ]);
}
}
