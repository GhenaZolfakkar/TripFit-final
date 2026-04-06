<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TripCategory;

class TripCategoryController extends Controller
{
    public function index()
    {
        return response()->json(TripCategory::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = TripCategory::create($validated);

        return response()->json($category, 201);
    }

    public function show($id)
    {
        return response()->json(
            TripCategory::findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $category = TripCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return response()->json(['message' => 'Updated']);
    }

    public function destroy($id)
    {
        TripCategory::findOrFail($id)->delete();

        return response()->json(['message' => 'Deleted']);
    }
}