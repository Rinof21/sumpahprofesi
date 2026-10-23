<?php

namespace App\Http\Controllers;

use App\Models\ContactPerson;
use App\Models\DocumentationVendor;
use App\Models\OathCandidate;
use App\Models\OathPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TokenAccessController extends Controller
{
    /**
     * Check and return verified period ID if within 60 minutes window.
     */
    private function getVerifiedPeriodId(Request $request): ?int
    {
        $sessionPeriodId = session('verified_period_id');
        $sessionExpiresAt = session('verified_period_expires_at');

        if ($sessionPeriodId && $sessionExpiresAt && now()->timestamp < $sessionExpiresAt) {
            return (int) $sessionPeriodId;
        }

        // Cookie fallback (persists across browser restarts / logouts within 60 mins)
        $cookieData = $request->cookie('verified_period_access');
        if ($cookieData) {
            $data = json_decode($cookieData, true);
            if (is_array($data) && isset($data['period_id'], $data['expires_at'])) {
                if (now()->timestamp < $data['expires_at']) {
                    session([
                        'verified_period_id' => $data['period_id'],
                        'verified_period_expires_at' => $data['expires_at'],
                    ]);

                    return (int) $data['period_id'];
                }
            }
        }

        session()->forget(['verified_period_id', 'verified_period_expires_at']);

        return null;
    }

    /**
     * Store 60-minute token verification in session and cookie.
     */
    private function storeVerifiedTokenSession(int $periodId): void
    {
        $expiresAt = now()->addMinutes(60)->timestamp;

        session([
            'verified_period_id' => $periodId,
            'verified_period_expires_at' => $expiresAt,
        ]);

        Cookie::queue(
            'verified_period_access',
            json_encode(['period_id' => $periodId, 'expires_at' => $expiresAt]),
            60 // 60 minutes
        );
    }

    /**
     * Clear token session and cookie.
     */
    private function clearVerifiedTokenSession(): void
    {
        session()->forget(['verified_period_id', 'verified_period_expires_at']);
        Cookie::queue(Cookie::forget('verified_period_access'));
    }

    public function showTokenForm(Request $request): View|RedirectResponse
    {
        $verifiedPeriodId = $this->getVerifiedPeriodId($request);
        if ($verifiedPeriodId) {
            return redirect()->route('token-access.portal')->with('info', 'Token Anda telah terverifikasi dan masih aktif (berlaku 60 menit).');
        }

        $activePeriods = OathPeriod::with('studyProgram')
            ->where('status', 'active')
            ->orderBy('event_date', 'asc')
            ->get();

        return view('public.token-verify', compact('activePeriods'));
    }

    public function verifyToken(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'period_id' => ['required', 'exists:oath_periods,id'],
            'access_token' => ['required', 'string'],
        ]);

        $period = OathPeriod::with('studyProgram')->findOrFail($validated['period_id']);

        if ($period->access_token && strtoupper(trim($validated['access_token'])) !== strtoupper(trim($period->access_token))) {
            return redirect()->back()->withInput()->with('error', 'Kode Token Akses Periode Salah! Silakan tanyakan kode token resmi kepada Panitia Admin Prodi.');
        }

        // Store 60-minute session and cookie
        $this->storeVerifiedTokenSession($period->id);

        return redirect()->route('token-access.portal')->with('success', "Akses Terverifikasi! Selamat datang di Portal Sumpah Profesi {$period->studyProgram->name}. (Akses aktif selama 60 menit).");
    }

    public function resetTokenSession(): RedirectResponse
    {
        $this->clearVerifiedTokenSession();

        return redirect()->route('token-access.index')->with('info', 'Sesi token berhasil direset. Silakan masukkan kode token periode yang baru.');
    }

    public function portal(Request $request): View|RedirectResponse
    {
        $periodId = $this->getVerifiedPeriodId($request);

        if (! $periodId) {
            return redirect()->route('token-access.index')->with('error', 'Sesi token telah berakhir (60 menit) atau belum dimasukkan. Silakan masukkan kode token akses.');
        }

        $period = OathPeriod::with(['studyProgram', 'schedules', 'rules', 'candidates' => function ($q) {
            $q->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
        }])->findOrFail($periodId);

        $expiresAt = session('verified_period_expires_at');
        $remainingMinutes = $expiresAt ? max(1, ceil(($expiresAt - now()->timestamp) / 60)) : 60;

        $adminContacts = ContactPerson::where('study_program_id', $period->study_program_id)
            ->where('category', 'admin')
            ->where('is_active', true)
            ->get();

        $itContacts = ContactPerson::whereNull('study_program_id')
            ->where('category', 'it')
            ->where('is_active', true)
            ->get();

        $vendors = DocumentationVendor::active()
            ->where(function ($q) use ($period) {
                $q->whereNull('study_program_id')->orWhere('study_program_id', $period->study_program_id);
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('public.token-portal', compact('period', 'adminContacts', 'itContacts', 'vendors', 'remainingMinutes'));
    }

    public function eventSchedule(Request $request): View|RedirectResponse
    {
        $periodId = $this->getVerifiedPeriodId($request);

        if (! $periodId) {
            return redirect()->route('token-access.index')->with('error', 'Sesi token telah berakhir (60 menit) atau belum dimasukkan. Silakan masukkan kode token akses.');
        }

        $period = OathPeriod::with(['studyProgram', 'schedules', 'rules'])->findOrFail($periodId);

        $expiresAt = session('verified_period_expires_at');
        $remainingMinutes = $expiresAt ? max(1, ceil(($expiresAt - now()->timestamp) / 60)) : 60;

        return view('public.token-event-schedule', compact('period', 'remainingMinutes'));
    }

    public function oathScript(Request $request): View|RedirectResponse
    {
        $periodId = $this->getVerifiedPeriodId($request);

        if (! $periodId) {
            return redirect()->route('token-access.index')->with('error', 'Sesi token telah berakhir (60 menit) atau belum dimasukkan. Silakan masukkan kode token akses.');
        }

        $period = OathPeriod::with('studyProgram')->findOrFail($periodId);

        $expiresAt = session('verified_period_expires_at');
        $remainingMinutes = $expiresAt ? max(1, ceil(($expiresAt - now()->timestamp) / 60)) : 60;

        return view('public.token-oath-script', compact('period', 'remainingMinutes'));
    }

    public function storeCandidate(Request $request): RedirectResponse
    {
        $periodId = $this->getVerifiedPeriodId($request);
        $period = OathPeriod::findOrFail($request->period_id);

        if (! $periodId || $period->id != $periodId) {
            return redirect()->route('token-access.index')->with('error', 'Sesi token tidak valid atau telah berakhir (60 menit). Silakan masukkan token kembali.');
        }

        if ($period->is_locked) {
            return redirect()->back()->withInput()->with('error', 'Pendaftaran & pengisian data untuk periode ini telah DIKUNCI oleh Admin Prodi!');
        }

        $validated = $request->validate([
            'period_id' => ['required', 'exists:oath_periods,id'],
            'sort_order' => ['nullable', 'integer', 'min:1'],
            'nim' => ['required', 'string', 'unique:oath_candidates,nim'],
            'nik' => ['nullable', 'string'],
            'full_name' => ['required', 'string'],
            'birth_place' => ['required', 'string'],
            'birth_date' => ['required', 'date'],
            'father_name' => ['required', 'string'],
            'mother_name' => ['required', 'string'],
            'admission_path' => ['required', 'string'],
            'religion' => ['required', 'string'],
        ]);

        $sortOrder = ! empty($validated['sort_order'])
            ? (int) $validated['sort_order']
            : (($period->candidates()->max('sort_order') ?? 0) + 1);

        $isCoord = $request->boolean('is_oath_coordinator');
        $isRep = $request->boolean('is_speech_rep');

        if ($isCoord) {
            OathCandidate::where('period_id', $period->id)->update(['is_oath_coordinator' => false]);
        }
        if ($isRep) {
            OathCandidate::where('period_id', $period->id)->update(['is_speech_rep' => false]);
        }

        OathCandidate::create([
            'period_id' => $period->id,
            'sort_order' => $sortOrder,
            'user_id' => auth()->check() ? auth()->id() : null,
            'nim' => $validated['nim'],
            'nik' => $validated['nik'] ?? null,
            'full_name' => $validated['full_name'],
            'birth_place' => $validated['birth_place'],
            'birth_date' => $validated['birth_date'],
            'father_name' => $validated['father_name'],
            'mother_name' => $validated['mother_name'],
            'admission_path' => $validated['admission_path'],
            'religion' => $validated['religion'],
            'agreed_rules' => true,
            'agreed_at' => now(),
            'is_oath_coordinator' => $isCoord,
            'coordinator_notes' => $isCoord ? 'Koordinator Sumpah Resmi '.$period->name : null,
            'is_speech_rep' => $isRep,
            'speech_notes' => $isRep ? 'Perwakilan Pesan & Kesan Resmi '.$period->name : null,
        ]);

        return redirect()->route('token-access.portal')->with('success', "Biodata peserta {$validated['full_name']} (NIM: {$validated['nim']}, No. Urut: {$sortOrder}) berhasil ditambahkan!");
    }

    public function updateCandidate(Request $request, OathCandidate $candidate): RedirectResponse
    {
        $periodId = $this->getVerifiedPeriodId($request);
        if (! $periodId || $candidate->period_id != $periodId) {
            return redirect()->route('token-access.index')->with('error', 'Sesi token tidak valid atau telah berakhir (60 menit).');
        }

        if ($candidate->period && $candidate->period->is_locked) {
            return redirect()->back()->with('error', 'Pendaftaran dan edit data telah DIKUNCI oleh Admin Prodi. Perubahan tidak dapat dilakukan.');
        }

        $validated = $request->validate([
            'sort_order' => ['nullable', 'integer', 'min:1'],
            'nim' => ['required', 'string', 'unique:oath_candidates,nim,'.$candidate->id],
            'nik' => ['nullable', 'string'],
            'full_name' => ['required', 'string'],
            'birth_place' => ['required', 'string'],
            'birth_date' => ['required', 'date'],
            'father_name' => ['required', 'string'],
            'mother_name' => ['required', 'string'],
            'admission_path' => ['required', 'string'],
            'religion' => ['required', 'string'],
        ]);

        if ($request->has('is_oath_coordinator')) {
            $isCoord = $request->boolean('is_oath_coordinator');
            if ($isCoord) {
                OathCandidate::where('period_id', $candidate->period_id)
                    ->where('id', '!=', $candidate->id)
                    ->update(['is_oath_coordinator' => false]);
                $candidate->is_oath_coordinator = true;
                $candidate->coordinator_notes = 'Koordinator Sumpah Resmi '.$candidate->period->name;
            } else {
                $candidate->is_oath_coordinator = false;
            }
        }

        if ($request->has('is_speech_rep')) {
            $isRep = $request->boolean('is_speech_rep');
            if ($isRep) {
                OathCandidate::where('period_id', $candidate->period_id)
                    ->where('id', '!=', $candidate->id)
                    ->update(['is_speech_rep' => false]);
                $candidate->is_speech_rep = true;
                $candidate->speech_notes = 'Perwakilan Pesan & Kesan Resmi '.$candidate->period->name;
            } else {
                $candidate->is_speech_rep = false;
            }
        }

        $candidate->fill($validated);
        $candidate->save();

        return redirect()->route('token-access.portal')->with('success', "Data peserta {$candidate->full_name} berhasil diperbarui!");
    }

    public function moveCandidate(Request $request, OathCandidate $candidate): RedirectResponse
    {
        $periodId = $this->getVerifiedPeriodId($request);
        if (! $periodId || $candidate->period_id != $periodId) {
            return redirect()->route('token-access.index')->with('error', 'Sesi token tidak valid atau telah berakhir (60 menit).');
        }

        if ($candidate->period && $candidate->period->is_locked) {
            return redirect()->back()->with('error', 'Pendaftaran telah DIKUNCI oleh Admin Prodi. Urutan nomor peserta tidak dapat diubah.');
        }

        $direction = $request->input('direction', 'up');

        // Fetch all candidates of this period ordered by sort_order, id
        $candidates = OathCandidate::where('period_id', $candidate->period_id)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $currentIndex = $candidates->search(fn ($c) => $c->id === $candidate->id);

        if ($currentIndex === false) {
            return redirect()->route('token-access.portal');
        }

        $targetIndex = $direction === 'up' ? $currentIndex - 1 : $currentIndex + 1;

        if ($targetIndex >= 0 && $targetIndex < $candidates->count()) {
            $targetCandidate = $candidates[$targetIndex];

            // Normalize sort orders: assign 1, 2, 3... to all, but swap currentIndex with targetIndex
            $candidates[$currentIndex] = $targetCandidate;
            $candidates[$targetIndex] = $candidate;

            foreach ($candidates->values() as $index => $c) {
                $c->update(['sort_order' => $index + 1]);
            }

            return redirect()->route('token-access.portal')->with('success', "Urutan nomor peserta {$candidate->full_name} berhasil diubah!");
        }

        return redirect()->route('token-access.portal');
    }

    public function reorderCandidates(Request $request): RedirectResponse
    {
        $periodId = $this->getVerifiedPeriodId($request);
        if (! $periodId) {
            return redirect()->route('token-access.index')->with('error', 'Sesi token tidak valid atau telah berakhir (60 menit).');
        }

        $period = OathPeriod::findOrFail($periodId);
        if ($period->is_locked) {
            return redirect()->back()->with('error', 'Pendaftaran telah DIKUNCI oleh Admin Prodi. Urutan nomor peserta tidak dapat diubah.');
        }

        $validated = $request->validate([
            'orders' => ['required', 'array'],
            'orders.*' => ['required', 'integer', 'min:1'],
        ]);

        foreach ($validated['orders'] as $candidateId => $sortOrder) {
            OathCandidate::where('id', $candidateId)
                ->where('period_id', $periodId)
                ->update(['sort_order' => (int) $sortOrder]);
        }

        return redirect()->route('token-access.portal')->with('success', 'Urutan nomor seluruh peserta berhasil disimpan!');
    }

    public function deleteCandidate(Request $request, OathCandidate $candidate): RedirectResponse
    {
        $periodId = $this->getVerifiedPeriodId($request);
        if (! $periodId || $candidate->period_id != $periodId) {
            return redirect()->route('token-access.index')->with('error', 'Sesi token tidak valid atau telah berakhir (60 menit).');
        }

        if ($candidate->period && $candidate->period->is_locked) {
            return redirect()->back()->with('error', 'Pendaftaran telah DIKUNCI oleh Admin Prodi. Data peserta tidak dapat dihapus.');
        }

        $fullName = $candidate->full_name;
        $candidate->delete();

        return redirect()->route('token-access.portal')->with('success', "Data peserta {$fullName} berhasil dihapus.");
    }

    public function uploadPpt(Request $request, OathCandidate $candidate): RedirectResponse
    {
        $periodId = $this->getVerifiedPeriodId($request);
        if (! $periodId || $candidate->period_id != $periodId) {
            return redirect()->route('token-access.index')->with('error', 'Sesi token tidak valid atau telah berakhir (60 menit).');
        }

        if ($candidate->period && $candidate->period->is_locked) {
            return redirect()->back()->with('error', 'Pendaftaran telah DIKUNCI oleh Admin Prodi. Slide PPT tidak dapat diunggah.');
        }

        $request->validate([
            'ppt_file' => ['required', 'file', 'mimes:ppt,pptx,pdf', 'max:20480'],
        ]);

        if ($request->hasFile('ppt_file')) {
            $path = $request->file('ppt_file')->store('ppt_slides', 'public');
            $candidate->update(['ppt_file_path' => $path]);
        }

        return redirect()->route('token-access.portal')->with('success', "Slide PPT profil untuk {$candidate->full_name} berhasil diunggah!");
    }

    public function assignSpeechRep(Request $request, OathCandidate $candidate): RedirectResponse
    {
        $periodId = $this->getVerifiedPeriodId($request);
        if (! $periodId || $candidate->period_id != $periodId) {
            return redirect()->route('token-access.index')->with('error', 'Sesi token tidak valid atau telah berakhir (60 menit).');
        }

        if ($candidate->period && $candidate->period->is_locked) {
            return redirect()->back()->with('error', 'Pendaftaran telah DIKUNCI oleh Admin Prodi. Penunjukan perwakilan tidak dapat diubah.');
        }

        $isCurrentlyRep = (bool) $candidate->is_speech_rep;

        DB::transaction(function () use ($candidate, $isCurrentlyRep) {
            OathCandidate::where('period_id', $candidate->period_id)
                ->update(['is_speech_rep' => false]);

            if (! $isCurrentlyRep) {
                OathCandidate::where('id', $candidate->id)->update([
                    'is_speech_rep' => true,
                    'speech_notes' => 'Perwakilan Pesan & Kesan Resmi '.$candidate->period->name,
                ]);
            }
        });

        $message = $isCurrentlyRep
            ? "Penunjukan {$candidate->full_name} sebagai pembaca Pesan & Kesan telah dibatalkan."
            : "{$candidate->full_name} berhasil ditunjuk sebagai Pembaca Pesan & Kesan!";

        return redirect()->route('token-access.portal')->with('success', $message);
    }

    public function assignOathCoordinator(Request $request, OathCandidate $candidate): RedirectResponse
    {
        $periodId = $this->getVerifiedPeriodId($request);
        if (! $periodId || $candidate->period_id != $periodId) {
            return redirect()->route('token-access.index')->with('error', 'Sesi token tidak valid atau telah berakhir (60 menit).');
        }

        if ($candidate->period && $candidate->period->is_locked) {
            return redirect()->back()->with('error', 'Pendaftaran telah DIKUNCI oleh Admin Prodi. Penunjukan koordinator tidak dapat diubah.');
        }

        $isCurrentlyCoord = (bool) $candidate->is_oath_coordinator;

        DB::transaction(function () use ($candidate, $isCurrentlyCoord) {
            OathCandidate::where('period_id', $candidate->period_id)
                ->update(['is_oath_coordinator' => false]);

            if (! $isCurrentlyCoord) {
                OathCandidate::where('id', $candidate->id)->update([
                    'is_oath_coordinator' => true,
                    'coordinator_notes' => 'Koordinator Sumpah Resmi '.$candidate->period->name,
                ]);
            }
        });

        $message = $isCurrentlyCoord
            ? "Penunjukan {$candidate->full_name} sebagai Koordinator Sumpah telah dibatalkan."
            : "{$candidate->full_name} berhasil ditunjuk sebagai Koordinator Sumpah Wisudawan!";

        return redirect()->route('token-access.portal')->with('success', $message);
    }

    public function assignRoles(Request $request): RedirectResponse
    {
        $periodId = $this->getVerifiedPeriodId($request);
        if (! $periodId) {
            return redirect()->route('token-access.index')->with('error', 'Sesi token tidak valid atau telah berakhir (60 menit).');
        }

        $period = OathPeriod::findOrFail($periodId);
        if ($period->is_locked) {
            return redirect()->back()->with('error', 'Pendaftaran telah DIKUNCI oleh Admin Prodi. Penunjukan peran tidak dapat diubah.');
        }

        $validated = $request->validate([
            'oath_coordinator_id' => ['nullable', 'exists:oath_candidates,id'],
            'speech_rep_id' => ['nullable', 'exists:oath_candidates,id'],
        ]);

        DB::transaction(function () use ($period, $validated) {
            OathCandidate::where('period_id', $period->id)
                ->update([
                    'is_oath_coordinator' => false,
                    'is_speech_rep' => false,
                ]);

            if (! empty($validated['oath_coordinator_id'])) {
                OathCandidate::where('id', $validated['oath_coordinator_id'])
                    ->where('period_id', $period->id)
                    ->update([
                        'is_oath_coordinator' => true,
                        'coordinator_notes' => 'Koordinator Sumpah Resmi '.$period->name,
                    ]);
            }

            if (! empty($validated['speech_rep_id'])) {
                OathCandidate::where('id', $validated['speech_rep_id'])
                    ->where('period_id', $period->id)
                    ->update([
                        'is_speech_rep' => true,
                        'speech_notes' => 'Perwakilan Pesan & Kesan Resmi '.$period->name,
                    ]);
            }
        });

        return redirect()->route('token-access.portal')->with('success', 'Penunjukan Koordinator Sumpah dan Pembaca Pesan & Kesan berhasil disimpan!');
    }
}
