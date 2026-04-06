<?php

namespace App\Services\Auth;

use App\Contracts\SsoServiceInterface;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SsoAuthService
{
    public function __construct(
        private SsoServiceInterface $provider
    ) {}

    public function login(int $employeeId, string $password): ?User
    {
        $user = $this->provider->validate($employeeId, $password);

        if (! $user) {
            Log::info("Fallo de autenticación para empleado ID: $employeeId");

            return null;
        }

        $user->loadMissing('role');

        if ($user->role->name !== 'User Authorized') {
            Log::warning("Acceso denegado para empleado ID: $employeeId");

            throw new AuthorizationException('No tienes acceso autorizado.');
        }

        Auth::login($user);
        session()->regenerate();

        return $user;
    }

    public function logout(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Log::info('Sesión cerrada.');
    }
}
