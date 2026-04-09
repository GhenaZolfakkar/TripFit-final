<?php
 
namespace App\Filament\Resources\AgencyRequests\Tables;
 
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use App\Models\User;
use App\Models\Agency;
use Illuminate\Support\Facades\Mail;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Support\Facades\DB;
 
class AgencyRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),
                TextColumn::make('agency_name')->sortable()->searchable(),
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->sortable(),
        
IconColumn::make('approve')
    ->label('Approve')
    ->color('success')
    ->icon('heroicon-o-check')
    ->visible(fn($record) => $record && $record->status === 'pending')
    ->action(function ($record) {
        DB::transaction(function () use ($record) {
            $user = User::create([
                'name' => $record->name,
                'middle_name' => $record->middle_name,
                'last_name' => $record->last_name,
                'phone' => $record->phone,
                'date_of_birth' => $record->date_of_birth,
                'email' => $record->email,
                'password' =>Hash::make($record->password ),
                'type' => 'agency_owner',
            ]);
            $agency = Agency::create([
                'owner_id' => $user->id,
                'agency_name' => $record->agency_name,
                'logo' => $record->logo,
                'description' => $record->description,
                'website' => $record->website,
                'commission_rate' => $record->commission_rate,
                'contact_details' => $record->contact_details,
                'business_license' => $record->business_license,
                'documentation_url' => $record->documentation_url,
                'verification_status' => 'approved',
            ]);
            $record->update(['status' => 'approved']);
            $dashboardUrl = url('/admin');
            \Mail::raw(
                "Hello {$user->name}, your agency request has been approved! Login here: {$dashboardUrl}",
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Agency Approved');
                }
            );
        });
    }),
IconColumn::make('reject')
    ->label('Reject')
    ->color('danger')
    ->icon('heroicon-o-x')
    ->visible(fn($record) => $record && $record->status === 'pending')
    ->action(fn($record) => $record->update(['status' => 'rejected'])),
            ])
               

            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}