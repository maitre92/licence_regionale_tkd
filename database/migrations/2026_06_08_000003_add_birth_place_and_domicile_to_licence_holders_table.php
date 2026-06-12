<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('licence_holders', function (Blueprint $table) {
            if (!Schema::hasColumn('licence_holders', 'birth_place')) {
                $table->string('birth_place')->nullable()->after('birth_date');
            }
            if (!Schema::hasColumn('licence_holders', 'domicile')) {
                $table->string('domicile')->nullable()->after('club');
            }
        });
    }

    public function down(): void
    {
        Schema::table('licence_holders', function (Blueprint $table) {
            if (Schema::hasColumn('licence_holders', 'birth_place') || Schema::hasColumn('licence_holders', 'domicile')) {
                $table->dropColumn(array_filter([
                    Schema::hasColumn('licence_holders', 'birth_place') ? 'birth_place' : null,
                    Schema::hasColumn('licence_holders', 'domicile') ? 'domicile' : null,
                ]));
            }
        });
    }
};
