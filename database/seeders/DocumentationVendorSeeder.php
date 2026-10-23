<?php

namespace Database\Seeders;

use App\Models\DocumentationVendor;
use App\Models\StudyProgram;
use Illuminate\Database\Seeder;

class DocumentationVendorSeeder extends Seeder
{
    public function run(): void
    {
        $prodiDokter = StudyProgram::where('code', 'Dr')->first();

        DocumentationVendor::firstOrCreate(
            ['name' => 'Pontianak Visual Studio'],
            [
                'study_program_id' => $prodiDokter?->id,
                'category' => 'photographer',
                'contact_person' => 'Budi Santoso',
                'whatsapp_number' => '081234567890',
                'instagram_handle' => '@pontianakvisual',
                'price_estimate' => 'Mulai Rp 500.000 / paket',
                'description' => 'Langganan peserta Sumpah Dokter FK UNTAN. Menyediakan foto prosesi sakral, mini-studio background wisuda, & file hi-res cloud.',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        DocumentationVendor::firstOrCreate(
            ['name' => 'Lensa Khatulistiwa Photography'],
            [
                'study_program_id' => $prodiDokter?->id,
                'category' => 'photographer',
                'contact_person' => 'Reza Pratama',
                'whatsapp_number' => '085298765432',
                'instagram_handle' => '@lensakhatulistiwa',
                'price_estimate' => 'Mulai Rp 650.000 / paket',
                'description' => 'Spesialis foto wisudawan & keluarga. Bonus cetak frame 12R dan softcopy link Google Drive instan hari yang sama.',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        DocumentationVendor::firstOrCreate(
            ['name' => 'Untan Live Stream Media'],
            [
                'study_program_id' => $prodiDokter?->id,
                'category' => 'live_streaming',
                'contact_person' => 'Andi Saputra',
                'whatsapp_number' => '081345678901',
                'youtube_channel' => 'https://youtube.com/@untanlive',
                'price_estimate' => 'Mulai Rp 1.500.000 / prosesi',
                'description' => 'Jasa live streaming YouTube multi-kamera HD, audio direct mixer aula, lower-third nama peserta & running text resmi.',
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        DocumentationVendor::firstOrCreate(
            ['name' => 'Khatulistiwa Broadcast Pro'],
            [
                'study_program_id' => null, // Berlaku lintas prodi
                'category' => 'live_streaming',
                'contact_person' => 'Fajar Maulana',
                'whatsapp_number' => '089612345678',
                'youtube_channel' => 'https://youtube.com/@khatulistiwabroadcast',
                'price_estimate' => 'Mulai Rp 2.000.000 / prosesi',
                'description' => 'Broadcasting profesional prosesi sumpah & yudisium. Internet dedicated bonding 4G/Starlink anti-buffering dengan recording 4K.',
                'is_active' => true,
                'sort_order' => 4,
            ]
        );
    }
}
