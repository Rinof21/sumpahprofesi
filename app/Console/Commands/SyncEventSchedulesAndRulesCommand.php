<?php

namespace App\Console\Commands;

use App\Models\EventRule;
use App\Models\EventSchedule;
use App\Models\OathPeriod;
use Illuminate\Console\Command;

class SyncEventSchedulesAndRulesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oath:sync-templates {--force : Timpa susunan acara dan tata tertib yang sudah ada}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronkan template susunan acara dan tata tertib resmi prodi untuk semua periode sumpah';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai sinkronisasi susunan acara & tata tertib untuk semua periode sumpah...');

        $periods = OathPeriod::with('studyProgram')->get();

        if ($periods->isEmpty()) {
            $this->warn('Tidak ada periode sumpah yang ditemukan.');

            return self::SUCCESS;
        }

        $force = (bool) $this->option('force');
        $synced = 0;
        $skipped = 0;

        $rows = [];

        foreach ($periods as $period) {
            $hasSchedules = $period->schedules()->exists();
            $hasRules = $period->rules()->exists();

            if (! $force && $hasSchedules && $hasRules) {
                $skipped++;
                $rows[] = [
                    $period->id,
                    $period->name,
                    $period->studyProgram->name ?? '-',
                    $period->schedules()->count(),
                    $period->rules()->count(),
                    'Lewati (Sudah ada data)',
                ];

                continue;
            }

            if ($period->is_locked && ! $force) {
                $skipped++;
                $rows[] = [
                    $period->id,
                    $period->name,
                    $period->studyProgram->name ?? '-',
                    $period->schedules()->count(),
                    $period->rules()->count(),
                    'Dilewati (Periode Dikunci)',
                ];

                continue;
            }

            if ($force || ! $hasSchedules) {
                $period->schedules()->delete();
                $prodiCode = $period->studyProgram->code ?? 'KED';
                foreach (EventSchedule::getDefaultTemplateForProdi($prodiCode) as $sched) {
                    $period->schedules()->create($sched);
                }
            }

            if ($force || ! $hasRules) {
                $period->rules()->delete();
                $prodiCode = $period->studyProgram->code ?? 'KED';
                foreach (EventRule::getDefaultTemplateForProdi($prodiCode) as $rul) {
                    $period->rules()->create($rul);
                }
            }

            $synced++;
            $rows[] = [
                $period->id,
                $period->name,
                $period->studyProgram->name ?? '-',
                $period->schedules()->count(),
                $period->rules()->count(),
                'Berhasil Disinkronkan',
            ];
        }

        $this->table(
            ['ID', 'Nama Periode', 'Program Studi', 'Agenda Acara', 'Tata Tertib', 'Status Sinkronisasi'],
            $rows
        );

        $this->info("Selesai! {$synced} periode disinkronkan, {$skipped} periode dilewati.");

        return self::SUCCESS;
    }
}
