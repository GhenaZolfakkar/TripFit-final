<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Trip;

class ChatbotController extends Controller
{
    public function ask(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = strtolower($request->message);

        // 1️⃣ Parse user message into filters
        $filters = $this->parseMessageForFilters($message);

        // 2️⃣ Query DB with these filters
        $trips = $this->searchTrips($filters);

        if ($trips->isNotEmpty()) {
            return response()->json([
                'type' => 'trips',
                'data' => $trips
            ]);
        }

        // 3️⃣ If nothing found in DB, send to AI
        $aiResponse = $this->askAI(
            $message . "\n\nSuggest a trip even if there is no match in my DB."
        );

        return $aiResponse;
    }

    // Convert user message into DB filters
    private function parseMessageForFilters($message)
    {
        $filters = [];

        // Budget
        if (preg_match('/\bunder \$?(\d+)/', $message, $matches)) {
            $filters['price'] = (float)$matches[1];
        } elseif (str_contains($message, 'cheap')) {
            $filters['price'] = 1000; // default cheap
        }

        // Destination
        if (str_contains($message, 'beach')) {
            $filters['destination'] = '%beach%';
        } elseif (str_contains($message, 'mountain')) {
            $filters['destination'] = '%mountain%';
        }

        // Duration
        if (preg_match('/(\d+)\s*(day|days|week|weeks)/', $message, $matches)) {
            $days = (int)$matches[1];
            $filters['duration'] = $days;
        } elseif (str_contains($message, 'short')) {
            $filters['duration'] = 3; // short trips <= 3 days
        } elseif (str_contains($message, 'long')) {
            $filters['duration'] = 7; // long trips >= 7 days
        }

        return $filters;
    }

    // Search trips in DB based on parsed filters
    private function searchTrips($filters)
    {
        $query = Trip::query();

        if (isset($filters['price'])) {
            $query->where('price', '<=', $filters['price']);
        }

        if (isset($filters['destination'])) {
            $query->where('destination', 'like', $filters['destination']);
        }

        if (isset($filters['duration'])) {
            if (str_contains(request()->message, 'short')) {
                $query->where('duration', '<=', $filters['duration']);
            } elseif (str_contains(request()->message, 'long')) {
                $query->where('duration', '>=', $filters['duration']);
            } else {
                $query->where('duration', $filters['duration']);
            }
        }

        return $query->take(5)->get([
            'title',
            'destination',
            'price',
            'duration',
            'start_date',
            'end_date',
            'description'
        ]);
    }

    // AI fallback
    private function askAI($message)
    {
        $model = "gemini-2.5-flash-lite"; // free version
        $apiKey = env('GEMINI_API_KEY');

        $response = Http::post(
            "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
            [
                "contents" => [
                    [
                        "parts" => [
                            ["text" => $message]
                        ]
                    ]
                ]
            ]
        );

        $data = $response->json();

        $text = $data['candidates'][0]['content']['parts'][0]['text'] ??
            ($data['error']['message'] ?? "AI response error");

        return response()->json([
            'type' => 'ai',
            'data' => $text
        ]);
    }
}
