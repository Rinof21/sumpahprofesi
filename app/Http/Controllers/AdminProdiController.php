<?php

namespace App\Http\Controllers;

use App\Exports\CandidatesExport;
use App\Models\ContactPerson;
use App\Models\DocumentationVendor;
use App\Models\EventPhoto;
use App\Models\EventRule;
use App\Models\EventSchedule;
use App\Models\OathCandidate;
use App\Models\OathPeriod;
use App\Models\Photographer;
use App\Models\StudyProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class AdminProdiController extends Controller
{
    private function getAdminProdiId()
    {
        $user = Auth::user();

        return $user->study_program_id;
    }

    public function dashboard(): View
    {
        $prodiId = $this->getAdminProdiId();
        $prodi = StudyProgram::find($prodiId);

        $periods = OathPeriod::where('study_program_id', $prodiId)->orderBy('event_date', 'desc')->get();
        $activePeriod = $periods->firstWhere('status', 'active');

        $candidatesCount = 0;
        $religionRecap = collect();
        $photographersCount = 0;

        if ($activePeriod) {
            $candidatesCount = OathCandidate::where('period_id', $activePeriod->id)->count();
            $religionRecap = OathCandidate::where('period_id', $activePeriod->id)
                ->select('religion', DB::raw('count(*) as count'))
                ->groupBy('religion')
                ->pluck('count', 'religion');
            $photographersCount = Photographer::where('period_id', $activePeriod->id)->count();
        }

        // Vendor Dokumentasi & Live Streaming (Rujukan Berkelanjutan Lintas Periode)
        $photographerVendorsCount = DocumentationVendor::active()
            ->where(function ($q) use ($prodiId) {
                $q->whereNull('study_program_id')->orWhere('study_program_id', $prodiId);
            })
            ->where('category', 'photographer')
            ->count();

        $streamingVendorsCount = DocumentationVendor::active()
            ->where(function ($q) use ($prodiId) {
                $q->whereNull('study_program_id')->orWhere('study_program_id', $prodiId);
            })
            ->where('category', 'live_streaming')
            ->count();

        $totalVendorsCount = $photographerVendorsCount + $streamingVendorsCount;

        return view('admin.dashboard', compact(
            'prodi',
            'periods',
            'activePeriod',
            'candidatesCount',
            'religionRecap',
            'photographersCount',
            'photographerVendorsCount',
            'streamingVendorsCount',
            'totalVendorsCount'
        ));
    }

    public function updateOathPdf(Request $request): RedirectResponse
    {
        $prodiId = $this->getAdminProdiId();
        $prodi = StudyProgram::findOrFail($prodiId);

        $request->validate([
            'oath_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        if ($request->hasFile('oath_pdf')) {
            $oathPdfPath = $request->file('oath_pdf')->store('oath_pdfs', 'public');
            $prodi->update(['oath_pdf_path' => $oathPdfPath]);

            return redirect()->route('admin.dashboard')->with('success', 'Naskah Lafal Sumpah (PDF) berhasil diunggah dan disimpan untuk prodi ini.');
        }

        return redirect()->back()->with('error', 'Silakan pilih file PDF untuk diunggah.');
    }

    // --- PERIOD MANAGEMENT (FR-B1, B2, B3) ---
    public function periods(): View
    {
        $prodiId = $this->getAdminProdiId();

        $activePeriods = OathPeriod::withCount(['candidates', 'photographers'])
            ->where('study_program_id', $prodiId)
            ->whereIn('status', ['active', 'draft'])
            ->orderBy('event_date', 'desc')
            ->get();

        $archivedPeriods = OathPeriod::withCount(['candidates', 'photographers'])
            ->where('study_program_id', $prodiId)
            ->where('status', 'archived')
            ->orderBy('event_date', 'desc')
            ->get();

        $trashedPeriods = OathPeriod::onlyTrashed()
            ->withCount(['candidates', 'photographers'])
            ->where('study_program_id', $prodiId)
            ->orderBy('deleted_at', 'desc')
            ->get();

        $periods = $activePeriods;

        return view('admin.periods.index', compact('periods', 'activePeriods', 'archivedPeriods', 'trashedPeriods'));
    }

    public function destroyPeriod(OathPeriod $period): RedirectResponse
    {
        $name = $period->name;
        $period->delete();

        return redirect()->route('admin.periods.index')->with('success', "Periode sumpah \"{$name}\" berhasil dihapus (soft delete) dan dipindahkan ke Sampah/Trash.");
    }

    public function restorePeriod($id): RedirectResponse
    {
        $prodiId = $this->getAdminProdiId();
        $period = OathPeriod::onlyTrashed()
            ->where('study_program_id', $prodiId)
            ->findOrFail($id);

        $period->restore();

        return redirect()->route('admin.periods.index')->with('success', "Periode sumpah \"{$period->name}\" berhasil dipulihkan dari Trash.");
    }

    public function forceDeletePeriod($id): RedirectResponse
    {
        $prodiId = $this->getAdminProdiId();
        $period = OathPeriod::onlyTrashed()
            ->where('study_program_id', $prodiId)
            ->findOrFail($id);

        $name = $period->name;
        $period->forceDelete();

        return redirect()->route('admin.periods.index')->with('success', "Periode sumpah \"{$name}\" telah dihapus secara permanen.");
    }

    public function storePeriod(Request $request): RedirectResponse
    {
        $prodiId = $this->getAdminProdiId();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'quarter_code' => ['required', 'string'],
            'access_token' => ['nullable', 'string', 'max:50'],
            'drive_url' => ['nullable', 'url'],
            'oath_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'status' => ['required', 'in:draft,active,archived'],
        ]);

        // FR-B3: Strictly only 1 active period per study program
        if ($validated['status'] === 'active') {
            OathPeriod::where('study_program_id', $prodiId)
                ->where('status', 'active')
                ->update(['status' => 'archived']);
        }

        $token = ! empty($validated['access_token'])
            ? strtoupper(trim($validated['access_token']))
            : 'TK-'.strtoupper(Str::random(6));

        $oathPdfPath = null;
        if ($request->hasFile('oath_pdf')) {
            $oathPdfPath = $request->file('oath_pdf')->store('oath_pdfs', 'public');
        }

        $period = OathPeriod::create([
            'study_program_id' => $prodiId,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name'].'-'.Str::random(5)),
            'event_date' => $validated['event_date'],
            'quarter_code' => $validated['quarter_code'],
            'access_token' => $token,
            'drive_url' => $validated['drive_url'] ?? null,
            'oath_pdf_path' => $oathPdfPath,
            'status' => $validated['status'],
            'is_locked' => false,
        ]);

        // Otomatis populate susunan acara dan tata tertib standar prodi untuk periode baru
        $prodiCode = $period->studyProgram->code ?? null;
        $defaultSchedules = EventSchedule::getDefaultTemplateForProdi($prodiCode);
        $defaultRules = EventRule::getDefaultTemplateForProdi($prodiCode);

        foreach ($defaultSchedules as $sched) {
            $period->schedules()->create($sched);
        }

        foreach ($defaultRules as $rul) {
            $period->rules()->create($rul);
        }

        return redirect()->route('admin.periods.index')->with('success', "Periode sumpah baru berhasil dibuat beserta susunan acara & tata tertib otomatis dengan Kode Token Akses: {$token}");
    }

    public function updatePeriod(Request $request, OathPeriod $period): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'quarter_code' => ['required', 'string'],
            'access_token' => ['nullable', 'string', 'max:50'],
            'drive_url' => ['nullable', 'url'],
            'oath_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'status' => ['required', 'in:draft,active,archived'],
        ]);

        if ($validated['status'] === 'active') {
            OathPeriod::where('study_program_id', $period->study_program_id)
                ->where('id', '!=', $period->id)
                ->where('status', 'active')
                ->update(['status' => 'archived']);
        }

        $token = ! empty($validated['access_token'])
            ? strtoupper(trim($validated['access_token']))
            : ($period->access_token ?: 'TK-'.strtoupper(Str::random(6)));

        $oathPdfPath = $period->oath_pdf_path;
        if ($request->hasFile('oath_pdf')) {
            $oathPdfPath = $request->file('oath_pdf')->store('oath_pdfs', 'public');
        }

        $period->update([
            'name' => $validated['name'],
            'event_date' => $validated['event_date'],
            'quarter_code' => $validated['quarter_code'],
            'access_token' => $token,
            'drive_url' => $validated['drive_url'] ?? null,
            'oath_pdf_path' => $oathPdfPath,
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', 'Data periode sumpah berhasil diperbarui.');
    }

    public function updatePeriodStatus(Request $request, OathPeriod $period): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:draft,active,archived'],
        ]);

        if ($validated['status'] === 'active') {
            OathPeriod::where('study_program_id', $period->study_program_id)
                ->where('id', '!=', $period->id)
                ->where('status', 'active')
                ->update(['status' => 'archived']);
        }

        $period->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Status periode berhasil diperbarui.');
    }

    public function toggleLock(OathPeriod $period): RedirectResponse
    {
        $period->update(['is_locked' => ! $period->is_locked]);
        $statusText = $period->is_locked ? 'DIKUNCI' : 'DIBUKA';

        return redirect()->back()->with('success', "Pendaftaran & edit data peserta untuk {$period->name} berhasil {$statusText}.");
    }

    // --- CANDIDATES MANAGEMENT & CLERIC RECAP (FR-C2, FR-E3) ---
    public function exportCandidates(Request $request)
    {
        $prodiId = $this->getAdminProdiId();

        $activePeriod = OathPeriod::where('study_program_id', $prodiId)->where('status', 'active')->first();
        $selectedPeriodId = $request->get('period_id', $activePeriod?->id);

        $candidatesQuery = OathCandidate::query();

        if ($selectedPeriodId) {
            $candidatesQuery->where('period_id', $selectedPeriodId);
        } else {
            $candidatesQuery->whereHas('period', function ($q) use ($prodiId) {
                $q->where('study_program_id', $prodiId);
            });
        }

        if ($request->filled('religion')) {
            $candidatesQuery->where('religion', $request->religion);
        }

        if ($request->filled('search')) {
            $candidatesQuery->where(function ($q) use ($request) {
                $q->where('full_name', 'like', '%'.$request->search.'%')
                    ->orWhere('nim', 'like', '%'.$request->search.'%');
            });
        }

        $candidates = $candidatesQuery->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();

        // Resolve period info for the title block
        $selectedPeriod = $selectedPeriodId
            ? OathPeriod::find($selectedPeriodId)
            : null;

        $periodName = $selectedPeriod?->name ?? 'Semua Periode';
        $eventDate = $selectedPeriod?->event_date?->format('d F Y') ?? '-';

        $fileName = 'Daftar_Kandidat_Peserta_'.date('Ymd_His').'.xlsx';

        return Excel::download(new CandidatesExport($candidates, $periodName, $eventDate), $fileName);
    }

    public function candidates(Request $request): View
    {
        $prodiId = $this->getAdminProdiId();

        $activePeriod = OathPeriod::where('study_program_id', $prodiId)->where('status', 'active')->first();
        $selectedPeriodId = $request->get('period_id', $activePeriod?->id);

        $periods = OathPeriod::where('study_program_id', $prodiId)->orderBy('event_date', 'desc')->get();

        $candidatesQuery = OathCandidate::with('period');

        if ($selectedPeriodId) {
            $candidatesQuery->where('period_id', $selectedPeriodId);
        } else {
            $candidatesQuery->whereHas('period', function ($q) use ($prodiId) {
                $q->where('study_program_id', $prodiId);
            });
        }

        if ($request->filled('religion')) {
            $candidatesQuery->where('religion', $request->religion);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $candidatesQuery->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $candidates = $candidatesQuery->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->paginate(15);

        // Cleric recap for selected period
        $clericRecap = collect();
        if ($selectedPeriodId) {
            $clericRecap = OathCandidate::where('period_id', $selectedPeriodId)
                ->select('religion', DB::raw('count(*) as total'))
                ->groupBy('religion')
                ->get();
        }

        return view('admin.candidates.index', compact('candidates', 'periods', 'selectedPeriodId', 'clericRecap'));
    }

    public function moveCandidate(Request $request, OathCandidate $candidate): RedirectResponse
    {
        $direction = $request->input('direction', 'up');

        $candidates = OathCandidate::where('period_id', $candidate->period_id)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $currentIndex = $candidates->search(fn ($c) => $c->id === $candidate->id);

        if ($currentIndex !== false) {
            $targetIndex = $direction === 'up' ? $currentIndex - 1 : $currentIndex + 1;

            if ($targetIndex >= 0 && $targetIndex < $candidates->count()) {
                $targetCandidate = $candidates[$targetIndex];

                $candidates[$currentIndex] = $targetCandidate;
                $candidates[$targetIndex] = $candidate;

                foreach ($candidates->values() as $index => $c) {
                    $c->update(['sort_order' => $index + 1]);
                }

                return redirect()->back()->with('success', "Urutan nomor peserta {$candidate->full_name} berhasil diubah!");
            }
        }

        return redirect()->back();
    }

    public function reorderCandidates(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'orders' => ['required', 'array'],
            'orders.*' => ['required', 'integer', 'min:1'],
        ]);

        foreach ($validated['orders'] as $candidateId => $sortOrder) {
            OathCandidate::where('id', $candidateId)->update(['sort_order' => (int) $sortOrder]);
        }

        return redirect()->back()->with('success', 'Urutan nomor seluruh peserta berhasil disimpan!');
    }

    // FR-E3: Assign 1 Speech Representative (auto unsets previous candidate)
    public function assignSpeechRep(OathCandidate $candidate): RedirectResponse
    {
        DB::transaction(function () use ($candidate) {
            // Unset any existing representative for this period
            OathCandidate::where('period_id', $candidate->period_id)
                ->update(['is_speech_rep' => false]);

            // Set new representative using query update
            OathCandidate::where('id', $candidate->id)->update([
                'is_speech_rep' => true,
                'speech_notes' => 'Perwakilan Pesan & Kesan Resmi '.$candidate->period->name,
            ]);
        });

        return redirect()->back()->with('success', "{$candidate->full_name} berhasil ditetapkan sebagai Perwakilan Pesan & Kesan!");
    }

    // Assign 1 Oath Coordinator (auto unsets previous coordinator)
    public function assignOathCoordinator(OathCandidate $candidate): RedirectResponse
    {
        DB::transaction(function () use ($candidate) {
            // Unset any existing coordinator for this period
            OathCandidate::where('period_id', $candidate->period_id)
                ->update(['is_oath_coordinator' => false]);

            // Set new coordinator
            OathCandidate::where('id', $candidate->id)->update([
                'is_oath_coordinator' => true,
                'coordinator_notes' => 'Koordinator Sumpah Resmi '.$candidate->period->name,
            ]);
        });

        return redirect()->back()->with('success', "{$candidate->full_name} berhasil ditetapkan sebagai Koordinator Sumpah!");
    }

    // --- PHOTOGRAPHER MANAGEMENT (FR-E4: MAX 3 PHOTOGRAPHERS WITH LOCKING) ---
    public function photographers(Request $request): View
    {
        $prodiId = $this->getAdminProdiId();
        $periods = OathPeriod::where('study_program_id', $prodiId)->orderBy('event_date', 'desc')->get();
        $activePeriod = $periods->firstWhere('status', 'active');
        $selectedPeriodId = $request->get('period_id', $activePeriod?->id);

        $photographers = collect();
        if ($selectedPeriodId) {
            $photographers = Photographer::where('period_id', $selectedPeriodId)->get();
        }

        return view('admin.photographers.index', compact('periods', 'selectedPeriodId', 'photographers', 'activePeriod'));
    }

    public function storePhotographer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'period_id' => ['required', 'exists:oath_periods,id'],
            'name' => ['required', 'string', 'max:255'],
            'agency_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:50'],
        ]);

        try {
            DB::transaction(function () use ($validated, &$photographer) {
                // Pessimistic count check inside transaction
                $count = Photographer::where('period_id', $validated['period_id'])->lockForUpdate()->count();

                if ($count >= 3) {
                    throw new \Exception('Batas maksimum 3 fotografer resmi untuk periode ini telah tercapai! Pendaftaran ke-4 ditolak.');
                }

                $period = OathPeriod::with('studyProgram')->findOrFail($validated['period_id']);
                $badgeSeq = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
                $badgeCode = "PHOTO-{$period->studyProgram->code}-".date('Y')."-{$badgeSeq}";

                $photographer = Photographer::create([
                    'period_id' => $period->id,
                    'name' => $validated['name'],
                    'agency_name' => $validated['agency_name'],
                    'phone_number' => $validated['phone_number'],
                    'badge_code' => $badgeCode,
                ]);
            });

            return redirect()->back()->with('success', 'Fotografer resmi berhasil terdaftar dengan E-Badge Code: '.$photographer->badge_code);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroyPhotographer(Photographer $photographer): RedirectResponse
    {
        $photographer->delete();

        return redirect()->back()->with('success', 'Fotografer berhasil dihapus dari daftar kuota.');
    }

    public function printBadge(Photographer $photographer): View
    {
        $photographer->load(['period.studyProgram']);

        return view('admin.photographers.badge', compact('photographer'));
    }

    // --- DOCUMENTATION VENDOR & LIVE STREAMING (RUJUKAN LINTAS PERIODE) ---
    public function vendors(Request $request): View
    {
        $prodiId = $this->getAdminProdiId();
        $prodi = StudyProgram::find($prodiId);
        $category = $request->get('category');

        $query = DocumentationVendor::with('studyProgram')
            ->where(function ($q) use ($prodiId) {
                $q->whereNull('study_program_id')->orWhere('study_program_id', $prodiId);
            });

        if ($category && in_array($category, ['photographer', 'live_streaming'])) {
            $query->where('category', $category);
        }

        $vendors = $query->orderBy('sort_order')->orderBy('name')->get();

        $photographerVendorsCount = DocumentationVendor::where(function ($q) use ($prodiId) {
            $q->whereNull('study_program_id')->orWhere('study_program_id', $prodiId);
        })->where('category', 'photographer')->count();

        $streamingVendorsCount = DocumentationVendor::where(function ($q) use ($prodiId) {
            $q->whereNull('study_program_id')->orWhere('study_program_id', $prodiId);
        })->where('category', 'live_streaming')->count();

        $studyPrograms = StudyProgram::all();

        return view('admin.vendors.index', compact(
            'vendors',
            'category',
            'prodi',
            'studyPrograms',
            'photographerVendorsCount',
            'streamingVendorsCount'
        ));
    }

    public function storeVendor(Request $request): RedirectResponse
    {
        $prodiId = $this->getAdminProdiId();

        $validated = $request->validate([
            'category' => ['required', 'in:photographer,live_streaming'],
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'whatsapp_number' => ['required', 'string', 'max:50'],
            'instagram_handle' => ['nullable', 'string', 'max:100'],
            'youtube_channel' => ['nullable', 'string', 'max:255'],
            'price_estimate' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'is_global' => ['nullable'],
        ]);

        DocumentationVendor::create([
            'study_program_id' => ! empty($validated['is_global']) ? null : $prodiId,
            'category' => $validated['category'],
            'name' => $validated['name'],
            'contact_person' => $validated['contact_person'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'],
            'instagram_handle' => $validated['instagram_handle'] ?? null,
            'youtube_channel' => $validated['youtube_channel'] ?? null,
            'price_estimate' => $validated['price_estimate'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => true,
            'sort_order' => (DocumentationVendor::max('sort_order') ?? 0) + 1,
        ]);

        $label = $validated['category'] === 'live_streaming' ? 'Jasa Streaming Live Youtube' : 'Kontak Fotografer';

        return redirect()->back()->with('success', "{$label} berhasil disimpan sebagai rekomendasi periode sumpah!");
    }

    public function updateVendor(Request $request, DocumentationVendor $vendor): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'in:photographer,live_streaming'],
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'whatsapp_number' => ['required', 'string', 'max:50'],
            'instagram_handle' => ['nullable', 'string', 'max:100'],
            'youtube_channel' => ['nullable', 'string', 'max:255'],
            'price_estimate' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable'],
        ]);

        $vendor->update([
            'category' => $validated['category'],
            'name' => $validated['name'],
            'contact_person' => $validated['contact_person'] ?? null,
            'whatsapp_number' => $validated['whatsapp_number'],
            'instagram_handle' => $validated['instagram_handle'] ?? null,
            'youtube_channel' => $validated['youtube_channel'] ?? null,
            'price_estimate' => $validated['price_estimate'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : $vendor->is_active,
        ]);

        return redirect()->back()->with('success', 'Data vendor rekomendasi berhasil diperbarui!');
    }

    public function destroyVendor(DocumentationVendor $vendor): RedirectResponse
    {
        $vendor->delete();

        return redirect()->back()->with('success', 'Kontak vendor berhasil dihapus dari rekomendasi.');
    }

    public function toggleVendor(DocumentationVendor $vendor): RedirectResponse
    {
        $vendor->update(['is_active' => ! $vendor->is_active]);

        $status = $vendor->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()->with('success', "Status vendor '{$vendor->name}' berhasil {$status}.");
    }

    // --- ADMIN CONTACT MANAGEMENT (FR-F1/F2) ---
    public function contacts(): View
    {
        $prodiId = $this->getAdminProdiId();
        $contacts = ContactPerson::where('study_program_id', $prodiId)->get();

        return view('admin.contacts.index', compact('contacts'));
    }

    public function storeContact(Request $request): RedirectResponse
    {
        $prodiId = $this->getAdminProdiId();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        ContactPerson::create([
            'study_program_id' => $prodiId,
            'category' => 'admin',
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->back()->with('success', 'Narahubung admin prodi berhasil ditambahkan.');
    }

    public function toggleContact(ContactPerson $contact): RedirectResponse
    {
        $contact->update(['is_active' => ! $contact->is_active]);

        return redirect()->back()->with('success', 'Status penayangan kontak berhasil diperbarui.');
    }

    // --- GALLERY & REPOSITORY CURATION (FR-G2, FR-G3) ---
    public function gallery(Request $request): View
    {
        $prodiId = $this->getAdminProdiId();
        $periods = OathPeriod::where('study_program_id', $prodiId)->orderBy('event_date', 'desc')->get();
        $activePeriod = $periods->firstWhere('status', 'active');
        $selectedPeriodId = $request->get('period_id', $activePeriod?->id);

        $period = null;
        $photos = collect();

        if ($selectedPeriodId) {
            $period = OathPeriod::with('eventPhotos')->find($selectedPeriodId);
            $photos = $period ? $period->eventPhotos : collect();
        }

        return view('admin.gallery.index', compact('periods', 'selectedPeriodId', 'period', 'photos'));
    }

    public function storePhoto(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'period_id' => ['required', 'exists:oath_periods,id'],
            'photo' => ['required', 'image', 'max:5120'], // Max 5MB
            'caption' => ['nullable', 'string', 'max:255'],
        ]);

        $period = OathPeriod::findOrFail($validated['period_id']);

        if ($period->eventPhotos()->count() >= 10) {
            return redirect()->back()->with('error', 'Kurasi galeri foto dibatasi tepat maksimal 10 foto terbaik per periode!');
        }

        $path = $request->file('photo')->store('event_photos', 'public');
        $nextOrder = $period->eventPhotos()->count() + 1;

        EventPhoto::create([
            'period_id' => $period->id,
            'photo_path' => $path,
            'caption' => $validated['caption'],
            'sort_order' => $nextOrder,
        ]);

        return redirect()->back()->with('success', 'Foto kurasi berhasil diunggah.');
    }

    public function updateYoutube(Request $request, OathPeriod $period): RedirectResponse
    {
        $validated = $request->validate([
            'youtube_url' => ['nullable', 'url'],
        ]);

        $period->update(['youtube_url' => $validated['youtube_url']]);

        return redirect()->back()->with('success', 'Tautan video dokumentasi YouTube berhasil diperbarui.');
    }

    public function destroyPhoto(EventPhoto $photo): RedirectResponse
    {
        $photo->delete();

        return redirect()->back()->with('success', 'Foto berhasil dihapus dari kurasi.');
    }

    // --- SUSUNAN ACARA & TATA TERTIB MANAGEMENT ---
    public function eventSchedules(Request $request): View
    {
        $prodiId = $this->getAdminProdiId();
        $periods = OathPeriod::where('study_program_id', $prodiId)
            ->withCount(['schedules', 'rules'])
            ->orderBy('event_date', 'desc')
            ->get();

        $selectedPeriodId = $request->get('period_id', $periods->firstWhere('status', 'active')?->id ?? $periods->first()?->id);
        $selectedPeriod = $periods->firstWhere('id', $selectedPeriodId);

        $schedules = $selectedPeriod ? $selectedPeriod->schedules()->orderBy('sort_order', 'asc')->orderBy('start_time', 'asc')->get() : collect();
        $rules = $selectedPeriod ? $selectedPeriod->rules()->orderBy('sort_order', 'asc')->get() : collect();

        return view('admin.event-schedules.index', compact('periods', 'selectedPeriod', 'schedules', 'rules', 'selectedPeriodId'));
    }

    public function storeSchedule(Request $request): RedirectResponse
    {
        $prodiId = $this->getAdminProdiId();
        $validated = $request->validate([
            'period_id' => ['required', 'exists:oath_periods,id'],
            'start_time' => ['required', 'string', 'max:20'],
            'end_time' => ['nullable', 'string', 'max:20'],
            'activity_name' => ['required', 'string', 'max:255'],
            'person_in_charge' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $period = OathPeriod::where('study_program_id', $prodiId)->findOrFail($validated['period_id']);

        $sortOrder = $validated['sort_order'] ?? ($period->schedules()->max('sort_order') ?? 0) + 1;

        EventSchedule::create([
            'period_id' => $period->id,
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'] ?? null,
            'activity_name' => $validated['activity_name'],
            'person_in_charge' => $validated['person_in_charge'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'sort_order' => (int) $sortOrder,
        ]);

        return redirect()->route('admin.event-schedules.index', ['period_id' => $period->id])
            ->with('success', 'Agenda susunan acara baru berhasil ditambahkan.');
    }

    public function updateSchedule(Request $request, EventSchedule $schedule): RedirectResponse
    {
        $prodiId = $this->getAdminProdiId();
        if ($schedule->period->study_program_id !== $prodiId) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $validated = $request->validate([
            'start_time' => ['required', 'string', 'max:20'],
            'end_time' => ['nullable', 'string', 'max:20'],
            'activity_name' => ['required', 'string', 'max:255'],
            'person_in_charge' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $schedule->update([
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'] ?? null,
            'activity_name' => $validated['activity_name'],
            'person_in_charge' => $validated['person_in_charge'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'sort_order' => $validated['sort_order'] ?? $schedule->sort_order,
        ]);

        return redirect()->route('admin.event-schedules.index', ['period_id' => $schedule->period_id])
            ->with('success', 'Agenda susunan acara berhasil diperbarui.');
    }

    public function destroySchedule(EventSchedule $schedule): RedirectResponse
    {
        $prodiId = $this->getAdminProdiId();
        if ($schedule->period->study_program_id !== $prodiId) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $periodId = $schedule->period_id;
        $schedule->delete();

        return redirect()->route('admin.event-schedules.index', ['period_id' => $periodId])
            ->with('success', 'Agenda susunan acara berhasil dihapus.');
    }

    public function storeRule(Request $request): RedirectResponse
    {
        $prodiId = $this->getAdminProdiId();
        $validated = $request->validate([
            'period_id' => ['required', 'exists:oath_periods,id'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'is_strict' => ['nullable'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $period = OathPeriod::where('study_program_id', $prodiId)->findOrFail($validated['period_id']);

        $sortOrder = $validated['sort_order'] ?? ($period->rules()->max('sort_order') ?? 0) + 1;

        EventRule::create([
            'period_id' => $period->id,
            'title' => $validated['title'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'is_strict' => $request->boolean('is_strict'),
            'sort_order' => (int) $sortOrder,
        ]);

        return redirect()->route('admin.event-schedules.index', ['period_id' => $period->id])
            ->with('success', 'Tata tertib baru berhasil ditambahkan.');
    }

    public function updateRule(Request $request, EventRule $rule): RedirectResponse
    {
        $prodiId = $this->getAdminProdiId();
        if ($rule->period->study_program_id !== $prodiId) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'is_strict' => ['nullable'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $rule->update([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'is_strict' => $request->boolean('is_strict'),
            'sort_order' => $validated['sort_order'] ?? $rule->sort_order,
        ]);

        return redirect()->route('admin.event-schedules.index', ['period_id' => $rule->period_id])
            ->with('success', 'Tata tertib berhasil diperbarui.');
    }

    public function destroyRule(EventRule $rule): RedirectResponse
    {
        $prodiId = $this->getAdminProdiId();
        if ($rule->period->study_program_id !== $prodiId) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $periodId = $rule->period_id;
        $rule->delete();

        return redirect()->route('admin.event-schedules.index', ['period_id' => $periodId])
            ->with('success', 'Tata tertib berhasil dihapus.');
    }

    public function loadTemplate(Request $request): RedirectResponse
    {
        $prodiId = $this->getAdminProdiId();
        $validated = $request->validate([
            'period_id' => ['required', 'exists:oath_periods,id'],
            'apply_all' => ['nullable', 'boolean'],
        ]);

        $period = OathPeriod::with('studyProgram')->where('study_program_id', $prodiId)->findOrFail($validated['period_id']);
        $prodiCode = $period->studyProgram->code ?? 'KED';

        if ($request->boolean('apply_all')) {
            $allPeriods = OathPeriod::where('study_program_id', $prodiId)->get();
            $updatedCount = 0;
            $skippedLockedCount = 0;

            foreach ($allPeriods as $per) {
                if ($per->is_locked) {
                    $skippedLockedCount++;

                    continue;
                }

                $per->schedules()->delete();
                $per->rules()->delete();

                foreach (EventSchedule::getDefaultTemplateForProdi($prodiCode) as $sched) {
                    $per->schedules()->create($sched);
                }

                foreach (EventRule::getDefaultTemplateForProdi($prodiCode) as $rul) {
                    $per->rules()->create($rul);
                }

                $updatedCount++;
            }

            $msg = "Template susunan acara dan tata tertib resmi Program Studi {$period->studyProgram->name} ({$prodiCode}) berhasil diterapkan ke SEMUA ({$updatedCount}) periode!";
            if ($skippedLockedCount > 0) {
                $msg .= " ({$skippedLockedCount} periode terkunci dilewati).";
            }

            return redirect()->route('admin.event-schedules.index', ['period_id' => $period->id])
                ->with('success', $msg);
        }

        if ($period->is_locked) {
            return redirect()->back()->with('error', 'Periode ini telah dikunci oleh panitia admin prodi. Tidak dapat memuat template.');
        }

        $period->schedules()->delete();
        $period->rules()->delete();

        $defaultSchedules = EventSchedule::getDefaultTemplateForProdi($prodiCode);
        $defaultRules = EventRule::getDefaultTemplateForProdi($prodiCode);

        foreach ($defaultSchedules as $sched) {
            $period->schedules()->create($sched);
        }

        foreach ($defaultRules as $rul) {
            $period->rules()->create($rul);
        }

        return redirect()->route('admin.event-schedules.index', ['period_id' => $period->id])
            ->with('success', "Template susunan acara dan tata tertib resmi Program Studi {$period->studyProgram->name} ({$prodiCode}) berhasil dimuat untuk periode ini!");
    }
}
