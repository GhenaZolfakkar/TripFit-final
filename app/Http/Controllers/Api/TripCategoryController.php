<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TripCategory;

class TripCategoryController extends Controller
{
        private function user()
    {
        return auth()->user();
    }
 
    public function index()
    {
        return TripCategory::all();
    }
 public function show($id)
{
    $category = TripCategory::findOrFail($id);

    return response()->json($category);
}
    public function store(Request $request)
    {
        $user = $this->user();
 
        if ($user->type === 'agency_owner') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
 
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
 
        return TripCategory::create($validated);
    }
 
    public function update(Request $request, $id)
    {
        $user = $this->user();
 
        if ($user->type === 'agency_owner') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
 
        $category = TripCategory::findOrFail($id);
 
        $category->update($request->all());
 
        return response()->json(['message' => 'Updated']);
    }
 
    public function destroy($id)
    {
        $user = $this->user();
 
        if ($user->type === 'agency_owner') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
 
        TripCategory::findOrFail($id)->delete();
 
        return response()->json(['message' => 'Deleted']);
    }
}

