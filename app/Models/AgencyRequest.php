<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Agency;

class AgencyRequest extends Model
{
     protected $fillable = [
        
        'name',
        'middle_name',
        'last_name',
        'phone',
        'date_of_birth',
        'email',
        'password',
        'agency_name',
        'logo',
        'description',
        'website',
        'commission_rate',
        'contact_details',
        'business_license',
        'documentation_url',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

        protected static function booted()
    {
        static::updated(function ($request) {
            
            if ($request->wasChanged('status') && $request->status === 'approved') {
                DB::transaction(function () use ($request) {

                    $user = User::create([
                        'name' => $request->name,
                        'middle_name' => $request->middle_name,
                        'last_name' => $request->last_name,
                        'phone' => $request->phone,
                        'date_of_birth' => $request->date_of_birth,
                        'email' => $request->email,
                        'password' => Hash::make($request->password ),
                        'type' => 'agency_owner',
                    ]);

                    Agency::create([
                        'owner_id' => $user->id,
                        'agency_name' => $request->agency_name,
                        'logo' => $request->logo,
                        'description' => $request->description,
                        'website' => $request->website,
                        'commission_rate' => $request->commission_rate,
                        'contact_details' => $request->contact_details,
                        'business_license' => $request->business_license,
                        'documentation_url' => $request->documentation_url,
                        'verification_status' => 'approved',
                    ]); 

                    $dashboardUrl = url('/admin/login');
                    Mail::raw(
                        "Hello {$user->name},\n\nYour agency request has been approved! Login here: {$dashboardUrl}",
                        function ($message) use ($user) {
                            $message->to($user->email)
                                ->subject('Agency Approved');
                        }
                    );
                });
            }
            if ($request->wasChanged('status') && $request->status === 'rejected') {
                $feedback = $request->rejection_feedback ?? "Unfortunately, your agency request did not meet our requirements. Please review your information and try again or contact support for more details.";

                Mail::raw(
                    "Hello {$request->name},\n\nYour agency request has been rejected.\n\nReason: {$feedback}\n\nThank you for your understanding.",
                    function ($message) use ($request) {
                        $message->to($request->email)
                            ->subject('Agency Request Rejected');
                    }
                );
            }
        });
        
    }
}
