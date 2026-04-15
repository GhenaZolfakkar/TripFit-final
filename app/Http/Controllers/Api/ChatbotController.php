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
            'message' => 'required|string|max:500'
        ]);

        $message = strtolower($request->message);

        // Extract user preferences
        $preferences = $this->extractPreferences($message);

        // Recommend trips from DB
        $recommendedTrips = $this->recommendTrips($preferences);

        if ($recommendedTrips->isNotEmpty()) {
            return response()->json([
                'type' => 'recommendations',
                'data' => $recommendedTrips
            ]);
        }

        // Check simple suggestions
        $trips = $this->checkTripSuggestion($message);

        if ($trips) {
            return response()->json([
                'type' => 'trips',
                'data' => $trips
            ]);
        }

        // Ask AI if nothing found
        return $this->askAI($message);
    }



    private function extractPreferences($message)
    {
        $preferences = [
            'budget' => null,
            'duration' => null,
            'travelers' => null,
            'type' => null
        ];

        if (preg_match('/\d+\s*(egp|pound|budget)/', $message, $matches)) {
            $preferences['budget'] = (int) filter_var($matches[0], FILTER_SANITIZE_NUMBER_INT);
        }

        if (preg_match('/\d+\s*(day|days)/', $message, $matches)) {
            $preferences['duration'] = (int) filter_var($matches[0], FILTER_SANITIZE_NUMBER_INT);
        }

        if (preg_match('/\d+\s*(people|person|travelers)/', $message, $matches)) {
            $preferences['travelers'] = (int) filter_var($matches[0], FILTER_SANITIZE_NUMBER_INT);
        }

        if (str_contains($message, 'beach')) {
            $preferences['type'] = 'beach';
        }

        if (str_contains($message, 'adventure')) {
            $preferences['type'] = 'adventure';
        }

        if (str_contains($message, 'culture')) {
            $preferences['type'] = 'cultural';
        }

        if (str_contains($message, 'desert')) {
            $preferences['type'] = 'desert';
        }

        return $preferences;
    }



    private function recommendTrips($preferences)
    {
        $query = Trip::with('category');

        if ($preferences['budget']) {
            $query->where('price', '<=', $preferences['budget']);
        }

        if ($preferences['duration']) {
            $query->where('duration', '<=', $preferences['duration']);
        }

        if ($preferences['type']) {
            $query->whereHas('category', function ($q) use ($preferences) {
                $q->where('name', $preferences['type']);
            });
        }

        return $query
            ->limit(5)
            ->get(['id', 'title', 'destination', 'price', 'duration']);
    }



    private function checkTripSuggestion($message)
    {
        $query = Trip::query();

        if (str_contains($message, 'cheap')) {
            $query->where('price', '<', 1000);
        }

        if (str_contains($message, 'alex')) {
            $query->where('destination', 'like', '%alex%');
        }

        if (str_contains($message, 'hurghada')) {
            $query->where('destination', 'like', '%hurghada%');
        }

        if (str_contains($message, 'sharm')) {
            $query->where('destination', 'like', '%sharm%');
        }

        return $query
            ->limit(5)
            ->get(['id', 'title', 'destination', 'price']);
    }



    private function askAI($message)
    {
        $model = "gemini-2.5-flash-lite";

        $dbTrips = Trip::select('title', 'destination', 'price')
            ->limit(10)
            ->get()
            ->toJson();

        $systemPrompt = "
You are a travel assistant chatbot for a travel platform called TripFit.

Rules:
- Recommend destinations ONLY in Egypt.
- Never suggest places outside Egypt.
- If the question is unrelated to travel say:
'Sorry, I can only help with travel related questions.'

Answer style:
- Maximum 2 sentences
- Simple English
- Short and clear

Egypt destinations you can recommend:
Hurghada, Sharm El Sheikh, Dahab, Marsa Alam, Alexandria, North Coast, Siwa, Luxor, Aswan.

Trips available in database:
$dbTrips
";

        $response = Http::post(
            "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . env('GEMINI_API_KEY'),
            [
                "contents" => [
                    [
                        "parts" => [
                            [
                                "text" => $systemPrompt . "\nUser question: " . $message
                            ]
                        ]
                    ]
                ]
            ]
        );

        $data = $response->json();

        $text = $data['candidates'][0]['content']['parts'][0]['text']
            ?? ($data['error']['message'] ?? "AI response error");

        return response()->json([
            'type' => 'ai',
            'data' => $text
        ]);
    }
}
