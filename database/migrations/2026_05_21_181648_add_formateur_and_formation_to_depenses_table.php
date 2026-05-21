<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('depenses', function (Blueprint $table) {
            $table->foreignId('formation_id')->nullable()->after('piece_jointe')->constrained('formations')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->after('formation_id')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('depenses', function (Blueprint $table) {
            $table->dropForeign(['formation_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['formation_id', 'user_id']);
        });
    }
};
