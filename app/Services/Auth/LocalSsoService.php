<?php

namespace App\Services\Auth;

use App\Contracts\SsoServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LocalSsoService implements SsoServiceInterface
{
    public function validate(int $employeeId, string $password): ?User
    {
        $user = User::find($employeeId);

        if (! $user || ! Hash::check($password, $user->password)) {
            Log::info("Login failed for employee ID: $employeeId");

            return null;
        }

        return $user;
    }
}
