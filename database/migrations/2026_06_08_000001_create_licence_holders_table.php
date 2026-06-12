<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('licence_holders')) {
            Schema::create('licence_holders', function (Blueprint $table) {
                $table->id();
                $table->string('licence_number')->unique();
                $table->string('first_name');
                $table->string('last_name');
                $table->enum('gender', ['M', 'F']);
                $table->date('birth_date')->nullable();
                $table->string('phone')->nullable();
                $table->string('grade')->nullable();
                $table->string('club')->nullable();
                $table->string('photo_path')->nullable();
                $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
                $table->date('issued_at')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['status', 'club']);
                $table->index(['last_name', 'first_name']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('licence_holders');
    }
};
