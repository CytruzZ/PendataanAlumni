<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * Display login view
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login authentication
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            $roleText = $user->isAdmin() ? 'Admin' : 'Mahasiswa';

            return redirect()->intended(route('alumni.index'))
                ->with('success', "Selamat datang kembali, {$user->name} ({$roleText})!");
        }

        return back()->withErrors([
            'email' => 'Username/Email atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    /**
     * Display student registration view
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle student registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'unique:users'],
            'nim_nip' => ['required', 'string', 'max:50'],
            'no_wa' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nim_nip' => $request->nim_nip,
            'no_wa' => $request->no_wa,
            'role' => 'mahasiswa', // default student registration
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('alumni.index')
            ->with('success', 'Akun Mahasiswa Anda berhasil dibuat! Selamat datang di Portal Alumni MNI IPB.');
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
