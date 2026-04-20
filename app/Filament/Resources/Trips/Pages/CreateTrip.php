<?php

namespace App\Filament\Resources\Trips\Pages;

use App\Filament\Resources\Trips\TripResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTrip extends CreateRecord
{
    protected static string $resource = TripResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

      
        if ($user->type !== 'admin') {
            $data['agency_id'] = $user->agency_id;
        }

        
        if (isset($data['images']) && is_array($data['images'])) {
            $images = [];

            foreach ($data['images'] as $file) {
                if ($file) {
                    $images[] =
                        'data:' . $file->getMimeType() .
                        ';base64,' .
                        base64_encode(file_get_contents($file->getRealPath()));
                }
            }

            $data['images'] = $images;
        }

      
        if (isset($data['videos']) && is_array($data['videos'])) {
            $videos = [];

            foreach ($data['videos'] as $file) {
                if ($file) {
                    $videos[] =
                        'data:' . $file->getMimeType() .
                        ';base64,' .
                        base64_encode(file_get_contents($file->getRealPath()));
                }
            }

            $data['videos'] = $videos;
        }

        return $data;
    }
}