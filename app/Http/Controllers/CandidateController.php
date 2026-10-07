<?php

namespace App\Http\Controllers;

use App\Models\ContactPerson;
use App\Models\OathCandidate;
use App\Models\OathPeriod;
use App\Models\StudyProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CandidateController extends Controller
{
    public function dashboard(): View
    {
        $user = Auth::user();
        $candidate = $user->candidate;

        if (!$candidate) {
            // Find active period for user's prodi if available
            $activePeriod = OathPeriod::where('study_program_id', $user->study_program_id)
                ->where('status', 'active')
                ->first();

            $isTokenVerified = false;
            if ($activePeriod) {
                $sessionPeriodId = session('verified_period_id');
                $sessionExpiresAt = session('verified_period_expires_at');
                if ($sessionPeriodId == $activePeriod->id && $sessionExpiresAt && now()->timestamp < $sessionExpiresAt) {
                    $isTokenVerified = true;
                } else {
                    $cookieData = request()->cookie('verified_period_access');
                    if ($cookieData) {
                        $data = json_decode($cookieData, true);
                        if (is_array($data) && ($data['period_id'] ?? null) == $activePeriod->id && ($data['expires_at'] ?? 0) > now()->timestamp) {
                            $isTokenVerified = true;
                            session([
                                'verified_period_id' => $activePeriod->id,
                                'verified_period_expires_at' => $data['expires_at'],
                            ]);
                        }
                    }
                }
            }
            
            $studyPrograms = StudyProgram::all();
            
            return view('candidate.register', compact('activePeriod', 'studyPrograms', 'isTokenVerified'));
        }

        $candidate->load(['period.studyProgram']);

        // Contacts for candidate's prodi + IT
        $itContacts = ContactPerson::whereNull('study_program_id')
            ->where('category', 'it')
            ->where('is_active', true)
            ->get();

        $adminContacts = ContactPerson::where('study_program_id', $candidate->period->study_program_id)
            ->where('category', 'admin')
            ->where('is_active', true)
            ->get();

        return view('candidate.dashboard', compact('candidate', 'itContacts', 'adminContacts'));
    }

    public function registerStore(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'period_id' => ['required', 'exists:oath_periods,id'],
            'access_token' => ['nullable', 'string'],
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

        $period = OathPeriod::findOrFail($validated['period_id']);

        // Check if period is locked
        if ($period->is_locked) {
            return redirect()->back()->withInput()->with('error', 'Pendaftaran untuk periode ini telah DIKUNCI oleh Panitia Admin Prodi!');
        }

        // Check token verification status (session & cookie)
        $sessionPeriodId = session('verified_period_id');
        $sessionExpiresAt = session('verified_period_expires_at');
        $isTokenVerified = false;

        if ($sessionPeriodId == $period->id && $sessionExpiresAt && now()->timestamp < $sessionExpiresAt) {
            $isTokenVerified = true;
        } else {
            $cookieData = $request->cookie('verified_period_access');
            if ($cookieData) {
                $data = json_decode($cookieData, true);
                if (is_array($data) && ($data['period_id'] ?? null) == $period->id && ($data['expires_at'] ?? 0) > now()->timestamp) {
                    $isTokenVerified = true;
                    session([
                        'verified_period_id' => $period->id,
                        'verified_period_expires_at' => $data['expires_at'],
                    ]);
                }
            }
        }

        if (!$isTokenVerified) {
            if (empty($validated['access_token'])) {
                return redirect()->back()->withInput()->with('error', 'Kode Token Akses Wajib Diisi!');
            }

            if ($period->access_token && strtoupper(trim($validated['access_token'])) !== strtoupper(trim($period->access_token))) {
                return redirect()->back()->withInput()->with('error', 'Kode Token Akses Periode Salah! Silakan masukkan kode token resmi yang didapatkan dari Panitia Admin Prodi.');
            }

            // Save verified session & cookie for 60 minutes
            $expiresAt = now()->addMinutes(60)->timestamp;
            session([
                'verified_period_id' => $period->id,
                'verified_period_expires_at' => $expiresAt,
            ]);
            \Illuminate\Support\Facades\Cookie::queue('verified_period_access', json_encode(['period_id' => $period->id, 'expires_at' => $expiresAt]), 60);
        }

        $candidate = OathCandidate::create([
            'period_id' => $period->id,
            'user_id' => $user->id,
            'nim' => $validated['nim'],
            'nik' => $validated['nik'],
            'full_name' => $validated['full_name'],
            'birth_place' => $validated['birth_place'],
            'birth_date' => $validated['birth_date'],
            'father_name' => $validated['father_name'],
            'mother_name' => $validated['mother_name'],
            'admission_path' => $validated['admission_path'],
            'religion' => $validated['religion'],
            'agreed_rules' => false,
        ]);

        $user->update(['study_program_id' => $period->study_program_id]);

        return redirect()->route('candidate.dashboard')->with('success', 'Pendaftaran & verifikasi token berhasil! Silakan baca dan setujui Pakta Tata Tertib Acara.');
    }

    public function updateBiodata(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $candidate = $user->candidate;

        if (!$candidate) {
            return redirect()->route('candidate.dashboard');
        }

        $period = OathPeriod::find($candidate->period_id);

        // Lock enforcement check
        if ($period && $period->is_locked) {
            return redirect()->route('candidate.dashboard')->with('error', 'Pendaftaran dan data peserta telah DIKUNCI oleh Panitia Admin Prodi. Perubahan data tidak diizinkan.');
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

        return redirect()->route('candidate.dashboard')->with('success', 'Biodata peserta sumpah berhasil diperbarui!');
    }

    public function deleteRegistration(): RedirectResponse
    {
        $user = Auth::user();
        $candidate = $user->candidate;

        if (!$candidate) {
            return redirect()->route('candidate.dashboard');
        }

        $period = OathPeriod::find($candidate->period_id);

        // Lock enforcement check
        if ($period && $period->is_locked) {
            return redirect()->route('candidate.dashboard')->with('error', 'Pendaftaran telah DIKUNCI oleh Panitia Admin Prodi. Data pendaftaran tidak dapat dihapus.');
        }

        $candidate->delete();

        return redirect()->route('candidate.dashboard')->with('success', 'Pendaftaran sumpah berhasil dibatalkan/dihapus.');
    }

    public function agreeRules(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $candidate = $user->candidate;

        if (!$candidate) {
            return redirect()->route('candidate.dashboard');
        }

        $request->validate([
            'confirm_no_children' => ['required', 'accepted'],
            'confirm_punctuality' => ['required', 'accepted'],
            'confirm_dresscode' => ['required', 'accepted'],
        ]);

        $candidate->update([
            'agreed_rules' => true,
            'agreed_at' => now(),
        ]);

        return redirect()->route('candidate.dashboard')->with('success', 'Komitmen Pakta Integritas Digital berhasil disetujui. Akses e-ticket dan naskah lafal sumpah telah terbuka.');
    }

    public function oathScript(): View|RedirectResponse
    {
        $user = Auth::user();
        $candidate = $user->candidate;

        if (!$candidate || !$candidate->agreed_rules) {
            return redirect()->route('candidate.dashboard')->with('error', 'Anda wajib menyetujui Pakta Tata Tertib sebelum mengunduh naskah lafal sumpah.');
        }

        $candidate->load(['period.studyProgram']);

        return view('candidate.oath-script', compact('candidate'));
    }

    public function uploadPpt(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $candidate = $user->candidate;

        if (!$candidate) {
            return redirect()->route('candidate.dashboard');
        }

        // Lock enforcement check
        if ($candidate->period && $candidate->period->is_locked) {
            return redirect()->route('candidate.dashboard')->with('error', 'Pendaftaran telah DIKUNCI oleh Panitia Admin Prodi. Upload file PPT tidak diizinkan.');
        }

        $request->validate([
            'ppt_file' => ['required', 'file', 'mimes:ppt,pptx,pdf', 'max:20480'], // Max 20MB
        ]);

        if ($request->hasFile('ppt_file')) {
            $path = $request->file('ppt_file')->store('ppt_slides', 'public');
            $candidate->update(['ppt_file_path' => $path]);
        }

        return redirect()->route('candidate.dashboard')->with('success', 'Slide PPT profil berhasil diunggah!');
    }

    public function eTicket(): View|RedirectResponse
    {
        $user = Auth::user();
        $candidate = $user->candidate;

        if (!$candidate || !$candidate->agreed_rules) {
            return redirect()->route('candidate.dashboard')->with('error', 'Akses e-Ticket dikunci! Anda belum menyetujui Komitmen Pakta Tata Tertib.');
        }

        $candidate->load(['period.studyProgram']);

        return view('candidate.e-ticket', compact('candidate'));
    }
}
