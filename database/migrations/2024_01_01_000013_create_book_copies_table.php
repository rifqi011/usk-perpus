<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_copies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->string('barcode')->unique();
            $table->string('inventory_code')->unique();
            $table->date('acquisition_date');
            $table->enum('source', ['purchase', 'donation', 'other'])->default('purchase');
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('condition', ['new', 'good', 'fair', 'minor_damage', 'major_damage', 'lost'])->default('new');
            $table->enum('copy_status', ['available', 'borrowed', 'reserved', 'maintenance', 'lost', 'discarded'])->default('available');
            $table->timestamp('last_borrowed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('barcode');
            $table->index('inventory_code');
            $table->index('copy_status');
            $table->index('book_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_copies');
    }
};
