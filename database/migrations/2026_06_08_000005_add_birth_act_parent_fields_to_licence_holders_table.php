<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('licence_holders', function (Blueprint $table) {
            $table->string('birth_act_number')->nullable()->after('domicile');
            $table->string('nina')->nullable()->after('birth_act_number');
            $table->string('birth_act_region')->nullable()->after('nina');
            $table->string('birth_act_cercle')->nullable()->after('birth_act_region');
            $table->string('birth_act_arrondissement')->nullable()->after('birth_act_cercle');
            $table->string('birth_act_commune')->nullable()->after('birth_act_arrondissement');
            $table->string('birth_act_center')->nullable()->after('birth_act_commune');
            $table->string('father_first_name')->nullable()->after('birth_act_center');
            $table->string('father_last_name')->nullable()->after('father_first_name');
            $table->string('father_profession')->nullable()->after('father_last_name');
            $table->string('father_domicile')->nullable()->after('father_profession');
            $table->string('mother_first_name')->nullable()->after('father_domicile');
            $table->string('mother_last_name')->nullable()->after('mother_first_name');
            $table->string('mother_profession')->nullable()->after('mother_last_name');
            $table->string('mother_domicile')->nullable()->after('mother_profession');
            $table->string('civil_officer_name')->nullable()->after('mother_domicile');
            $table->string('civil_officer_quality')->nullable()->after('civil_officer_name');
            $table->date('birth_act_established_at')->nullable()->after('civil_officer_quality');
            $table->date('birth_act_certified_at')->nullable()->after('birth_act_established_at');
            $table->string('birth_certificate_path')->nullable()->after('birth_act_certified_at');
        });
    }

    public function down(): void
    {
        Schema::table('licence_holders', function (Blueprint $table) {
            $table->dropColumn([
                'birth_act_number',
                'nina',
                'birth_act_region',
                'birth_act_cercle',
                'birth_act_arrondissement',
                'birth_act_commune',
                'birth_act_center',
                'father_first_name',
                'father_last_name',
                'father_profession',
                'father_domicile',
                'mother_first_name',
                'mother_last_name',
                'mother_profession',
                'mother_domicile',
                'civil_officer_name',
                'civil_officer_quality',
                'birth_act_established_at',
                'birth_act_certified_at',
                'birth_certificate_path',
            ]);
        });
    }
};
