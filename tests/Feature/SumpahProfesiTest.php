<?php

namespace Tests\Feature;

use App\Models\OathCandidate;
use App\Models\OathPeriod;
use App\Models\Photographer;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SumpahProfesiTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_public_landing_page_loads_successfully()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Sumpah Profesi Kesehatan');
    }

    public function test_public_helpdesk_loads_successfully()
    {
        $response = $this->get('/helpdesk');
        $response->assertStatus(200);
        $response->assertSee('Direktori Meja Bantuan Terarah');
    }

    public function test_compliance_gate_locks_e_ticket_until_agreed()
    {
        $candidateUser = User::role('Peserta')->first();

        // Candidate has agreed_rules = true from seeder, let's set to false first
        $candidateUser->candidate->update(['agreed_rules' => false]);

        $this->actingAs($candidateUser);

        // Accessing e-ticket should redirect to dashboard with error message
        $response = $this->get(route('candidate.e-ticket'));
        $response->assertRedirect(route('candidate.dashboard'));
        $response->assertSessionHas('error');

        // Agree rules
        $agreeResponse = $this->post(route('candidate.agree-rules'), [
            'confirm_no_children' => '1',
            'confirm_punctuality' => '1',
            'confirm_dresscode' => '1',
        ]);
        $agreeResponse->assertRedirect(route('candidate.dashboard'));
        $agreeResponse->assertSessionHas('success');

        // Now e-ticket should load
        $ticketResponse = $this->get(route('candidate.e-ticket'));
        $ticketResponse->assertStatus(200);
        $ticketResponse->assertSee('DILARANG MEMBAWA ANAK KECIL / BALITA');
    }

    public function test_photographer_max_quota_limit_enforced()
    {
        $adminUser = User::role('Admin Prodi')->first();
        $period = OathPeriod::where('study_program_id', $adminUser->study_program_id)->first();

        // Remove existing photographers
        Photographer::where('period_id', $period->id)->delete();

        $this->actingAs($adminUser);

        // Add 3 photographers
        for ($i = 1; $i <= 3; $i++) {
            $response = $this->post(route('admin.photographers.store'), [
                'period_id' => $period->id,
                'name' => "Fotografer {$i}",
                'agency_name' => "Vendor {$i}",
                'phone_number' => "08123456789{$i}",
            ]);
            $response->assertSessionHas('success');
        }

        $this->assertEquals(3, Photographer::where('period_id', $period->id)->count());

        // Attempting to add 4th photographer must fail with error message
        $fourthResponse = $this->post(route('admin.photographers.store'), [
            'period_id' => $period->id,
            'name' => "Fotografer 4",
            'agency_name' => "Vendor 4",
            'phone_number' => "081234567894",
        ]);
        $fourthResponse->assertSessionHas('error');

        // Total remains 3
        $this->assertEquals(3, Photographer::where('period_id', $period->id)->count());
    }

    public function test_speech_representative_assignment_unsets_previous()
    {
        $adminUser = User::where('email', 'admin.dokter@fkik.ac.id')->first();
        $period = OathPeriod::where('study_program_id', $adminUser->study_program_id)->first();

        $candidates = OathCandidate::where('period_id', $period->id)->get();
        $candidate1 = $candidates[0];
        $candidate2 = $candidates[1];

        $this->actingAs($adminUser);

        // Assign candidate 1
        $this->post(route('admin.candidates.speech-rep', $candidate1->id));
        $this->assertTrue((bool)$candidate1->fresh()->is_speech_rep);

        // Assign candidate 2
        $this->post(route('admin.candidates.speech-rep', $candidate2->id));
        $this->assertFalse((bool)$candidate1->fresh()->is_speech_rep);
        $this->assertTrue((bool)$candidate2->fresh()->is_speech_rep);
    }

    public function test_superadmin_can_manage_spatie_roles_and_permissions()
    {
        $superadmin = User::role('Superadmin')->first();
        $this->actingAs($superadmin);

        // 1. View roles & permissions page
        $response = $this->get(route('superadmin.roles-permissions.index'));
        $response->assertStatus(200);
        $response->assertSee('Manajemen Peran & Izin Akses (Spatie Permission)');

        // 2. Create a new Role
        $roleResponse = $this->post(route('superadmin.roles.store'), [
            'name' => 'Verifikator Berkas',
            'permissions' => ['validate-candidates', 'view-cleric-recap'],
        ]);
        $roleResponse->assertSessionHas('success');
        $this->assertDatabaseHas('roles', ['name' => 'Verifikator Berkas']);

        // 3. Create a new Permission
        $permResponse = $this->post(route('superadmin.permissions.store'), [
            'name' => 'export-audit-log',
        ]);
        $permResponse->assertSessionHas('success');
        $this->assertDatabaseHas('permissions', ['name' => 'export-audit-log']);

        // 4. Update Role permissions
        $newRole = \Spatie\Permission\Models\Role::findByName('Verifikator Berkas');
        $updateRoleResponse = $this->put(route('superadmin.roles.update', $newRole->id), [
            'name' => 'Verifikator Berkas Utama',
            'permissions' => ['validate-candidates', 'export-audit-log'],
        ]);
        $updateRoleResponse->assertSessionHas('success');
        $this->assertDatabaseHas('roles', ['name' => 'Verifikator Berkas Utama']);

        // 5. Delete role
        $deleteRoleResponse = $this->delete(route('superadmin.roles.destroy', $newRole->id));
        $deleteRoleResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('roles', ['name' => 'Verifikator Berkas Utama']);
    }

    public function test_candidate_token_verification_biodata_crud_and_admin_lock()
    {
        $adminUser = User::where('email', 'admin.dokter@fkik.ac.id')->first();
        $period = OathPeriod::where('study_program_id', $adminUser->study_program_id)->first();
        $period->update(['access_token' => 'TK-DOKTER26', 'is_locked' => false]);

        // Create new student user without candidate record
        $studentUser = User::create([
            'name' => 'Siti Rahma, S.Ked',
            'email' => 'siti.rahma@student.ac.id',
            'password' => bcrypt('password'),
            'study_program_id' => $adminUser->study_program_id,
        ]);
        $studentUser->assignRole('Peserta');

        $this->actingAs($studentUser);

        // 1. Invalid Token Registration fails
        $invalidTokenRes = $this->post(route('candidate.register.store'), [
            'period_id' => $period->id,
            'access_token' => 'WRONG_TOKEN',
            'nim' => '22010119099',
            'nik' => '3275011204980099',
            'full_name' => 'Siti Rahma, S.Ked',
            'birth_place' => 'Pontianak',
            'birth_date' => '1999-05-10',
            'father_name' => 'Rahman',
            'mother_name' => 'Siti',
            'admission_path' => 'SNBP/SNMPTN',
            'religion' => 'Islam',
        ]);
        $invalidTokenRes->assertSessionHas('error');

        // 2. Valid Token Registration succeeds
        $validTokenRes = $this->post(route('candidate.register.store'), [
            'period_id' => $period->id,
            'access_token' => 'TK-DOKTER26',
            'nim' => '22010119099',
            'nik' => '3275011204980099',
            'full_name' => 'Siti Rahma, S.Ked',
            'birth_place' => 'Pontianak',
            'birth_date' => '1999-05-10',
            'father_name' => 'Rahman',
            'mother_name' => 'Siti',
            'admission_path' => 'SNBP/SNMPTN',
            'religion' => 'Islam',
        ]);
        $validTokenRes->assertRedirect(route('candidate.dashboard'));
        $validTokenRes->assertSessionHas('success');
        $this->assertDatabaseHas('oath_candidates', ['nim' => '22010119099']);

        // 3. Edit Biodata succeeds when unlocked
        $editRes = $this->put(route('candidate.biodata.update'), [
            'nim' => '22010119099',
            'nik' => '3275011204980099',
            'full_name' => 'Dr. Siti Rahma, S.Ked',
            'birth_place' => 'Pontianak',
            'birth_date' => '1999-05-10',
            'father_name' => 'H. Rahman',
            'mother_name' => 'Hj. Siti',
            'admission_path' => 'SNBP/SNMPTN',
            'religion' => 'Islam',
        ]);
        $editRes->assertRedirect(route('candidate.dashboard'));
        $editRes->assertSessionHas('success');
        $this->assertDatabaseHas('oath_candidates', ['full_name' => 'Dr. Siti Rahma, S.Ked']);

        // 4. Admin locks the period
        $this->actingAs($adminUser);
        $lockRes = $this->patch(route('admin.periods.toggle-lock', $period->id));
        $lockRes->assertSessionHas('success');
        $this->assertTrue((bool)$period->fresh()->is_locked);

        // 5. Editing biodata when locked is blocked
        $this->actingAs($studentUser);
        $blockedEditRes = $this->put(route('candidate.biodata.update'), [
            'nim' => '22010119099',
            'nik' => '3275011204980099',
            'full_name' => 'Attempt Edit When Locked',
            'birth_place' => 'Pontianak',
            'birth_date' => '1999-05-10',
            'father_name' => 'H. Rahman',
            'mother_name' => 'Hj. Siti',
            'admission_path' => 'SNBP/SNMPTN',
            'religion' => 'Islam',
        ]);
        $blockedEditRes->assertSessionHas('error');
        $this->assertDatabaseMissing('oath_candidates', ['full_name' => 'Attempt Edit When Locked']);
    }

    public function test_public_token_access_without_login()
    {
        $adminUser = User::where('email', 'admin.dokter@fkik.ac.id')->first();
        $period = OathPeriod::where('study_program_id', $adminUser->study_program_id)->first();
        $period->update(['access_token' => 'TOKEN123', 'is_locked' => false]);

        // 1. Visit public token index page without login
        $response = $this->get(route('token-access.index'));
        $response->assertStatus(200);
        $response->assertSee('Portal Pendaftaran Sumpah Mahasiswa');

        // 2. Verify token without login
        $verifyRes = $this->post(route('token-access.verify'), [
            'period_id' => $period->id,
            'access_token' => 'TOKEN123',
        ]);
        $verifyRes->assertRedirect(route('token-access.portal'));

        // 3. Access portal and store candidate entry
        $portalRes = $this->get(route('token-access.portal'));
        $portalRes->assertStatus(200);
        $portalRes->assertSee('TOKEN123');
        $portalRes->assertSee('Naskah Lafal Sumpah Official');

        $storeRes = $this->post(route('token-access.store'), [
            'period_id' => $period->id,
            'nim' => '22010119888',
            'nik' => '3275011204988888',
            'full_name' => 'Mahasiswa Tanpa Login',
            'birth_place' => 'Pontianak',
            'birth_date' => '1999-12-12',
            'father_name' => 'Bapak Mahasiswa',
            'mother_name' => 'Ibu Mahasiswa',
            'admission_path' => 'SNBP/SNMPTN',
            'religion' => 'Islam',
        ]);
        $storeRes->assertRedirect(route('token-access.portal'));
        $this->assertDatabaseHas('oath_candidates', ['nim' => '22010119888', 'full_name' => 'Mahasiswa Tanpa Login']);
    }

    public function test_period_soft_delete_restore_and_force_delete()
    {
        $adminUser = User::where('email', 'admin.dokter@fkik.ac.id')->first();
        $this->actingAs($adminUser);

        // 1. Create a test period
        $period = OathPeriod::create([
            'study_program_id' => $adminUser->study_program_id,
            'name' => 'Periode Uji Soft Delete',
            'slug' => 'periode-uji-soft-delete',
            'event_date' => '2026-11-20',
            'quarter_code' => 'Q4',
            'access_token' => 'TK-TESTSOFTDELETE',
            'status' => 'draft',
            'is_locked' => false,
        ]);

        $this->assertDatabaseHas('oath_periods', ['id' => $period->id, 'deleted_at' => null]);

        // 2. Soft delete period
        $deleteRes = $this->delete(route('admin.periods.destroy', $period->id));
        $deleteRes->assertRedirect(route('admin.periods.index'));
        $deleteRes->assertSessionHas('success');

        // Verify soft deleted (deleted_at is NOT null)
        $this->assertSoftDeleted('oath_periods', ['id' => $period->id]);

        // 3. Restore period from trash
        $restoreRes = $this->post(route('admin.periods.restore', $period->id));
        $restoreRes->assertRedirect(route('admin.periods.index'));
        $restoreRes->assertSessionHas('success');

        // Verify restored (deleted_at is null)
        $this->assertDatabaseHas('oath_periods', ['id' => $period->id, 'deleted_at' => null]);

        // 4. Soft delete again and force delete
        $this->delete(route('admin.periods.destroy', $period->id));
        $this->assertSoftDeleted('oath_periods', ['id' => $period->id]);

        $forceDeleteRes = $this->delete(route('admin.periods.force-delete', $period->id));
        $forceDeleteRes->assertRedirect(route('admin.periods.index'));
        $forceDeleteRes->assertSessionHas('success');

        // Verify permanently deleted from database
        $this->assertDatabaseMissing('oath_periods', ['id' => $period->id]);
    }

    public function test_admin_periods_page_renders_full_width_table_and_modal()
    {
        $adminUser = User::role('Admin Prodi')->first();
        $this->actingAs($adminUser);

        $response = $this->get(route('admin.periods.index'));
        $response->assertStatus(200);

        // Verify full-width table container & trigger buttons exist
        $response->assertSee('Daftar Periode Sumpah & Akses Token', false);
        $response->assertSee('modal-create-period');
        $response->assertSee('modal-edit-period');
        $response->assertSee("openModal('modal-create-period')", false);

        // Verify storing period through POST works
        $storeRes = $this->post(route('admin.periods.store'), [
            'name' => 'Sumpah Dokter Periode Baru 2026',
            'event_date' => '2026-11-20',
            'quarter_code' => 'Q4',
            'access_token' => 'TK-BARU2026',
            'status' => 'draft',
        ]);
        $storeRes->assertRedirect(route('admin.periods.index'));
        $this->assertDatabaseHas('oath_periods', [
            'name' => 'Sumpah Dokter Periode Baru 2026',
            'access_token' => 'TK-BARU2026',
        ]);
    }
}

