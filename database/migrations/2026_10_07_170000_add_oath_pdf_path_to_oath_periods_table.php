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
        Schema::table('oath_periods', function (Blueprint $table) {
            $table->string('oath_pdf_path')->nullable()->after('youtube_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oath_periods', function (Blueprint $table) {
            $table->dropColumn('oath_pdf_path');
        });
    }
};
