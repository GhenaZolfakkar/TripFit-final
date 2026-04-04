<?php

namespace App\Filament\Resources\AgencyInvitations\Pages;

use App\Filament\Resources\AgencyInvitations\AgencyInvitationResource;
use App\Models\AgencyInvitation;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class ListAgencyInvitations extends ListRecords
{
    protected static string $resource = AgencyInvitationResource::class;

    // 🔹 Header actions (Send Invitation button)
    protected function getHeaderActions(): array
    {
        $user = auth()->user();

        // Only agency owners can send invitations
        if ($user->type !== 'agency_owner') {
            return [];
        }

        return [
            Action::make('send_invitation')
                ->label('Send Invitation')
                ->form([
                    TextInput::make('email')
                        ->label('Invite Email')
                        ->email()
                        ->required(),
                ])
                ->action(function (array $data) use ($user) {

                    // 🔹 create invitation
                    $invitation = AgencyInvitation::create([
                        'agency_id' => $user->agency_id,
                        'email' => $data['email'],
                        'token' => Str::random(40),
                        'status' => 'pending',
                        'expires_at' => now()->addDays(2),
                    ]);

                    // 🔎 check if user exists
                    $userToNotify = User::where('email', $data['email'])->first();

                    if ($userToNotify) {

                        // 🔔 create database notification
                        Notification::create([
                            'user_id' => $userToNotify->id,
                            'type' => 'agency_invitation',
                            'title' => 'Agency Invitation',
                            'message' => 'You received an invitation to join an agency',
                            'link' => '/accept-invitation/' . $invitation->token,
                            'is_read' => false,
                        ]);
                    }

                    // 🔹 Filament popup notification
                    FilamentNotification::make()
                        ->title('Invitation sent successfully!')
                        ->body('Link: ' . url('/admin/accept-invitation?token=' . $invitation->token))
                        ->success()
                        ->send();
                }),
        ];
    }
    protected function getTableActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn() => auth()->user()->type === 'agency_owner'),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            DeleteBulkAction::make()
                ->visible(fn() => auth()->user()->type === 'agency_owner'),
        ];
    }
    protected static function getEloquentQuery()
    {
        $user = auth()->user();

        if ($user->type === 'agency_owner') {
            // Only invitations for this owner’s agency
            return parent::getEloquentQuery()
                ->where('agency_id', $user->agency_id);
        }

        if ($user->type === 'admin') {
            // Admin sees all invitations
            return parent::getEloquentQuery();
        }

        // Other users see nothing
        return parent::getEloquentQuery()->whereRaw('0 = 1');
    }
}
