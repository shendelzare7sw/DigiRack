<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('security_question')->nullable()->after('is_active');
            $table->string('security_answer')->nullable()->after('security_question');
            $table->string('security_pin')->nullable()->after('security_answer');
            $table->timestamp('last_login_at')->nullable()->after('security_pin');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
        });

        // Backfill existing users with auto username
        $users = \App\Models\User::whereNull('username')->get();
        foreach ($users as $user) {
            $user->username = 'user_' . strtolower(\Illuminate\Support\Str::random(8));
            $user->save();
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'security_question', 'security_answer', 'security_pin', 'last_login_at', 'last_login_ip']);
        });
    }
};
