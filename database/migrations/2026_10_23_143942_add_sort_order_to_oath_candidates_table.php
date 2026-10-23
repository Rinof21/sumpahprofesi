<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('oath_candidates', function (Blueprint $table) {
            $table->integer('sort_order')->default(1)->after('period_id');
        });

        // Backfill sequential sort_order for existing candidates grouped by period
        $periods = DB::table('oath_candidates')
            ->select('period_id')
            ->distinct()
            ->pluck('period_id');

        foreach ($periods as $periodId) {
            $candidates = DB::table('oath_candidates')
                ->where('period_id', $periodId)
                ->orderBy('id', 'asc')
                ->get();

            $order = 1;
            foreach ($candidates as $cand) {
                DB::table('oath_candidates')
                    ->where('id', $cand->id)
                    ->update(['sort_order' => $order++]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oath_candidates', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
