<?php

namespace App\Filament\Resources\LoanRuleResource\Pages;

use App\Filament\Resources\LoanRuleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLoanRule extends CreateRecord
{
    protected static string $resource = LoanRuleResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
