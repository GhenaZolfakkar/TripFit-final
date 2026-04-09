<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;

class FaqController extends Controller
{
   
    public function index(Request $request)
    {
        $query = Faq::where('is_active', true)
            ->orderBy('display_order');

        
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        return response()->json([
            'data' => $query->get()
        ]);
    }

    
    public function show($id)
    {
        $faq = Faq::where('is_active', true)->findOrFail($id);

        return response()->json($faq);
    }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ]);

        $faq = Faq::create($validated);

        return response()->json($faq, 201);
    }

    
    public function update(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);

        $validated = $request->validate([
            'question' => 'sometimes|string|max:255',
            'answer' => 'sometimes|string',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ]);

        $faq->update($validated);

        return response()->json($faq);
    }

    
    public function destroy($id)
    {
        Faq::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }
}
