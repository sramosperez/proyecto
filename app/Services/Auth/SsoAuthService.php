<?php

namespace App\Services\Auth;

use App\Contracts\SsoProviderInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SsoAuthService
{
    public function __construct(
        private SsoProviderInterface $provider
    ) {}

    public function login(int $employeeId, string $password): ?User
    {
        $user = $this->provider->validate($employeeId, $password);

        if (!$user) {
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
    }
}
