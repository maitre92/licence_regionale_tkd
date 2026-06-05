<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groupe_formation_formateur', function (Blueprint $table) {
            $table->string('commission_type', 20)->default('pourcentage')->after('taux_commission');
            $table->decimal('montant_commission', 12, 2)->nullable()->after('commission_type');
        });
    }

    public function down(): void
    {
        Schema::table('groupe_formation_formateur', function (Blueprint $table) {
            $table->dropColumn(['commission_type', 'montant_commission']);
        });
    }
};
