<?php

use App\Http\Controllers\AdminProdiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SuperadminController;
use App\Http\Controllers\TokenAccessController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes & Candidate Token Access (Tanpa Login)
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'index'])->name('public.index');
Route::get('/archive/{slug}', [PublicController::class, 'archiveDetail'])->name('public.archive-detail');
Route::get('/helpdesk', [PublicController::class, 'helpdesk'])->name('public.helpdesk');

// Akses Mahasiswa Tanpa Login via Kode Token Akses Admin
Route::prefix('pendaftaran-token')->as('token-access.')->group(function () {
    Route::get('/', [TokenAccessController::class, 'showTokenForm'])->name('index');
    Route::post('/verify', [TokenAccessController::class, 'verifyToken'])->name('verify');
    Route::post('/reset', [TokenAccessController::class, 'resetTokenSession'])->name('reset');
    Route::get('/portal', [TokenAccessController::class, 'portal'])->name('portal');
    Route::post('/peserta', [TokenAccessController::class, 'storeCandidate'])->name('store');
    Route::put('/peserta/{candidate}', [TokenAccessController::class, 'updateCandidate'])->name('update');
    Route::delete('/peserta/{candidate}', [TokenAccessController::class, 'deleteCandidate'])->name('destroy');
    Route::post('/peserta/{candidate}/ppt', [TokenAccessController::class, 'uploadPpt'])->name('upload-ppt');
});

/*
|--------------------------------------------------------------------------
| Authentication & Demo Switcher
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/demo-login/{user}', [AuthController::class, 'loginAs'])->name('demo.login-as');

/*
|--------------------------------------------------------------------------
| Peserta / Candidate Routes (Role: Peserta)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Peserta'])->prefix('candidate')->as('candidate.')->group(function () {
    Route::get('/dashboard', [CandidateController::class, 'dashboard'])->name('dashboard');
    Route::post('/register', [CandidateController::class, 'registerStore'])->name('register.store');
    Route::put('/biodata', [CandidateController::class, 'updateBiodata'])->name('biodata.update');
    Route::delete('/registration', [CandidateController::class, 'deleteRegistration'])->name('registration.delete');
    Route::post('/agree-rules', [CandidateController::class, 'agreeRules'])->name('agree-rules');
    Route::get('/oath-script', [CandidateController::class, 'oathScript'])->name('oath-script');
    Route::post('/upload-ppt', [CandidateController::class, 'uploadPpt'])->name('upload-ppt');
    Route::get('/e-ticket', [CandidateController::class, 'eTicket'])->name('e-ticket');
});

/*
|--------------------------------------------------------------------------
| Admin Prodi Routes (Role: Admin Prodi)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Admin Prodi'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', [AdminProdiController::class, 'dashboard'])->name('dashboard');
    Route::post('/dashboard/oath-pdf', [AdminProdiController::class, 'updateOathPdf'])->name('dashboard.update-oath-pdf');
    
    // Period Management
    Route::get('/periods', [AdminProdiController::class, 'periods'])->name('periods.index');
    Route::post('/periods', [AdminProdiController::class, 'storePeriod'])->name('periods.store');
    Route::put('/periods/{period}', [AdminProdiController::class, 'updatePeriod'])->name('periods.update');
    Route::patch('/periods/{period}/status', [AdminProdiController::class, 'updatePeriodStatus'])->name('periods.update-status');
    Route::patch('/periods/{period}/toggle-lock', [AdminProdiController::class, 'toggleLock'])->name('periods.toggle-lock');
    Route::delete('/periods/{period}', [AdminProdiController::class, 'destroyPeriod'])->name('periods.destroy');
    Route::post('/periods/{id}/restore', [AdminProdiController::class, 'restorePeriod'])->name('periods.restore');
    Route::delete('/periods/{id}/force-delete', [AdminProdiController::class, 'forceDeletePeriod'])->name('periods.force-delete');

    
    // Candidate & Cleric Recap
    Route::get('/candidates/export', [AdminProdiController::class, 'exportCandidates'])->name('candidates.export');
    Route::get('/candidates', [AdminProdiController::class, 'candidates'])->name('candidates.index');
    Route::post('/candidates/{candidate}/speech-rep', [AdminProdiController::class, 'assignSpeechRep'])->name('candidates.speech-rep');
    Route::post('/candidates/{candidate}/oath-coordinator', [AdminProdiController::class, 'assignOathCoordinator'])->name('candidates.oath-coordinator');
    
    // Photographer Management (Max 3 Lock)
    Route::get('/photographers', [AdminProdiController::class, 'photographers'])->name('photographers.index');
    Route::post('/photographers', [AdminProdiController::class, 'storePhotographer'])->name('photographers.store');
    Route::delete('/photographers/{photographer}', [AdminProdiController::class, 'destroyPhotographer'])->name('photographers.destroy');
    Route::get('/photographers/{photographer}/badge', [AdminProdiController::class, 'printBadge'])->name('photographers.badge');
    
    // Contact Management
    Route::get('/contacts', [AdminProdiController::class, 'contacts'])->name('contacts.index');
    Route::post('/contacts', [AdminProdiController::class, 'storeContact'])->name('contacts.store');
    Route::patch('/contacts/{contact}/toggle', [AdminProdiController::class, 'toggleContact'])->name('contacts.toggle');
    
    // Gallery & Archive Curation
    Route::get('/gallery', [AdminProdiController::class, 'gallery'])->name('gallery.index');
    Route::post('/gallery/photo', [AdminProdiController::class, 'storePhoto'])->name('gallery.store-photo');
    Route::patch('/gallery/youtube/{period}', [AdminProdiController::class, 'updateYoutube'])->name('gallery.update-youtube');
    Route::delete('/gallery/photo/{photo}', [AdminProdiController::class, 'destroyPhoto'])->name('gallery.destroy-photo');
});

/*
|--------------------------------------------------------------------------
| Superadmin Routes (Role: Superadmin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Superadmin'])->prefix('superadmin')->as('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperadminController::class, 'dashboard'])->name('dashboard');
    
    // Master Study Programs
    Route::get('/study-programs', [SuperadminController::class, 'studyPrograms'])->name('study-programs.index');
    Route::post('/study-programs', [SuperadminController::class, 'storeStudyProgram'])->name('study-programs.store');
    
    // IT Helpdesk Contacts
    Route::get('/it-contacts', [SuperadminController::class, 'itContacts'])->name('it-contacts.index');
    Route::post('/it-contacts', [SuperadminController::class, 'storeITContact'])->name('it-contacts.store');
    Route::patch('/it-contacts/{contact}/toggle', [SuperadminController::class, 'toggleITContact'])->name('it-contacts.toggle');
    
    // User & Spatie Role Management
    Route::get('/users', [SuperadminController::class, 'users'])->name('users.index');
    Route::post('/users', [SuperadminController::class, 'storeUser'])->name('users.store');
    Route::patch('/users/{user}/role', [SuperadminController::class, 'updateUserRole'])->name('users.update-role');

    // Spatie Roles & Permissions CRUD Management
    Route::get('/roles-permissions', [RolePermissionController::class, 'index'])->name('roles-permissions.index');
    Route::post('/roles', [RolePermissionController::class, 'storeRole'])->name('roles.store');
    Route::put('/roles/{role}', [RolePermissionController::class, 'updateRole'])->name('roles.update');
    Route::delete('/roles/{role}', [RolePermissionController::class, 'destroyRole'])->name('roles.destroy');

    Route::post('/permissions', [RolePermissionController::class, 'storePermission'])->name('permissions.store');
    Route::put('/permissions/{permission}', [RolePermissionController::class, 'updatePermission'])->name('permissions.update');
    Route::delete('/permissions/{permission}', [RolePermissionController::class, 'destroyPermission'])->name('permissions.destroy');

    Route::patch('/users/{user}/permissions', [RolePermissionController::class, 'updateUserPermissions'])->name('users.update-permissions');
});
