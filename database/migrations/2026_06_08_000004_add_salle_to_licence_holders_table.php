<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('licence_holders', function (Blueprint $table) {
            if (!Schema::hasColumn('licence_holders', 'salle')) {
                $table->string('salle')->nullable()->after('club');
            }
        });
    }

    public function down(): void
    {
        Schema::table('licence_holders', function (Blueprint $table) {
            if (Schema::hasColumn('licence_holders', 'salle')) {
                $table->dropColumn('salle');
            }
        });
    }
};
