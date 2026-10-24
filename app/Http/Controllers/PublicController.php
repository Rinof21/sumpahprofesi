<?php

namespace App\Http\Controllers;

use App\Models\ContactPerson;
use App\Models\DocumentationVendor;
use App\Models\OathCandidate;
use App\Models\OathPeriod;
use App\Models\StudyProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function index(): View
    {
        $studyPrograms = StudyProgram::with(['oathPeriods' => function ($query) {
            $query->orderBy('event_date', 'desc');
        }])->get();

        $activePeriods = OathPeriod::with('studyProgram')
            ->where('status', 'active')
            ->orderBy('event_date', 'asc')
            ->get();

        $archivedPeriods = OathPeriod::with(['studyProgram', 'candidates'])
            ->where('status', 'archived')
            ->orderBy('event_date', 'desc')
            ->get();

        $totalGraduates = OathCandidate::count();
        $totalPeriods = OathPeriod::count();
        $totalProdi = StudyProgram::count();

        return view('public.index', compact(
            'studyPrograms',
            'activePeriods',
            'archivedPeriods',
            'totalGraduates',
            'totalPeriods',
            'totalProdi'
        ));
    }

    public function archiveDetail(string $slug): View
    {
        $period = OathPeriod::with(['studyProgram', 'eventPhotos', 'photographers'])
            ->where('slug', $slug)
            ->firstOrFail();

        $allCandidates = $period->candidates()->get();
        $totalCandidates = $allCandidates->count();

        $religionStats = $allCandidates->groupBy('religion')->map->count();
        $admissionStats = $allCandidates->groupBy('admission_path')->map->count();

        $speechRep = $allCandidates->firstWhere('is_speech_rep', true);

        $candidates = $period->candidates()
            ->orderBy('sort_order', 'asc')
            ->orderBy('full_name', 'asc')
            ->paginate(10);

        return view('public.archive-detail', compact(
            'period',
            'candidates',
            'totalCandidates',
            'religionStats',
            'admissionStats',
            'speechRep'
        ));
    }

    public function helpdesk(): View
    {
        $itContacts = ContactPerson::whereNull('study_program_id')
            ->where('category', 'it')
            ->where('is_active', true)
            ->get();

        $prodiContacts = ContactPerson::with('studyProgram')
            ->whereNotNull('study_program_id')
            ->where('category', 'admin')
            ->where('is_active', true)
            ->get();

        $photographerVendors = DocumentationVendor::active()
            ->photographers()
            ->with('studyProgram')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $streamingVendors = DocumentationVendor::active()
            ->liveStreaming()
            ->with('studyProgram')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('public.helpdesk', compact('itContacts', 'prodiContacts', 'photographerVendors', 'streamingVendors'));
    }

    public function eventSchedule(Request $request): RedirectResponse
    {
        $sessionPeriodId = session('verified_period_id');
        $sessionExpiresAt = session('verified_period_expires_at');

        if ($sessionPeriodId && $sessionExpiresAt && now()->timestamp < $sessionExpiresAt) {
            return redirect()->route('token-access.event-schedule');
        }

        $cookieData = $request->cookie('verified_period_access');
        if ($cookieData) {
            $data = json_decode($cookieData, true);
            if (is_array($data) && isset($data['period_id'], $data['expires_at']) && now()->timestamp < $data['expires_at']) {
                return redirect()->route('token-access.event-schedule');
            }
        }

        return redirect()->route('token-access.index')->with('info', 'Susunan acara dan tata tertib berbeda untuk setiap program studi. Silakan masukkan kode token periode prodi Anda terlebih dahulu.');
    }

    public function oathScript(Request $request): RedirectResponse
    {
        $sessionPeriodId = session('verified_period_id');
        $sessionExpiresAt = session('verified_period_expires_at');

        if ($sessionPeriodId && $sessionExpiresAt && now()->timestamp < $sessionExpiresAt) {
            return redirect()->route('token-access.oath-script');
        }

        $cookieData = $request->cookie('verified_period_access');
        if ($cookieData) {
            $data = json_decode($cookieData, true);
            if (is_array($data) && isset($data['period_id'], $data['expires_at']) && now()->timestamp < $data['expires_at']) {
                return redirect()->route('token-access.oath-script');
            }
        }

        return redirect()->route('token-access.index')->with('info', 'Naskah lafal sumpah berbeda untuk setiap program studi. Silakan masukkan kode token periode prodi Anda terlebih dahulu.');
    }
}
