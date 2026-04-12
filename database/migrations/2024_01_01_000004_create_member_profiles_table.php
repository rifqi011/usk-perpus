<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('member_code')->unique();
            $table->string('identity_number')->unique();
            $table->string('full_name');
            $table->enum('gender', ['L', 'P']);
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->string('photo')->nullable();
            $table->date('registration_date');
            $table->enum('membership_status', ['pending', 'active', 'suspended', 'inactive'])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            $table->unique('user_id');
            $table->index('member_code');
            $table->index('membership_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_profiles');
    }
};
