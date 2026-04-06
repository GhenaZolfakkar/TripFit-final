<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Trip;

class ChatbotController extends Controller
{
    // دالة رئيسية لتلقي رسالة المستخدم
    public function ask(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = $request->message;

        // أولاً: البحث في DB
        $trips = $this->checkTripSuggestion($message);

        if ($trips) {
            return response()->json([
                'type' => 'trips',
                'data' => $trips
            ]);
        }

        // ثانياً: لو مفيش رحلات مناسبة، نرسل للـ AI
        $aiResponse = $this->askAI(
            $message . "\n\nAlso, please check if there are any trips in my DB that match."
        );

        return $aiResponse;
    }

    // دالة لفحص الرسائل المتعلقة بالرحلات
    private function checkTripSuggestion($message)
    {
        $message = strtolower($message);

        $query = Trip::query();

        // مثال على كلمات مفتاحية:
        if (str_contains($message, 'cheap')) {
            $query->where('price', '<', 1000); // حسب الـ DB عندك
        }

        if (str_contains($message, 'beach')) {
            $query->where('destination', 'like', '%beach%');
        }

        if (str_contains($message, 'mountain')) {
            $query->where('destination', 'like', '%mountain%');
        }

        $trips = $query->take(5)->get(['title', 'destination', 'price']);

        return $trips->isNotEmpty() ? $trips : null;
    }

    // دالة التواصل مع AI
    private function askAI($message)
    {
        $model = "gemini-2.5-flash-lite"; // النسخة المجانية

        $response = Http::post(
            "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . env('GEMINI_API_KEY'),
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
