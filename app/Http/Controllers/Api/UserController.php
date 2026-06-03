<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function selectTier(Request $request)
{
    $request->validate([
        'tier' => 'required|in:basic,premium,exclusive'
    ]);

    $user = auth()->user();

    $user->update([
        'tier' => $request->tier
    ]);

    return response()->json([
        'message' => 'Tier updated successfully',
        'tier' => $user->tier
    ]);
}
public function activate(Request $request, $id)
{
    if ($request->user()->type !== 'admin') {
        return response()->json([
            'message' => 'Unauthorized.'
        ], 403);
    }

    $user = User::findOrFail($id);

    $user->update([
        'status' => 'active',
    ]);

    return response()->json([
        'message' => 'User activated successfully.',
        'data' => $user,
    ]);
}

public function suspend(Request $request, $id)
{
    if ($request->user()->type !== 'admin') {
        return response()->json([
            'message' => 'Unauthorized.'
        ], 403);
    }

    $user = User::findOrFail($id);

    $user->update([
        'status' => 'suspended',
    ]);

    return response()->json([
        'message' => 'User suspended successfully.',
        'data' => $user,
    ]);
}

public function block(Request $request, $id)
{
    if ($request->user()->type !== 'admin') {
        return response()->json([
            'message' => 'Unauthorized.'
        ], 403);
    }

    $user = User::findOrFail($id);

    $user->update([
        'status' => 'blocked',
    ]);

    return response()->json([
        'message' => 'User blocked successfully.',
        'data' => $user,
    ]);
}
}
