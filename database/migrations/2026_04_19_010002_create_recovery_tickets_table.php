<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recovery_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('tipe_recovery', ['lupa_password', 'lupa_username', 'lupa_keduanya'])->default('lupa_password');
            $table->enum('status', ['processing', 'sent', 'pending_admin', 'resolved', 'expired'])->default('processing');
            $table->string('token_reset', 64)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_tickets');
    }
};
