<?php

namespace App\Http\Controllers;

use App\Services\Auth\SsoAuthService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected SsoAuthService $ssoAuth
    ) {}

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

        try {
            $user = $this->ssoAuth->login(
                (int) $request->input('employee_id'),
                $request->input('password')
            );
            if (! $user) {
                return back()->withErrors(['login' => 'Usuario o contraseña no válidos.'])->withInput();
            }

            return redirect()->intended(route('issues.index'));

        } catch (AuthorizationException $e) {
            return back()->withErrors(['login' => $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['login' => 'Error del sistema.'])->withInput();
        }
    }

    public function logout(Request $request)
    {
        $this->ssoAuth->logout($request);

        return redirect()->route('login');
    }
}
