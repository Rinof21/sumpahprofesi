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

        

       
    }
}
