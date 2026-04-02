<?php

namespace App\Services\Auth;

use App\Contracts\SsoServiceInterface;
use App\Models\User;
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
