<?php

namespace App\Filament\Resources\LoanResource\Pages;

use App\Filament\Resources\LoanResource;
use App\Models\LoanRule;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoan extends EditRecord
{
    protected static string $resource = LoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => $this->record->status->value === 'borrowed'),
            Actions\ViewAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Tidak perlu isi book_copies karena section buku disembunyikan saat edit
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Recalculate due_date jika loan_rule atau loan_date berubah
        $loanRule = LoanRule::find($data['loan_rule_id']);
        if ($loanRule) {
            $loanDate = Carbon::parse($data['loan_date']);
            $data['due_date'] = $loanRule->calculateDueDate($loanDate->toDateTime());
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
