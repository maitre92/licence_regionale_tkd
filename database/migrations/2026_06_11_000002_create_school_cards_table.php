<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('school_cards')) {
            Schema::create('school_cards', function (Blueprint $table) {
                $table->id();
                $table->string('card_number')->unique();
                $table->string('matricule')->nullable()->index();
                $table->string('first_name');
                $table->string('last_name');
                $table->enum('gender', ['M', 'F']);
                $table->date('birth_date')->nullable();
                $table->string('birth_place')->nullable();
                $table->string('academy')->nullable();
                $table->string('cap')->nullable();
                $table->string('school_name')->nullable();
                $table->string('school_type')->nullable();
                $table->string('class_name')->nullable();
                $table->string('academic_year')->nullable();
                $table->string('photo_path')->nullable();
                $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
                $table->date('issued_at')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['status', 'class_name']);
                $table->index(['last_name', 'first_name']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('school_cards');
    }
};
