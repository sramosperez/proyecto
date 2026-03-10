<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class LocalSsoProvider implements SsoServiceInterface
{
    public function validate(int $employeeId, string $password): ?User
    {
        $user = User::find($employeeId);

        if (! $user || ! password_verify($password, $user->password)) {
            Log::info("Login failed for employee ID: $employeeId");

            return null;
        }

        return $user;
    }
}
