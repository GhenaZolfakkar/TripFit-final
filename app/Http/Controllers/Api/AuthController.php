<?php
 
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

 
class AuthController extends Controller
{
    public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'middle_name' => 'nullable|string',
        'last_name' => 'required|string',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8|confirmed',
        'phone' => 'required',
        'date_of_birth' => 'nullable|date',
    ]);
 
    $user = User::create([
        'name' => $request->name,
        'middle_name' => $request->middle_name,
        'last_name' => $request->last_name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'phone' => $request->phone,
        'date_of_birth' => $request->date_of_birth,
        'account_type' => $request->account_type
    ]);
 
    $token = $user->createToken("auth_token")->plainTextToken;
 
    return response()->json([
        'user' => $user,
        'token' => $token
    ]);
}
 
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
 
        $user = User::where('email', $request->email)->first();
 
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (auth()->user()->status === 'blocked') {
    return response()->json([
        'message' => 'Your account is blocked'
    ], 403);
}
 
        $token = $user->createToken("auth_token")->plainTextToken;
 
        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }
 
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
 
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
