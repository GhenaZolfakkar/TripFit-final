<?php

namespace App\Filament\Resources\Trips\Pages;

use App\Filament\Resources\Trips\TripResource;
use Filament\Resources\Pages\CreateRecord;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class CreateTrip extends CreateRecord
{
    protected static string $resource = TripResource::class;

   protected function mutateFormDataBeforeCreate(array $data): array
{
    $user = auth()->user();

    if ($user->type !== 'admin') {
        $data['agency_id'] = $user->agency_id;
    }

    if (isset($data['images'])) {
        $images = [];

        foreach ($data['images'] as $file) {
            $uploaded = Cloudinary::upload($file->getRealPath());

            $images[] = $uploaded->getSecurePath();
        }

        $data['images'] = $images;
    }

   
    if (isset($data['videos'])) {
        $videos = [];

        foreach ($data['videos'] as $file) {
            $uploaded = Cloudinary::uploadVideo($file->getRealPath());

            $videos[] = $uploaded->getSecurePath();
        }

        $data['videos'] = $videos;
    }

    return $data;
}
}