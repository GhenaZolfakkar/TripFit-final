<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AgencyInvitation;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AgencyInvitationController extends Controller
{
    // 📩 Send Invitation
    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = auth()->user();

        // Only agency can send
        if ($user->type !== 'agency') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $invitation = AgencyInvitation::create([
            'agency_id' => $user->agency_id,
            'email' => $request->email,
            'token' => Str::random(40),
            'expires_at' => Carbon::now()->addDays(2),
        ]);

        // 🔥 Here you can send email later

        return response()->json([
            'message' => 'Invitation sent successfully',
            'token' => $invitation->token
        ]);
    }

    // ✅ Accept Invitation
    public function accept($token)
    {
        $invitation = AgencyInvitation::where('token', $token)->first();

        if (!$invitation) {
            return response()->json(['message' => 'Invalid invitation'], 404);
        }

        if ($invitation->status === 'accepted') {
            return response()->json(['message' => 'Already accepted'], 400);
        }

        if ($invitation->expires_at && now()->greaterThan($invitation->expires_at)) {
            return response()->json(['message' => 'Invitation expired'], 400);
        }

        $user = auth()->user();

        // Attach user to agency
        $user->update([
            'agency_id' => $invitation->agency_id,
            'type' => 'agency'
        ]);

        $invitation->update([
            'status' => 'accepted'
        ]);

        return response()->json([
            'message' => 'Joined agency successfully'
        ]);
    }

    // 📄 List Invitations (for agency)
    public function index()
    {
        $user = auth()->user();

        if ($user->type !== 'agency') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $invitations = AgencyInvitation::where('agency_id', $user->agency_id)->get();

        return response()->json($invitations);
    }

    // ❌ Delete invitation
    public function destroy($id)
    {
        $invitation = AgencyInvitation::findOrFail($id);

        $user = auth()->user();

        if ($user->agency_id !== $invitation->agency_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $invitation->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}