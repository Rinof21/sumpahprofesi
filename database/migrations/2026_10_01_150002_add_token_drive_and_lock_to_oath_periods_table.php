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
            $table->string('access_token')->nullable()->after('quarter_code');
            $table->text('drive_url')->nullable()->after('youtube_url');
            $table->boolean('is_locked')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oath_periods', function (Blueprint $table) {
            $table->dropColumn(['access_token', 'drive_url', 'is_locked']);
        });
    }
};
