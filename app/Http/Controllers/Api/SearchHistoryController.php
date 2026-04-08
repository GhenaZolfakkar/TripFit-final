<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SearchHistory;

class SearchHistoryController extends Controller
{
    private function user()
    {
        return auth()->user();
    }

    // =========================
    // 📄 GET MY HISTORY
    // =========================
    public function index()
    {
        $user = $this->user();

        $history = SearchHistory::where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json($history);
    }

    // =========================
    // ➕ STORE SEARCH
    // =========================
    public function store(Request $request)
    {
        $user = $this->user();

        $validated = $request->validate([
            'budget' => 'nullable|numeric|min:0',
            'duration' => 'nullable|integer|min:1',
            'no_of_travelers' => 'nullable|integer|min:1',
            'trip_category' => 'nullable|string|max:100',
        ]);

        $validated['user_id'] = $user->id;

        $search = SearchHistory::create($validated);

        return response()->json([
            'message' => 'Search saved successfully',
            'data' => $search
        ], 201);
    }

    // =========================
    // ❌ DELETE ONE
    // =========================
    public function destroy($id)
    {
        $user = $this->user();

        $search = SearchHistory::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $search->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }

    // =========================
    // 🧹 CLEAR ALL
    // =========================
    public function clear()
    {
        $user = $this->user();

        SearchHistory::where('user_id', $user->id)->delete();

        return response()->json(['message' => 'All history cleared']);
    }
}
