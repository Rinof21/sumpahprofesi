<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('oath_candidates', function (Blueprint $table) {
            $table->boolean('is_oath_coordinator')->default(false)->after('speech_notes');
            $table->text('coordinator_notes')->nullable()->after('is_oath_coordinator');
        });
    }

    public function down(): void
    {
        Schema::table('oath_candidates', function (Blueprint $table) {
            $table->dropColumn(['is_oath_coordinator', 'coordinator_notes']);
        });
    }
};
