<?php

namespace App\Filament\Resources\MemberProfileResource\Pages;

use App\Filament\Resources\MemberProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMemberProfile extends ViewRecord
{
    protected static string $resource = MemberProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
