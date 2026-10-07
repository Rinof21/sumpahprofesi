<?php

namespace App\Http\Controllers;

use App\Models\ContactPerson;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class SuperadminController extends Controller
{
    public function dashboard(): View
    {
        $totalProdi = StudyProgram::count();
        $totalUsers = User::count();
        $totalITContacts = ContactPerson::whereNull('study_program_id')->count();
        
        $prodis = StudyProgram::withCount('oathPeriods')->get();

        return view('superadmin.dashboard', compact('totalProdi', 'totalUsers', 'totalITContacts', 'prodis'));
    }

    // --- MASTER STUDY PROGRAMS (FR-A1) ---
    public function studyPrograms(): View
    {
        $studyPrograms = StudyProgram::withCount(['oathPeriods', 'users'])->get();
        return view('superadmin.study-programs.index', compact('studyPrograms'));
    }

    public function storeStudyProgram(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', 'unique:study_programs,code'],
            'name' => ['required', 'string', 'max:255'],
            'degree_title' => ['required', 'string', 'max:50'],
            'organization' => ['required', 'string', 'max:255'],
        ]);

        StudyProgram::create([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'degree_title' => $validated['degree_title'],
            'organization' => $validated['organization'],
            'slug' => Str::slug($validated['name']),
        ]);

        return redirect()->back()->with('success', 'Program Studi baru berhasil ditambahkan!');
    }

    // --- IT HELPDESK CONTACTS (FR-F1) ---
    public function itContacts(): View
    {
        $contacts = ContactPerson::whereNull('study_program_id')
            ->where('category', 'it')
            ->get();

        return view('superadmin.it-contacts.index', compact('contacts'));
    }

    public function storeITContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        ContactPerson::create([
            'study_program_id' => null,
            'category' => 'it',
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Kontak Person IT Fakultas berhasil ditambahkan.');
    }

    public function toggleITContact(ContactPerson $contact): RedirectResponse
    {
        $contact->update(['is_active' => !$contact->is_active]);
        return redirect()->back()->with('success', 'Status kontak IT berhasil diperbarui.');
    }

    // --- USER MANAGEMENT & SPATIE PERMISSIONS ---
    public function users(): View
    {
        $users = User::with(['roles', 'studyProgram'])->paginate(15);
        $roles = Role::all();
        $studyPrograms = StudyProgram::all();

        return view('superadmin.users.index', compact('users', 'roles', 'studyPrograms'));
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'exists:roles,name'],
            'study_program_id' => ['nullable', 'exists:study_programs,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'study_program_id' => $validated['study_program_id'],
        ]);

        $user->assignRole($validated['role']);

        return redirect()->back()->with('success', "Pengguna {$user->name} berhasil dibuat dengan peran {$validated['role']}.");
    }

    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'exists:roles,name'],
            'study_program_id' => ['nullable', 'exists:study_programs,id'],
        ]);

        $user->syncRoles([$validated['role']]);
        $user->update(['study_program_id' => $validated['study_program_id']]);

        return redirect()->back()->with('success', 'Peran & Akses pengguna berhasil diperbarui.');
    }
}
