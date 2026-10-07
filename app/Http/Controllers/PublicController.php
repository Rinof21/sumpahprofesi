<?php

namespace App\Http\Controllers;

use App\Models\ContactPerson;
use App\Models\OathCandidate;
use App\Models\OathPeriod;
use App\Models\StudyProgram;
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
        $period = OathPeriod::with(['studyProgram', 'candidates' => function ($q) {
            $q->orderBy('full_name', 'asc');
        }, 'eventPhotos', 'photographers'])
            ->where('slug', $slug)
            ->firstOrFail();

        $candidates = $period->candidates;

        // Statistics
        $totalCandidates = $candidates->count();
        
        $religionStats = $candidates->groupBy('religion')->map->count();
        $admissionStats = $candidates->groupBy('admission_path')->map->count();

        $speechRep = $candidates->firstWhere('is_speech_rep', true);

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

        return view('public.helpdesk', compact('itContacts', 'prodiContacts'));
    }
}
