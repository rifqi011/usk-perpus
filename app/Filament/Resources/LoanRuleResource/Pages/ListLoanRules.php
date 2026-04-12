<?php

namespace App\Filament\Resources\LoanRuleResource\Pages;

use App\Filament\Resources\LoanRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoanRules extends ListRecords
{
    protected static string $resource = LoanRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
