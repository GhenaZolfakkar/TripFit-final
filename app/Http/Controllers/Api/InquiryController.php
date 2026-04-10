<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inquiry;

class InquiryController extends Controller
{
     public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if (auth()->check()) {
            $validated['user_id'] = auth()->id();
        }

        $inquiry = Inquiry::create($validated);

        return response()->json([
            'message' => 'Inquiry submitted successfully',
            'data' => $inquiry
        ], 201);
    }

    public function index()
    {
        return Inquiry::with(['user', 'repliedBy'])->latest()->get();
    }

    public function show($id)
    {
        return Inquiry::with(['user', 'repliedBy'])->findOrFail($id);
    }

   
    public function update(Request $request, $id)
    {
        if (auth()->user()->type !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $inquiry = Inquiry::findOrFail($id);

        $request->validate([
            'status' => 'required|in:open,in_progress,resolved',
            'reply' => 'nullable|string'
        ]);

        $inquiry->update([
            'status' => $request->status,
            'reply' => $request->reply,
            'replied_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Inquiry updated successfully',
            'data' => $inquiry
        ]);
    }
}
