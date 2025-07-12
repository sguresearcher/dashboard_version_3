<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginCT extends Controller
{
    public function index()
    {
        return view('pages.login');
    }

    public function auth(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $role = Auth::user()->role;

            //dd($role);

            if ($role === 'superadmin') {
                return redirect('/superadmin');
            } elseif ($role === 'tenant') {
                return redirect('/home');
            } else {
                Auth::logout();
                return redirect('/login')->withErrors([
                    'role' => 'Role tidak dikenali atau tidak diizinkan.',
                ]);
            }
        }

        // Jika gagal login
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
