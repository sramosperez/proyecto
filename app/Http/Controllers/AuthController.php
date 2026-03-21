<?php

namespace App\Http\Controllers;

use App\Services\Auth\SsoAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        private SsoAuthService $ssoAuth
    ) {}

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'password'    => 'required|string',
        ]);

        $user = $this->ssoAuth->login(
            (int) $request->input('employee_id'),
            $request->input('password')
        );

        if (!$user) {
            return back()
                ->withErrors(['login' => 'Credenciales incorrectas.'])
                ->onlyInput('employee_id');
        }

        return redirect()->intended(route('issues.index'));
    }

    public function logout(Request $request)
    {
        $this->ssoAuth->logout($request);

        return redirect()->route('login');
    }
}
