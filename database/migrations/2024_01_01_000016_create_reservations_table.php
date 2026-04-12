<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('member_profiles')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->timestamp('reservation_date');
            $table->timestamp('expire_at')->nullable();
            $table->enum('status', ['waiting', 'ready', 'cancelled', 'fulfilled', 'expired'])->default('waiting');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('member_id');
            $table->index('book_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
