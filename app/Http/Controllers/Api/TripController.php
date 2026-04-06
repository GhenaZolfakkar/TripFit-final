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
 
    // =======================
    // GET ALL TRIPS
    // =======================
   public function index()
{
    $user = auth()->user();
 
    // ✅ user & admin يشوفوا كل التريبس
    if (in_array($user->type, ['admin', 'user'])) {
        $trips = Trip::with('agency')->get();
    } else {
        // ✅ owner & member يشوفوا بتاعتهم بس
        $trips = Trip::with('agency')
            ->where('agency_id', $user->agency_id)
            ->get();
    }
 
    return response()->json($trips);
}
 
    // =======================
    // SHOW
    // =======================
   public function show($id)
{
    $user = auth()->user();
 
    $query = Trip::with('agency')->where('id', $id);
 
    // لو مش admin أو user → فلترة
    if (!in_array($user->type, ['admin', 'user'])) {
        $query->where('agency_id', $user->agency_id);
    }
 
    $trip = $query->firstOrFail();
 
    return response()->json($trip);
}
 
    // =======================
    // STORE
    // =======================
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
 
    // =======================
    // DELETE
    // =======================
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
}
