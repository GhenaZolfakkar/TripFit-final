<?php

namespace App\Filament\Resources\RefundRequests;

use App\Filament\Resources\RefundRequests\Pages\CreateRefundRequest;
use App\Filament\Resources\RefundRequests\Pages\EditRefundRequest;
use App\Filament\Resources\RefundRequests\Pages\ListRefundRequests;
use App\Filament\Resources\RefundRequests\Schemas\RefundRequestForm;
use App\Filament\Resources\RefundRequests\Tables\RefundRequestsTable;
use App\Models\RefundRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RefundRequestResource extends Resource
{
    protected static ?string $model = RefundRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'RefundRequest';

    public static function form(Schema $schema): Schema
    {
        return RefundRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RefundRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRefundRequests::route('/'),
            'create' => CreateRefundRequest::route('/create'),
            'edit' => EditRefundRequest::route('/{record}/edit'),
        ];
    }
}
