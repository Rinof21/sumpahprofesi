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
        Schema::create('oath_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('oath_periods')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nim')->unique();
            $table->string('nik');
            $table->string('full_name');
            $table->string('birth_place');
            $table->date('birth_date');
            $table->string('father_name');
            $table->string('mother_name');
            $table->string('admission_path');
            $table->string('religion');
            $table->boolean('agreed_rules')->default(false);
            $table->timestamp('agreed_at')->nullable();
            $table->string('ppt_file_path')->nullable();
            $table->boolean('is_speech_rep')->default(false);
            $table->text('speech_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oath_candidates');
    }
};
