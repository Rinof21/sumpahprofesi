<?php

namespace App\Http\Controllers;

use App\Models\ContactPerson;
use App\Models\DocumentationVendor;
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
        $contact->update(['is_active' => ! $contact->is_active]);

        return redirect()->back()->with('success', 'Status kontak IT berhasil diperbarui.');
    }

    // --- DOCUMENTATION VENDOR & LIVE STREAMING (SUPERADMIN) ---
    public function vendors(Request $request): View
    {
        $category = $request->get('category');
        $prodiFilter = $request->get('study_program_id');

        $query = DocumentationVendor::with('studyProgram');

        if ($category && in_array($category, ['photographer', 'live_streaming'])) {
            $query->where('category', $category);
        }

        if ($prodiFilter !== null && $prodiFilter !== '') {
            if ($prodiFilter === 'global') {
                $query->whereNull('study_program_id');
            } else {
                $query->where('study_program_id', $prodiFilter);
            }
        }

        $vendors = $query->orderBy('sort_order')->orderBy('name')->get();

        $photographerVendorsCount = DocumentationVendor::where('category', 'photographer')->count();
        $streamingVendorsCount = DocumentationVendor::where('category', 'live_streaming')->count();
        $studyPrograms = StudyProgram::all();

        return view('admin.vendors.index', compact(
            'vendors',
            'category',
            'prodiFilter',
            'studyPrograms',
            'photographerVendorsCount',
            'streamingVendorsCount'
        ));
    }

    public function storeVendor(Request $request): RedirectResponse
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
            'study_program_id' => ['nullable', 'exists:study_programs,id'],
        ]);

        DocumentationVendor::create([
            'study_program_id' => $validated['study_program_id'] ?? null,
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

        return redirect()->back()->with('success', "{$label} berhasil disimpan oleh Superadmin sebagai rekomendasi!");
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
            'study_program_id' => ['nullable', 'exists:study_programs,id'],
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
            'study_program_id' => $validated['study_program_id'] ?? null,
            'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : $vendor->is_active,
        ]);

        return redirect()->back()->with('success', 'Data vendor berhasil diperbarui!');
    }

    public function destroyVendor(DocumentationVendor $vendor): RedirectResponse
    {
        $vendor->delete();

        return redirect()->back()->with('success', 'Kontak vendor berhasil dihapus.');
    }

    public function toggleVendor(DocumentationVendor $vendor): RedirectResponse
    {
        $vendor->update(['is_active' => ! $vendor->is_active]);

        $status = $vendor->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()->with('success', "Status vendor '{$vendor->name}' berhasil {$status}.");
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
