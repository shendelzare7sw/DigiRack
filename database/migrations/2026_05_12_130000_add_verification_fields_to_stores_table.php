<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            if (!Schema::hasColumn('stores', 'identity_document_path')) {
                $table->string('identity_document_path')->nullable()->after('banner');
            }
            if (!Schema::hasColumn('stores', 'identity_submitted_at')) {
                $table->timestamp('identity_submitted_at')->nullable()->after('identity_document_path');
            }
            if (!Schema::hasColumn('stores', 'verification_status')) {
                $table->string('verification_status', 20)->default('pending')->after('is_verified');
            }
            if (!Schema::hasColumn('stores', 'verification_notes')) {
                $table->text('verification_notes')->nullable()->after('verification_status');
            }
            if (!Schema::hasColumn('stores', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verification_notes');
            }
        });

        DB::table('stores')
            ->where('is_verified', true)
            ->update([
                'verification_status' => 'approved',
                'is_active' => true,
                'verified_at' => now(),
            ]);

        DB::table('stores')
            ->where('is_verified', false)
            ->where(function ($query) {
                $query->whereNull('verification_status')
                    ->orWhere('verification_status', '');
            })
            ->update(['verification_status' => 'pending']);
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            if (Schema::hasColumn('stores', 'verified_at')) {
                $table->dropColumn('verified_at');
            }
            if (Schema::hasColumn('stores', 'verification_notes')) {
                $table->dropColumn('verification_notes');
            }
            if (Schema::hasColumn('stores', 'verification_status')) {
                $table->dropColumn('verification_status');
            }
            if (Schema::hasColumn('stores', 'identity_submitted_at')) {
                $table->dropColumn('identity_submitted_at');
            }
            if (Schema::hasColumn('stores', 'identity_document_path')) {
                $table->dropColumn('identity_document_path');
            }
        });
    }
};
