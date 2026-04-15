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

    if ($this->isQuestion($message)) {
        return $this->askAI($message);
    }


    $trips = $this->checkTripSuggestion($message);

    if ($trips) {
        return response()->json([
            'type' => 'trips',
            'data' => $trips
        ]);
    }

   
    return $this->askAI($message);
}


   private function checkTripSuggestion($message)
{
    $query = Trip::query();
    $hasFilter = false; 


    if (str_contains($message, 'cheap') || str_contains($message, 'low budget')) {
        $query->where('price', '<', 8000);
        $hasFilter = true;
    }


    if (str_contains($message, 'alex')) {
        $query->where('destination', 'like', '%alex%');
        $hasFilter = true;
    }

    if (str_contains($message, 'beach')) {
        $query->where('destination', 'like', '%beach%');
        $hasFilter = true;
    }

    if (str_contains($message, 'mountain')) {
        $query->where('destination', 'like', '%mountain%');
        $hasFilter = true;
    }

    if (str_contains($message, 'adventure')) {
        $query->where('trip_category_id', 'adventure');
        $hasFilter = true;
    }


    if (!$hasFilter) {
        return null;
    }

    $trips = $query
        ->select('id','title','destination','price')
        ->limit(5)
        ->get();

    return $trips->isNotEmpty() ? $trips : null;
}

    private function askAI($message)
    {
        $model = "gemini-2.5-flash-lite";

        $dbTrips = Trip::select('title','destination','price')
            ->limit(10)
            ->get()
            ->toJson();

        $systemPrompt = "
You are a travel assistant chatbot for a travel booking platform called TripFit.

Important rules:
- Recommend destinations ONLY in Egypt.
- Never suggest places outside Egypt.
- If the user asks for travel advice, suggest Egyptian destinations only.
- If the question is unrelated to travel say:
'Sorry, I can only help with travel related questions.'

Answer style:
- Maximum 2 sentences
- Simple English
- Short and clear

Example Egyptian destinations you can recommend:
Hurghada, Sharm El Sheikh, Dahab, Alexandria, Marsa Alam, Siwa Oasis, Luxor, Aswan.

Available trips in database:
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
    private function isQuestion($message)
{
    return str_contains($message, 'what') ||
           str_contains($message, 'when') ||
           str_contains($message, 'how') ||
           str_contains($message, 'where') ||
           str_contains($message, 'best') ||
           str_contains($message, 'weather') ||
           str_contains($message, '?');
}
}