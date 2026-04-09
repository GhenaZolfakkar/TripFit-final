<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AgencyRequest;
use Illuminate\Support\Facades\Hash;

class AgencyRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:agency_requests,email|unique:users,email',
            'password' => 'required|min:6',
            'agency_name' => 'required|string',
            'description' => 'required|string',
            'website' => 'required|string',
            'commission_rate' => 'required|integer',
            'contact_details' => 'required|string',
            'business_license' => 'required',
            'documentation_url' => 'required|string',
        ]);

        $agencyRequest = AgencyRequest::create([
            'name' => $request->name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'email' => $request->email,
            'password' => $request->password,

            'agency_name' => $request->agency_name,
            'logo' => $request->logo,
            'description' => $request->description,
            'website' => $request->website,
            'commission_rate' => $request->commission_rate ?? 0,
            'contact_details' => $request->contact_details,
            'business_license' => $request->business_license,
            'documentation_url' => $request->documentation_url,
        ]);

        return response()->json([
            'message' => 'Agency request submitted successfully',
            'data' => $agencyRequest,
        ]);
    }
}
