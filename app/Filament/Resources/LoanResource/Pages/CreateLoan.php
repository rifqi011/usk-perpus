<?php

namespace App\Filament\Resources\LoanResource\Pages;

use App\Filament\Resources\LoanResource;
use App\Services\LoanService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateLoan extends CreateRecord
{
    protected static string $resource = LoanResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Data akan diproses di handleRecordCreation
        return $data;
    }
    
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            $loanService = app(LoanService::class);
            
            // Extract book_ids from repeater
            $bookIds = collect($data['book_copies'])
                ->pluck('book_id')
                ->toArray();
            
            $loan = $loanService->createLoan(
                member: \App\Models\MemberProfile::findOrFail($data['member_id']),
                bookIds: $bookIds,
                loanRuleId: $data['loan_rule_id'],
                creator: auth()->user(),
                notes: $data['notes'] ?? null,
                loanDate: $data['loan_date'] ?? now()
            );
            
            Notification::make()
                ->success()
                ->title('Peminjaman Berhasil')
                ->body("Kode peminjaman: {$loan->loan_code}")
                ->send();
            
            return $loan;
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Gagal Membuat Peminjaman')
                ->body($e->getMessage())
                ->send();
            
            throw $e;
        }
    }
}
