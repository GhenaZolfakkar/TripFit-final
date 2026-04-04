<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AgencyInvitation;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Notification;

class AgencyInvitationController extends Controller
{
    // 📩 Send Invitation (OWNER ONLY)
    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = auth()->user();

        // ❌ only owner
        if ($user->type !== 'agency_owner') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $agencyId = $user->agency_id;

        $invitation = AgencyInvitation::create([
            'agency_id' => $agencyId,
            'email' => $request->email,
            'token' => Str::random(40),
            'status' => 'pending',
            'expires_at' => now()->addDays(2),
        ]);

        // check if user exists
        $userToNotify = User::where('email', $request->email)->first();

        if ($userToNotify) {

            Notification::create([
                'user_id' => $userToNotify->id,
                'type' => 'agency_invitation',
                'title' => 'Agency Invitation',
                'message' => 'You received an invitation to join an agency',
                'link' => '/accept-invitation/' . $invitation->token,
                'is_read' => false,
            ]);
        }

        return response()->json([
            'message' => 'Invitation sent successfully',
            'invitation_link' => url('/accept-invitation/' . $invitation->token)
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

        if ($user->email !== $invitation->email) {
            return response()->json(['message' => 'This invitation is not for you'], 403);
        }

        $user->update([
            'agency_id' => $invitation->agency_id,
            'type' => 'agency_member'
        ]);

        $invitation->update([
            'status' => 'accepted'
        ]);

        Notification::create([
            'user_id' => $user->id,
            'type' => 'agency_join',
            'title' => 'Agency Joined',
            'message' => 'You have successfully joined the agency',
            'link' => url('/admin'),
            'is_read' => false,
        ]);

        return response()->json([
            'message' => 'Joined agency successfully'
        ]);
    }

    // 📄 List Invitations (OWNER ONLY)
    public function index()
    {
        $user = auth()->user();

        if ($user->type !== 'agency_owner') {
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
