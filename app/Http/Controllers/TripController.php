<?php

namespace App\Http\Controllers;

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
        $trips = Trip::with('agency')
            ->where('agency_id', $this->agencyId())
            ->get();

        return response()->json($trips);
    }

    // =======================
    // SHOW
    // =======================
    public function show($id)
    {
        $trip = Trip::with('agency')
            ->where('id', $id)
            ->where('agency_id', $this->agencyId())
            ->firstOrFail();

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

    // =======================
    // UPDATE
    // =======================
    public function update(Request $request, $id)
    {
        $trip = Trip::where('id', $id)
            ->where('agency_id', $this->agencyId())
            ->firstOrFail();

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

        $validated['featured'] = $request->has('featured');

        $trip->update($validated);

        return response()->json(['message' => 'Updated successfully']);
    }

    // =======================
    // DELETE
    // =======================
    public function destroy($id)
    {
        $trip = Trip::where('id', $id)
            ->where('agency_id', $this->agencyId())
            ->firstOrFail();

        $trip->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}