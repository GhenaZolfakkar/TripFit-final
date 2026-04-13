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
}
