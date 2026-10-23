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
        Schema::create('documentation_vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_program_id')->nullable()->constrained('study_programs')->nullOnDelete();
            $table->string('category')->default('photographer'); // 'photographer' or 'live_streaming'
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('whatsapp_number');
            $table->string('instagram_handle')->nullable();
            $table->string('youtube_channel')->nullable();
            $table->string('price_estimate')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentation_vendors');
    }
};
