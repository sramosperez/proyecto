<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'password' => 'required|string',
        ]);

        $credentials = [
            'employee_id' => (int) $request->input('employee_id'),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials)) {

            $user = Auth::user();
            if ($user->role->name !== 'User Authorized') {
                Auth::logout();

                return back()->withErrors(['login' => 'No tienes acceso autorizado.'])->withInput();
            }

            $request->session()->regenerate();

            return redirect()->intended(route('issues.index'));
        }

        return back()->withErrors(['login' => 'Usuario o contraseña no válidos.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
