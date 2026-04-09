<?php

namespace App\Filament\Resources\Agencies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use App\Models\User;
use Filament\Forms\Components\FileUpload;

class AgencyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // 👤 OWNER (Admin only)
                Select::make('owner_id')
    ->label('Agency Owner')
    ->relationship(
        'owner',
        'name',
        fn($query, $record) => $query->where('type', 'user')
            // include current owner even if type is not 'user'
            ->orWhere('id', $record?->owner_id)
    )
    ->preload()
    ->required()
    ->disabled(fn() => auth()->user()->type === 'admin'),

                // 👤 AUTO OWNER (لو مش admin)
                Hidden::make('owner_id')
                    ->default(fn() => auth()->id())
                    ->visible(fn() => auth()->user()->type !== 'admin'),

                // 🏢 Agency Name
                TextInput::make('agency_name')
                    ->required()
                    ->maxLength(255),

                // 🖼 Logo
                FileUpload::make('logo')
                    ->image()
                    ->directory('agency')
                    ->nullable(),

                // 📝 Description
                Textarea::make('description')
                    ->columnSpanFull(),

                // 🌐 Website
                TextInput::make('website')
                    ->url()
                    ->nullable(),

                // 💰 Commission
                TextInput::make('commission_rate')
                    ->numeric()
                    ->default(0)
                    ->suffix('%'),

                // ⭐ Rating (disabled)
                TextInput::make('rating')
                    ->numeric()
                    ->default(0),

                // 📞 Contact
                Textarea::make('contact_details'),

                // 📄 Business License
                FileUpload::make('business_license')
                    ->nullable(),

                // 🔗 Documentation
                TextInput::make('documentation_url')
                    ->url()
                    ->nullable(),

                // ✅ Verification
                Select::make('verification_status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->visible(fn() => auth()->user()->type === 'admin'),
            ]);
    }
}
