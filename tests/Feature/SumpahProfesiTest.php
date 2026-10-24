<?php

namespace Tests\Feature;

use App\Models\DocumentationVendor;
use App\Models\OathCandidate;
use App\Models\OathPeriod;
use App\Models\Photographer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
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
        $response->assertSee('1. Meja Bantuan IT Fakultas');
        $response->assertSee('2. Meja Bantuan Administrasi');
        $response->assertSee('3. Rekomendasi Vendor Fotografer Ruangan');
        $response->assertSee('4. Rekomendasi Jasa Streaming Live YouTube');
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
            'name' => 'Fotografer 4',
            'agency_name' => 'Vendor 4',
            'phone_number' => '081234567894',
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
        $this->assertTrue((bool) $candidate1->fresh()->is_speech_rep);

        // Assign candidate 2
        $this->post(route('admin.candidates.speech-rep', $candidate2->id));
        $this->assertFalse((bool) $candidate1->fresh()->is_speech_rep);
        $this->assertTrue((bool) $candidate2->fresh()->is_speech_rep);
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
        $newRole = Role::findByName('Verifikator Berkas');
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
        $this->assertTrue((bool) $period->fresh()->is_locked);

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
            'sort_order' => 5,
            'nim' => '22010119888',
            'full_name' => 'Mahasiswa Tanpa Login',
            'birth_place' => 'Pontianak',
            'birth_date' => '1999-12-12',
            'father_name' => 'Bapak Mahasiswa',
            'mother_name' => 'Ibu Mahasiswa',
            'admission_path' => 'SNBP/SNMPTN',
            'religion' => 'Islam',
        ]);
        $storeRes->assertRedirect(route('token-access.portal'));
        $this->assertDatabaseHas('oath_candidates', [
            'nim' => '22010119888',
            'full_name' => 'Mahasiswa Tanpa Login',
            'nik' => null,
            'sort_order' => 5,
        ]);
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

    public function test_candidate_order_arrangement_and_movement()
    {
        $adminUser = User::where('email', 'admin.dokter@fkik.ac.id')->first();
        $period = OathPeriod::where('study_program_id', $adminUser->study_program_id)->first();
        $period->update(['access_token' => 'TK-ORDER26', 'is_locked' => false]);

        // Get existing two candidates from seeder
        $andika = OathCandidate::where('period_id', $period->id)->where('nim', 'I1011191001')->first();
        $maria = OathCandidate::where('period_id', $period->id)->where('nim', 'I1011191002')->first();

        $this->assertEquals(1, $andika->sort_order);
        $this->assertEquals(2, $maria->sort_order);

        // 1. Verify token
        $verifyRes = $this->post(route('token-access.verify'), [
            'period_id' => $period->id,
            'access_token' => 'TK-ORDER26',
        ]);
        $verifyRes->assertRedirect(route('token-access.portal'));

        // 2. View portal page - should see order badge, tambah peserta, and modals
        $portalRes = $this->get(route('token-access.portal'));
        $portalRes->assertStatus(200);
        $portalRes->assertSee('Atur Semua Nomor Urut');
        $portalRes->assertSee('modal-reorder-candidates');
        $portalRes->assertSee('Tambah Peserta');
        $portalRes->assertSee('modal-create-candidate');

        // 3. Move Maria UP (swapping with Andika)
        $moveUpRes = $this->post(route('token-access.move', $maria->id), [
            'direction' => 'up',
        ]);
        $moveUpRes->assertRedirect(route('token-access.portal'));
        $moveUpRes->assertSessionHas('success');

        $this->assertEquals(1, $maria->fresh()->sort_order);
        $this->assertEquals(2, $andika->fresh()->sort_order);

        // 4. Move Maria DOWN (swapping back)
        $moveDownRes = $this->post(route('token-access.move', $maria->id), [
            'direction' => 'down',
        ]);
        $moveDownRes->assertRedirect(route('token-access.portal'));
        $moveDownRes->assertSessionHas('success');

        $this->assertEquals(1, $andika->fresh()->sort_order);
        $this->assertEquals(2, $maria->fresh()->sort_order);

        // 5. Batch Reorder
        $batchRes = $this->post(route('token-access.reorder'), [
            'orders' => [
                $andika->id => 10,
                $maria->id => 20,
            ],
        ]);
        $batchRes->assertRedirect(route('token-access.portal'));
        $this->assertEquals(10, $andika->fresh()->sort_order);
        $this->assertEquals(20, $maria->fresh()->sort_order);

        // 6. Admin can also move candidate
        $this->actingAs($adminUser);
        $adminMoveRes = $this->post(route('admin.candidates.move', $maria->id), [
            'direction' => 'up',
        ]);
        $adminMoveRes->assertSessionHas('success');
        $this->assertEquals(1, $maria->fresh()->sort_order);
        $this->assertEquals(2, $andika->fresh()->sort_order);
    }

    public function test_admin_and_superadmin_can_manage_documentation_and_youtube_live_streaming_vendors()
    {
        $adminUser = User::role('Admin Prodi')->first();
        $this->actingAs($adminUser);

        // 1. View vendors index page
        $response = $this->get(route('admin.vendors.index'));
        $response->assertStatus(200);
        $response->assertSee('Rekomendasi Vendor Fotografer');

        // 2. Add photographer vendor
        $createPhotographerRes = $this->post(route('admin.vendors.store'), [
            'category' => 'photographer',
            'name' => 'Khatulistiwa Photo Art',
            'contact_person' => 'Budi Santoso',
            'whatsapp_number' => '081234567890',
            'price_estimate' => 'Rp 1.500.000 / Sesi',
            'instagram_handle' => '@khatulistiwaphoto',
            'description' => 'Spesialis foto studio & cetak langsung di tempat.',
        ]);
        $createPhotographerRes->assertSessionHas('success');
        $this->assertDatabaseHas('documentation_vendors', [
            'name' => 'Khatulistiwa Photo Art',
            'category' => 'photographer',
            'whatsapp_number' => '081234567890',
        ]);

        $vendor1 = DocumentationVendor::where('name', 'Khatulistiwa Photo Art')->first();
        $this->assertStringContainsString('wa.me/6281234567890', $vendor1->whatsapp_url);

        // 3. Add Live Streaming YouTube vendor
        $createStreamRes = $this->post(route('admin.vendors.store'), [
            'category' => 'live_streaming',
            'name' => 'Pontianak Broadcast YouTube',
            'contact_person' => 'Dedi Wijaya',
            'whatsapp_number' => '085299887766',
            'price_estimate' => 'Rp 2.500.000 / Acara',
            'youtube_channel' => 'https://youtube.com/@pontianakbroadcast',
            'description' => 'Multi-cam 3 sudut, audio mixer, siaran langsung YouTube HD.',
        ]);
        $createStreamRes->assertSessionHas('success');
        $this->assertDatabaseHas('documentation_vendors', [
            'name' => 'Pontianak Broadcast YouTube',
            'category' => 'live_streaming',
        ]);

        // 4. Update vendor
        $vendor2 = DocumentationVendor::where('name', 'Pontianak Broadcast YouTube')->first();
        $updateRes = $this->put(route('admin.vendors.update', $vendor2->id), [
            'category' => 'live_streaming',
            'name' => 'Pontianak Broadcast YouTube Pro',
            'contact_person' => 'Dedi Wijaya, S.Kom',
            'whatsapp_number' => '085299887766',
            'price_estimate' => 'Rp 3.000.000 / Acara',
            'is_active' => true,
        ]);
        $updateRes->assertSessionHas('success');
        $this->assertEquals('Pontianak Broadcast YouTube Pro', $vendor2->fresh()->name);

        // 5. Toggle active status
        $toggleRes = $this->patch(route('admin.vendors.toggle', $vendor2->id));
        $toggleRes->assertSessionHas('success');
        $this->assertFalse((bool) $vendor2->fresh()->is_active);

        // 6. Delete vendor
        $deleteRes = $this->delete(route('admin.vendors.destroy', $vendor2->id));
        $deleteRes->assertSessionHas('success');
        $this->assertDatabaseMissing('documentation_vendors', ['id' => $vendor2->id]);

        // 7. Superadmin can also view and create vendors
        $superadmin = User::role('Superadmin')->first();
        $this->actingAs($superadmin);

        $superadminView = $this->get(route('superadmin.vendors.index'));
        $superadminView->assertStatus(200);

        $superadminStore = $this->post(route('superadmin.vendors.store'), [
            'category' => 'live_streaming',
            'name' => 'Fakultas Official Streaming Unit',
            'whatsapp_number' => '081122334455',
            'study_program_id' => null, // Global faculty-wide
        ]);
        $superadminStore->assertSessionHas('success');
        $this->assertDatabaseHas('documentation_vendors', [
            'name' => 'Fakultas Official Streaming Unit',
            'study_program_id' => null,
        ]);

        // 8. Filter by category
        $this->actingAs($adminUser);
        $filterRes = $this->get(route('admin.vendors.index', ['category' => 'photographer']));
        $filterRes->assertStatus(200);
        $filterRes->assertSee('Khatulistiwa Photo Art');
    }

    public function test_candidates_can_appoint_oath_coordinator_and_speech_representative()
    {
        $adminUser = User::where('email', 'admin.dokter@fkik.ac.id')->first();
        $period = OathPeriod::where('study_program_id', $adminUser->study_program_id)->first();
        $period->update(['access_token' => 'TK-ROLE2026', 'is_locked' => false]);

        $andika = OathCandidate::where('period_id', $period->id)->where('nim', 'I1011191001')->first();
        $maria = OathCandidate::where('period_id', $period->id)->where('nim', 'I1011191002')->first();

        // 1. Verify token
        $verifyRes = $this->post(route('token-access.verify'), [
            'period_id' => $period->id,
            'access_token' => 'TK-ROLE2026',
        ]);
        $verifyRes->assertRedirect(route('token-access.portal'));

        // 2. View portal - verify UI triggers exist and redundant columns/buttons are removed
        $portalRes = $this->get(route('token-access.portal'));
        $portalRes->assertStatus(200);
        $portalRes->assertSee('Tunjuk Koordinator & Peran', false);
        $portalRes->assertSee('modal-assign-roles');
        $portalRes->assertDontSee('Slide PPT Profil');
        $portalRes->assertDontSee(route('token-access.oath-coordinator', $andika->id));
        $portalRes->assertDontSee(route('token-access.speech-rep', $andika->id));

        // 3. Appoint Andika as Speech Representative
        $repRes = $this->post(route('token-access.speech-rep', $andika->id));
        $repRes->assertRedirect(route('token-access.portal'));
        $repRes->assertSessionHas('success');
        $this->assertTrue((bool) $andika->fresh()->is_speech_rep);
        $this->assertFalse((bool) $maria->fresh()->is_speech_rep);

        // 4. Appoint Maria as Oath Coordinator
        $coordRes = $this->post(route('token-access.oath-coordinator', $maria->id));
        $coordRes->assertRedirect(route('token-access.portal'));
        $coordRes->assertSessionHas('success');
        $this->assertTrue((bool) $maria->fresh()->is_oath_coordinator);
        $this->assertFalse((bool) $andika->fresh()->is_oath_coordinator);

        // 5. Toggle Speech Rep off for Andika
        $toggleRepRes = $this->post(route('token-access.speech-rep', $andika->id));
        $toggleRepRes->assertRedirect(route('token-access.portal'));
        $this->assertFalse((bool) $andika->fresh()->is_speech_rep);

        // 6. Direct Bulk Assignment via assign-roles modal
        $assignRolesRes = $this->post(route('token-access.assign-roles'), [
            'oath_coordinator_id' => $andika->id,
            'speech_rep_id' => $maria->id,
        ]);
        $assignRolesRes->assertRedirect(route('token-access.portal'));
        $assignRolesRes->assertSessionHas('success');
        $this->assertTrue((bool) $andika->fresh()->is_oath_coordinator);
        $this->assertFalse((bool) $maria->fresh()->is_oath_coordinator);
        $this->assertTrue((bool) $maria->fresh()->is_speech_rep);
        $this->assertFalse((bool) $andika->fresh()->is_speech_rep);
    }

    public function test_public_archive_detail_displays_two_column_layout_and_pagination()
    {
        $period = OathPeriod::where('slug', 'sumpah-dokter-ke-63-wwrap')->first();
        if (! $period) {
            $period = OathPeriod::first();
        }

        $response = $this->get(route('public.archive-detail', $period->slug));
        $response->assertStatus(200);

        // Verify Sebaran Jalur Masuk exists
        $response->assertSee('Sebaran Jalur Masuk Kuliah Lulusan');

        // Verify Rekapitulasi Agama Peserta is removed
        $response->assertDontSee('Rekapitulasi Agama Peserta (Persuratan Rohaniwan)');

        // Verify Status column is removed
        $response->assertDontSee('<th class="px-4 py-2.5 text-center">Status</th>', false);
        $response->assertDontSee('Disumpah</span>', false);

        // Verify Daftar Peserta table exists
        $response->assertSee('Daftar Peserta Sumpah');
    }
}
