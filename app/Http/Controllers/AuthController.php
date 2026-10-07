<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            /** @var User $user */
            $user = Auth::user();

            if ($user->hasRole('Superadmin')) {
                return redirect()->intended('/superadmin/dashboard');
            } elseif ($user->hasRole('Admin Prodi')) {
                return redirect()->intended('/admin/dashboard');
            } else {
                return redirect()->intended('/candidate/dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'Kredensial yang Anda masukkan tidak cocok dengan catatan kami.',
        ])->onlyInput('email');
    }

    public function loginAs(User $user): RedirectResponse
    {
        Auth::login($user);
        request()->session()->regenerate();

        if ($user->hasRole('Superadmin')) {
            return redirect()->to('/superadmin/dashboard')->with('success', "Login sebagai {$user->name} ({$user->roles->first()?->name})");
        } elseif ($user->hasRole('Admin Prodi')) {
            return redirect()->to('/admin/dashboard')->with('success', "Login sebagai {$user->name} ({$user->roles->first()?->name})");
        } else {
            return redirect()->to('/candidate/dashboard')->with('success', "Login sebagai {$user->name} ({$user->roles->first()?->name})");
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('info', 'Anda telah keluar dari sistem.');
    }
}
