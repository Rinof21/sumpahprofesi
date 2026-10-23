<?php

namespace Tests\Feature;

use App\Models\EventRule;
use App\Models\EventSchedule;
use App\Models\OathPeriod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventScheduleAndRuleTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_admin_can_view_event_schedules_and_rules_page()
    {
        $admin = User::role('Admin Prodi')->first();
        $this->actingAs($admin);

        $response = $this->get(route('admin.event-schedules.index'));
        $response->assertStatus(200);
        $response->assertSee('Susunan Acara & Tata Tertib', false);
        $response->assertSee('Rundown Acara');
        $response->assertSee('Tata Tertib Resmi');
    }

    public function test_admin_can_create_update_and_delete_event_schedule()
    {
        $admin = User::role('Admin Prodi')->first();
        $this->actingAs($admin);

        $period = OathPeriod::where('study_program_id', $admin->study_program_id)->first();

        // 1. Create Schedule
        $storeResponse = $this->post(route('admin.event-schedules.store'), [
            'period_id' => $period->id,
            'start_time' => '13:00',
            'end_time' => '14:00',
            'activity_name' => 'Gladi Resik & Penataan Barisan',
            'person_in_charge' => 'Sie Acara & Protokol',
            'notes' => 'Di aula utama',
            'sort_order' => 15,
        ]);

        $storeResponse->assertRedirect();
        $storeResponse->assertSessionHas('success');

        $this->assertDatabaseHas('event_schedules', [
            'period_id' => $period->id,
            'activity_name' => 'Gladi Resik & Penataan Barisan',
            'start_time' => '13:00',
        ]);

        $schedule = EventSchedule::where('activity_name', 'Gladi Resik & Penataan Barisan')->first();

        // 2. Update Schedule
        $updateResponse = $this->put(route('admin.event-schedules.update', $schedule->id), [
            'start_time' => '13:30',
            'end_time' => '14:30',
            'activity_name' => 'Gladi Resik Terpadu Semua Agama',
            'person_in_charge' => 'Sie Acara & Rohaniwan',
            'notes' => 'Pakaian bebas rapi',
            'sort_order' => 16,
        ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');

        $this->assertDatabaseHas('event_schedules', [
            'id' => $schedule->id,
            'activity_name' => 'Gladi Resik Terpadu Semua Agama',
            'start_time' => '13:30',
        ]);

        // 3. Delete Schedule
        $deleteResponse = $this->delete(route('admin.event-schedules.destroy', $schedule->id));
        $deleteResponse->assertRedirect();
        $deleteResponse->assertSessionHas('success');

        $this->assertDatabaseMissing('event_schedules', [
            'id' => $schedule->id,
        ]);
    }

    public function test_admin_can_create_update_and_delete_event_rule()
    {
        $admin = User::role('Admin Prodi')->first();
        $this->actingAs($admin);

        $period = OathPeriod::where('study_program_id', $admin->study_program_id)->first();

        // 1. Create Rule
        $storeResponse = $this->post(route('admin.event-rules.store'), [
            'period_id' => $period->id,
            'title' => 'Larangan Merokok & Membawa Makanan',
            'category' => 'Prosesi Aula',
            'description' => 'Seluruh area aula merupakan kawasan tanpa rokok dan dilarang membawa makanan ke dalam ruangan.',
            'is_strict' => 1,
            'sort_order' => 10,
        ]);

        $storeResponse->assertRedirect();
        $storeResponse->assertSessionHas('success');

        $this->assertDatabaseHas('event_rules', [
            'period_id' => $period->id,
            'title' => 'Larangan Merokok & Membawa Makanan',
            'is_strict' => 1,
        ]);

        $rule = EventRule::where('title', 'Larangan Merokok & Membawa Makanan')->first();

        // 2. Update Rule
        $updateResponse = $this->put(route('admin.event-rules.update', $rule->id), [
            'title' => 'Larangan Merokok & Makanan/Minuman',
            'category' => 'Prosesi Aula',
            'description' => 'Dilarang merokok, vape, serta membawa makanan berat ke dalam ruang prosesi.',
            'is_strict' => 1,
            'sort_order' => 11,
        ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');

        $this->assertDatabaseHas('event_rules', [
            'id' => $rule->id,
            'title' => 'Larangan Merokok & Makanan/Minuman',
        ]);

        // 3. Delete Rule
        $deleteResponse = $this->delete(route('admin.event-rules.destroy', $rule->id));
        $deleteResponse->assertRedirect();
        $deleteResponse->assertSessionHas('success');

        $this->assertDatabaseMissing('event_rules', [
            'id' => $rule->id,
        ]);
    }

    public function test_admin_can_load_default_template()
    {
        $admin = User::role('Admin Prodi')->first();
        $this->actingAs($admin);

        // Create a new period without schedules
        $period = OathPeriod::create([
            'study_program_id' => $admin->study_program_id,
            'name' => 'Sumpah Dokter Periode Khusus 2026',
            'slug' => 'sumpah-dokter-periode-khusus-2026',
            'event_date' => '2026-11-15',
            'quarter_code' => 'Q4',
            'access_token' => 'TK-KHUSUS26',
            'status' => 'active',
            'is_locked' => false,
        ]);

        $this->assertEquals(0, $period->schedules()->count());
        $this->assertEquals(0, $period->rules()->count());

        $response = $this->post(route('admin.event-schedules.load-template'), [
            'period_id' => $period->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertGreaterThan(0, $period->schedules()->count());
        $this->assertGreaterThan(0, $period->rules()->count());
    }

    public function test_candidate_dashboard_displays_schedules_and_rules()
    {
        $candidateUser = User::role('Peserta')->first();
        $this->actingAs($candidateUser);

        $response = $this->get(route('candidate.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Susunan Acara & Tata Tertib', false);
        $response->assertSee('Susunan Acara (Rundown)', false);
        $response->assertSee('Tata Tertib Resmi Aula', false);
    }

    public function test_candidate_can_view_dedicated_event_schedule_page()
    {
        $candidateUser = User::role('Peserta')->first();
        $this->actingAs($candidateUser);

        $response = $this->get(route('candidate.event-schedule'));
        $response->assertStatus(200);
        $response->assertSee('Susunan Acara & Tata Tertib Prosesi', false);
        $response->assertSee('Cetak / Simpan PDF');
        $response->assertSee('Susunan Acara (Rundown Prosesi)', false);
        $response->assertSee('Tata Tertib & Ketentuan Resmi Ruang Prosesi', false);
    }

    public function test_token_portal_displays_event_schedules_and_rules_links()
    {
        $period = OathPeriod::first();

        // Simulate verified token session
        $this->withSession([
            'verified_period_id' => $period->id,
            'verified_period_expires_at' => now()->addMinutes(60)->timestamp,
        ]);

        $response = $this->get(route('token-access.portal'));
        $response->assertStatus(200);
        $response->assertSee('Susunan Acara & Tata Tertib', false);
        $response->assertSee('Naskah Lafal Sumpah Official', false);
    }

    public function test_token_user_can_access_dedicated_event_schedule_page()
    {
        $period = OathPeriod::first();

        $this->withSession([
            'verified_period_id' => $period->id,
            'verified_period_expires_at' => now()->addMinutes(60)->timestamp,
        ]);

        $response = $this->get(route('token-access.event-schedule'));
        $response->assertStatus(200);
        $response->assertSee('Susunan Acara & Tata Tertib Resmi Prosesi', false);
        $response->assertSee('Susunan Acara (Rundown Prosesi Resmi)', false);
        $response->assertSee('Tata Tertib & Ketentuan Resmi Ruang Prosesi', false);
        $response->assertSee('Cetak / Simpan PDF');
    }

    public function test_token_user_can_access_dedicated_oath_script_page()
    {
        $period = OathPeriod::first();

        $this->withSession([
            'verified_period_id' => $period->id,
            'verified_period_expires_at' => now()->addMinutes(60)->timestamp,
        ]);

        $response = $this->get(route('token-access.oath-script'));
        $response->assertStatus(200);
        $response->assertSee('Naskah Lafal Sumpah Official', false);
        $response->assertSee('NASKAH LAFAL SUMPAH PROFESI', false);
        $response->assertSee('Cetak Dokumen');
    }

    public function test_unverified_public_user_is_redirected_to_token_form()
    {
        // Without token, public index should NOT show Pendaftaran, Susunan Acara, or Lafal Sumpah in sidebar
        $indexResponse = $this->get(route('public.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertDontSee(route('token-access.portal'));
        $indexResponse->assertDontSee(route('token-access.event-schedule'));
        $indexResponse->assertDontSee(route('token-access.oath-script'));

        // On token verify page itself, sidebar also does not display registration or schedule menu
        $tokenFormResponse = $this->get(route('token-access.index'));
        $tokenFormResponse->assertStatus(200);
        $tokenFormResponse->assertDontSee(route('token-access.portal'));

        // Accessing public routes redirects to token verification form
        $scheduleResponse = $this->get(route('public.event-schedule'));
        $scheduleResponse->assertRedirect(route('token-access.index'));
        $scheduleResponse->assertSessionHas('info');

        $oathResponse = $this->get(route('public.oath-script'));
        $oathResponse->assertRedirect(route('token-access.index'));
        $oathResponse->assertSessionHas('info');
    }

    public function test_verified_token_user_sees_sidebar_menus_and_can_access_routes()
    {
        $period = OathPeriod::first();

        $sessionData = [
            'verified_period_id' => $period->id,
            'verified_period_expires_at' => now()->addMinutes(60)->timestamp,
        ];

        // With verified token session, sidebar contains dedicated links
        $portalResponse = $this->withSession($sessionData)->get(route('token-access.portal'));
        $portalResponse->assertStatus(200);
        $portalResponse->assertSee(route('token-access.event-schedule'));
        $portalResponse->assertSee(route('token-access.oath-script'));

        // Accessing public route redirects directly to prodi-specific verified page
        $scheduleRedirect = $this->withSession($sessionData)->get(route('public.event-schedule'));
        $scheduleRedirect->assertRedirect(route('token-access.event-schedule'));

        $oathRedirect = $this->withSession($sessionData)->get(route('public.oath-script'));
        $oathRedirect->assertRedirect(route('token-access.oath-script'));
    }

    public function test_each_prodi_has_distinct_event_schedules_and_rules()
    {
        $periodDokter = OathPeriod::whereHas('studyProgram', fn ($q) => $q->where('code', 'Dr'))->first();
        $periodApoteker = OathPeriod::whereHas('studyProgram', fn ($q) => $q->where('code', 'Apt'))->first();
        $periodNers = OathPeriod::whereHas('studyProgram', fn ($q) => $q->where('code', 'Ns'))->first();

        $this->assertNotNull($periodDokter);
        $this->assertNotNull($periodApoteker);
        $this->assertNotNull($periodNers);

        // 1. Dokter specific schedule and rules
        $dokterResponse = $this->withSession([
            'verified_period_id' => $periodDokter->id,
            'verified_period_expires_at' => now()->addMinutes(60)->timestamp,
        ])->get(route('token-access.event-schedule'));
        $dokterResponse->assertStatus(200);
        $dokterResponse->assertSee('IDI', false);
        $dokterResponse->assertSee('Sumpah Dokter', false);
        $dokterResponse->assertDontSee('STRA');
        $dokterResponse->assertDontSee('PPNI');

        // 2. Apoteker specific schedule and rules
        $apotekerResponse = $this->withSession([
            'verified_period_id' => $periodApoteker->id,
            'verified_period_expires_at' => now()->addMinutes(60)->timestamp,
        ])->get(route('token-access.event-schedule'));
        $apotekerResponse->assertStatus(200);
        $apotekerResponse->assertSee('IAI', false);
        $apotekerResponse->assertSee('STRA', false);
        $apotekerResponse->assertSee('Apoteker', false);
        $apotekerResponse->assertDontSee('PPNI');

        // 3. Ners specific schedule and rules
        $nersResponse = $this->withSession([
            'verified_period_id' => $periodNers->id,
            'verified_period_expires_at' => now()->addMinutes(60)->timestamp,
        ])->get(route('token-access.event-schedule'));
        $nersResponse->assertStatus(200);
        $nersResponse->assertSee('PPNI', false);
        $nersResponse->assertSee('Kap', false);
        $nersResponse->assertSee('Ners', false);
        $nersResponse->assertDontSee('STRA');
    }

    public function test_newly_created_period_automatically_has_schedules_and_rules()
    {
        $admin = User::role('Admin Prodi')->first();
        $this->actingAs($admin);

        $response = $this->post(route('admin.periods.store'), [
            'name' => 'Sumpah Dokter Periode Otomatis 2026',
            'event_date' => '2026-12-01',
            'quarter_code' => 'Q4',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.periods.index'));
        $response->assertSessionHas('success');

        $createdPeriod = OathPeriod::where('name', 'Sumpah Dokter Periode Otomatis 2026')->first();
        $this->assertNotNull($createdPeriod);

        // Verify schedules and rules are automatically populated
        $this->assertGreaterThan(0, $createdPeriod->schedules()->count());
        $this->assertGreaterThan(0, $createdPeriod->rules()->count());
        $this->assertDatabaseHas('event_schedules', [
            'period_id' => $createdPeriod->id,
            'activity_name' => 'Prosesi Sakral Pengambilan Lafal Sumpah Dokter Indonesia bersama Rohaniwan',
        ]);
    }

    public function test_admin_can_apply_template_to_all_periods_in_prodi()
    {
        $admin = User::role('Admin Prodi')->first();
        $this->actingAs($admin);

        // Create a secondary period under the same study program
        $secondPeriod = OathPeriod::create([
            'study_program_id' => $admin->study_program_id,
            'name' => 'Sumpah Dokter Periode Cadangan 2026',
            'slug' => 'sumpah-dokter-periode-cadangan-2026',
            'event_date' => '2026-11-20',
            'quarter_code' => 'Q4',
            'access_token' => 'TK-CADANGAN26',
            'status' => 'draft',
            'is_locked' => false,
        ]);

        // Manually empty schedules and rules on the second period
        $secondPeriod->schedules()->delete();
        $secondPeriod->rules()->delete();
        $this->assertEquals(0, $secondPeriod->schedules()->count());

        $firstPeriod = OathPeriod::where('study_program_id', $admin->study_program_id)->first();

        // Apply to ALL periods
        $response = $this->post(route('admin.event-schedules.load-template'), [
            'period_id' => $firstPeriod->id,
            'apply_all' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Both periods should now have full schedules and rules
        $this->assertGreaterThan(0, $firstPeriod->fresh()->schedules()->count());
        $this->assertGreaterThan(0, $secondPeriod->fresh()->schedules()->count());
        $this->assertGreaterThan(0, $secondPeriod->fresh()->rules()->count());
    }

    public function test_artisan_command_syncs_schedules_and_rules_for_all_periods()
    {
        $this->artisan('oath:sync-templates', ['--force' => true])
            ->assertSuccessful();

        $allPeriods = OathPeriod::all();
        $this->assertGreaterThan(0, $allPeriods->count());

        foreach ($allPeriods as $period) {
            $this->assertGreaterThan(0, $period->schedules()->count());
            $this->assertGreaterThan(0, $period->rules()->count());
        }
    }
}
