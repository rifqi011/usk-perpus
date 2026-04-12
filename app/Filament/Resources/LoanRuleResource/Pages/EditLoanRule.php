<?php

namespace App\Filament\Resources\LoanRuleResource\Pages;

use App\Filament\Resources\LoanRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoanRule extends EditRecord
{
    protected static string $resource = LoanRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
