<?php

namespace App\Http\Controllers;

use App\Models\ContactPerson;
use App\Models\OathCandidate;
use App\Models\OathPeriod;
use App\Models\StudyProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
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

        if (!$periodId) {
            return redirect()->route('token-access.index')->with('error', 'Sesi token telah berakhir (60 menit) atau belum dimasukkan. Silakan masukkan kode token akses.');
        }

        $period = OathPeriod::with(['studyProgram', 'candidates' => function ($q) {
            $q->orderBy('full_name', 'asc');
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

        return view('public.token-portal', compact('period', 'adminContacts', 'itContacts', 'remainingMinutes'));
    }

    public function storeCandidate(Request $request): RedirectResponse
    {
        $periodId = $this->getVerifiedPeriodId($request);
        $period = OathPeriod::findOrFail($request->period_id);

        if (!$periodId || $period->id != $periodId) {
            return redirect()->route('token-access.index')->with('error', 'Sesi token tidak valid atau telah berakhir (60 menit). Silakan masukkan token kembali.');
        }

        if ($period->is_locked) {
            return redirect()->back()->withInput()->with('error', 'Pendaftaran & pengisian data untuk periode ini telah DIKUNCI oleh Admin Prodi!');
        }

        $validated = $request->validate([
            'period_id' => ['required', 'exists:oath_periods,id'],
            'nim' => ['required', 'string', 'unique:oath_candidates,nim'],
            'nik' => ['required', 'string'],
            'full_name' => ['required', 'string'],
            'birth_place' => ['required', 'string'],
            'birth_date' => ['required', 'date'],
            'father_name' => ['required', 'string'],
            'mother_name' => ['required', 'string'],
            'admission_path' => ['required', 'string'],
            'religion' => ['required', 'string'],
        ]);

        OathCandidate::create([
            'period_id' => $period->id,
            'user_id' => auth()->check() ? auth()->id() : null,
            'nim' => $validated['nim'],
            'nik' => $validated['nik'],
            'full_name' => $validated['full_name'],
            'birth_place' => $validated['birth_place'],
            'birth_date' => $validated['birth_date'],
            'father_name' => $validated['father_name'],
            'mother_name' => $validated['mother_name'],
            'admission_path' => $validated['admission_path'],
            'religion' => $validated['religion'],
            'agreed_rules' => true,
            'agreed_at' => now(),
        ]);

        return redirect()->route('token-access.portal')->with('success', "Biodata peserta {$validated['full_name']} (NIM: {$validated['nim']}) berhasil ditambahkan!");
    }

    public function updateCandidate(Request $request, OathCandidate $candidate): RedirectResponse
    {
        $periodId = $this->getVerifiedPeriodId($request);
        if (!$periodId || $candidate->period_id != $periodId) {
            return redirect()->route('token-access.index')->with('error', 'Sesi token tidak valid atau telah berakhir (60 menit).');
        }

        if ($candidate->period && $candidate->period->is_locked) {
            return redirect()->back()->with('error', 'Pendaftaran dan edit data telah DIKUNCI oleh Admin Prodi. Perubahan tidak dapat dilakukan.');
        }

        $validated = $request->validate([
            'nim' => ['required', 'string', 'unique:oath_candidates,nim,' . $candidate->id],
            'nik' => ['required', 'string'],
            'full_name' => ['required', 'string'],
            'birth_place' => ['required', 'string'],
            'birth_date' => ['required', 'date'],
            'father_name' => ['required', 'string'],
            'mother_name' => ['required', 'string'],
            'admission_path' => ['required', 'string'],
            'religion' => ['required', 'string'],
        ]);

        $candidate->update($validated);

        return redirect()->route('token-access.portal')->with('success', "Data peserta {$candidate->full_name} berhasil diperbarui!");
    }

    public function deleteCandidate(Request $request, OathCandidate $candidate): RedirectResponse
    {
        $periodId = $this->getVerifiedPeriodId($request);
        if (!$periodId || $candidate->period_id != $periodId) {
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
        if (!$periodId || $candidate->period_id != $periodId) {
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
}

