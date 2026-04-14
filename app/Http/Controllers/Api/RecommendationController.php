<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\SearchHistory;
use Carbon\Carbon;

class RecommendationController extends Controller
{
    public function recommend(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'min_budget' => 'required|numeric',
            'max_budget' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'travelers' => 'required|integer|min:1',
            'category_id' => 'nullable|exists:trip_categories,id',
            'agency_id' => 'nullable|exists:agencies,id',
        ]);

       SearchHistory::create([
    'user_id' => $user->id,
    'budget' => ($data['min_budget'] + $data['max_budget']) / 2,
    'duration' => Carbon::parse($data['start_date'])
        ->diffInDays(Carbon::parse($data['end_date'])),
    'no_of_travelers' => $data['travelers'],
    'trip_category' => $data['category_id'] ?? null,
]);
            
        


        $query = Trip::with('agency')
            ->where('status', 'active');

        if ($user->tier !== 'exclusive') {
            $query->where('tier', '!=', 'exclusive');
        }

        $query->whereBetween('price', [$data['min_budget'], $data['max_budget']])
            ->whereDate('start_date', '>=', $data['start_date'])
            ->whereDate('end_date', '<=', $data['end_date'])
            ->where('max_travelers', '>=', $data['travelers']);

        if (!empty($data['category_id'])) {
            $query->where('trip_category_id', $data['category_id']);
        }

        if (!empty($data['agency_id'])) {
            $query->where('agency_id', $data['agency_id']);
        }

        $exactTrips = $query->get()->map(function ($trip) use ($data) {

            $score = 0;

            $avgBudget = ($data['min_budget'] + $data['max_budget']) / 2;

            $score += max(0, 100 - abs($trip->price - $avgBudget));
            $score += ($trip->rating ?? 0) * 20;

            if ($trip->featured) {
                $score += 20;
            }

            $trip->score = $score;

            return $trip;
        })->sortByDesc('score')->values();

        if ($exactTrips->count() > 0) {
            return response()->json([
                'type' => 'exact',
                'message' => 'We found trips matching your exact filters.',
                'trips' => $exactTrips
            ]);
        }

        $relaxedQuery = Trip::with('agency')
            ->where('status', 'active');

        if ($user->tier !== 'exclusive') {
            $relaxedQuery->where('tier', '!=', 'exclusive');
        }

        if (!empty($data['category_id'])) {
            $relaxedQuery->where('trip_category_id', $data['category_id']);
        }

        if (!empty($data['agency_id'])) {
            $relaxedQuery->where('agency_id', $data['agency_id']);
        }

        $allTrips = $relaxedQuery->get();

        $relaxedTrips = $allTrips->map(function ($trip) use ($data) {

            $score = 0;

            $avgBudget = ($data['min_budget'] + $data['max_budget']) / 2;

            
            $score += max(0, 100 - abs($trip->price - $avgBudget));

           
            $tripStart = Carbon::parse($trip->start_date);
            $userStart = Carbon::parse($data['start_date']);

            $daysDiff = $tripStart->diffInDays($userStart);
            $score += max(0, 50 - $daysDiff);

           
            $score += ($trip->rating ?? 0) * 20;

           
            if ($trip->featured) {
                $score += 20;
            }

          
            if ($trip->max_travelers >= $data['travelers']) {
                $score += 10;
            }

            $trip->score = $score;
            $trip->match_percentage = min(100, round($score));

            return $trip;
        })
        ->sortByDesc('score')
        ->take(10)
        ->values();


        if ($relaxedTrips->isEmpty()) {
            return response()->json([
                'type' => 'no_results',
                'message' => 'No trips found even after relaxing filters.',
                'suggestions' => [
                    'Increase your budget range',
                    'Try different travel dates',
                    'Remove category or agency filter'
                ],
                'trips' => []
            ]);
        }


        return response()->json([
            'type' => 'relaxed',
            'message' => 'No exact matches found. Here are trips closest to your preferences.',
            'explanation' => [
                'We did not find exact matches.',
                'So we show you the closest available trips ranked by similarity.'
            ],
            'suggestions' => [
                'Increase your budget slightly',
                'Be flexible with travel dates',
                'Remove some filters'
            ],
            'trips' => $relaxedTrips
        ]);
    }
}