<?php

namespace Database\Seeders;

use App\Models\ContactPerson;
use App\Models\EventPhoto;
use App\Models\OathCandidate;
use App\Models\OathPeriod;
use App\Models\Photographer;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SumpahProfesiSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Study Programs
        $prodiDokter = StudyProgram::create([
            'code' => 'Dr',
            'name' => 'Profesi Dokter',
            'degree_title' => 'dr.',
            'organization' => 'Ikatan Dokter Indonesia (IDI)',
            'slug' => 'profesi-dokter',
        ]);

        $prodiApoteker = StudyProgram::create([
            'code' => 'Apt',
            'name' => 'Profesi Apoteker',
            'degree_title' => 'Apt.',
            'organization' => 'Ikatan Apoteker Indonesia (IAI)',
            'slug' => 'profesi-apoteker',
        ]);

        $prodiNers = StudyProgram::create([
            'code' => 'Ns',
            'name' => 'Profesi Ners',
            'degree_title' => 'Ns.',
            'organization' => 'Persatuan Perawat Nasional Indonesia (PPNI)',
            'slug' => 'profesi-ners',
        ]);

        // 2. Create Contact Persons (FR-F1)
        // IT Helpdesk (Global / Null study_program_id)
        ContactPerson::create([
            'study_program_id' => null,
            'category' => 'it',
            'name' => 'Meja Bantuan IT Fakultas',
            'phone' => '6285391044299',
            'email' => 'rino.f@untan.ac.id',
            'is_active' => true,
        ]);

        // Admin Prodi Helpdesks
        ContactPerson::create([
            'study_program_id' => $prodiDokter->id,
            'category' => 'admin',
            'name' => 'Sekretariat Sumpah Dokter (Ibu. Evi Risdianty)',
            'phone' => '085387985303',
            'email' => 'evi.risdianty@untan.ac.id',
            'is_active' => true,
        ]);

        ContactPerson::create([
            'study_program_id' => $prodiApoteker->id,
            'category' => 'admin',
            'name' => 'Sekretariat Sumpah Apoteker (Bpk. Dahlan)',
            'phone' => '6281122334456',
            'email' => 'admin.apoteker@fkik.ac.id',
            'is_active' => true,
        ]);

        ContactPerson::create([
            'study_program_id' => $prodiNers->id,
            'category' => 'admin',
            'name' => 'Sekretariat Sumpah Ners (Neneng, S.Kep)',
            'phone' => '6281122334457',
            'email' => 'admin.ners@fkik.ac.id',
            'is_active' => true,
        ]);

        // 3. Create Users with Spatie Roles
        // Superadmin
        $superadmin = User::create([
            'name' => 'Rino Firmansyah',
            'email' => 'rino.f@untan.ac.id',
            'password' => Hash::make('password'),
        ]);
        $superadmin->assignRole('Superadmin');

        // Admin Prodi Users
        $adminDokter = User::create([
            'name' => 'Evi Risdianty',
            'email' => 'evi.risdianty@untan.ac.id',
            'password' => Hash::make('password'),
            'study_program_id' => $prodiDokter->id,
        ]);
        $adminDokter->assignRole('Admin Prodi');

        // Also create admin.dokter@fkik.ac.id for test and demo compatibility
        $adminDokterFkik = User::create([
            'name' => 'Admin Prodi Dokter',
            'email' => 'admin.dokter@fkik.ac.id',
            'password' => Hash::make('password'),
            'study_program_id' => $prodiDokter->id,
        ]);
        $adminDokterFkik->assignRole('Admin Prodi');

        $adminApoteker = User::create([
            'name' => 'Admin Prodi Apoteker',
            'email' => 'admin.apoteker@fkik.ac.id',
            'password' => Hash::make('password'),
            'study_program_id' => $prodiApoteker->id,
        ]);
        $adminApoteker->assignRole('Admin Prodi');

        $adminNers = User::create([
            'name' => 'Admin Prodi Ners',
            'email' => 'admin.ners@fkik.ac.id',
            'password' => Hash::make('password'),
            'study_program_id' => $prodiNers->id,
        ]);
        $adminNers->assignRole('Admin Prodi');

        // 4. Create Active Oath Periods
        $periodDokter = OathPeriod::create([
            'study_program_id' => $prodiDokter->id,
            'name' => 'Sumpah Dokter Periode II 2026',
            'slug' => 'sumpah-dokter-periode-ii-2026',
            'event_date' => '2026-08-15',
            'quarter_code' => 'Q2',
            'access_token' => 'TK-DOKTER26',
            'status' => 'active',
            'is_locked' => false,
        ]);

        $periodApoteker = OathPeriod::create([
            'study_program_id' => $prodiApoteker->id,
            'name' => 'Sumpah Apoteker Periode II 2026',
            'slug' => 'sumpah-apoteker-periode-ii-2026',
            'event_date' => '2026-08-20',
            'quarter_code' => 'Q2',
            'access_token' => 'TK-APOTEKER26',
            'status' => 'active',
            'is_locked' => false,
        ]);

        $periodNers = OathPeriod::create([
            'study_program_id' => $prodiNers->id,
            'name' => 'Sumpah Ners Periode II 2026',
            'slug' => 'sumpah-ners-periode-ii-2026',
            'event_date' => '2026-08-25',
            'quarter_code' => 'Q2',
            'access_token' => 'TK-NERS26',
            'status' => 'active',
            'is_locked' => false,
        ]);

        // 5. Create Peserta User & Oath Candidates
        $userPeserta = User::create([
            'name' => 'dr. Andika Pratama, S.Ked',
            'email' => 'peserta@fkik.ac.id',
            'password' => Hash::make('password'),
            'study_program_id' => $prodiDokter->id,
        ]);
        $userPeserta->assignRole('Peserta');

        OathCandidate::create([
            'period_id' => $periodDokter->id,
            'user_id' => $userPeserta->id,
            'nim' => 'I1011191001',
            'nik' => '6171010101980001',
            'full_name' => 'dr. Andika Pratama, S.Ked',
            'birth_place' => 'Pontianak',
            'birth_date' => '1998-01-01',
            'father_name' => 'Bambang Pratama',
            'mother_name' => 'Siti Aminah',
            'admission_path' => 'SNBP/SNMPTN',
            'religion' => 'Islam',
            'agreed_rules' => true,
            'agreed_at' => now(),
        ]);

        OathCandidate::create([
            'period_id' => $periodDokter->id,
            'user_id' => null,
            'nim' => 'I1011191002',
            'nik' => '6171010202980002',
            'full_name' => 'dr. Maria Fransiska, S.Ked',
            'birth_place' => 'Singkawang',
            'birth_date' => '1998-02-02',
            'father_name' => 'Fransiskus',
            'mother_name' => 'Theresia',
            'admission_path' => 'SNBT/SBMPTN',
            'religion' => 'Katolik',
            'agreed_rules' => true,
            'agreed_at' => now(),
        ]);

        // 6. Create Photographer
        Photographer::create([
            'period_id' => $periodDokter->id,
            'name' => 'Budi Santoso',
            'agency_name' => 'Pontianak Visual Studio',
            'phone_number' => '081234567890',
            'badge_code' => 'PHOTO-DR-2026-001',
        ]);
    }
}
