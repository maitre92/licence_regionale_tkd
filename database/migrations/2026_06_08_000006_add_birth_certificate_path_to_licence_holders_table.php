<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('licence_holders', function (Blueprint $table) {
            if (!Schema::hasColumn('licence_holders', 'birth_certificate_path')) {
                $table->string('birth_certificate_path')->nullable()->after('birth_act_certified_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('licence_holders', function (Blueprint $table) {
            if (Schema::hasColumn('licence_holders', 'birth_certificate_path')) {
                $table->dropColumn('birth_certificate_path');
            }
        });
    }
};
