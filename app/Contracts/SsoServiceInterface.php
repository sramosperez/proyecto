<?php

namespace App\Contracts;

use App\Models\User;

interface SsoServiceInterface
{
    public function validate(int $employeeId, string $password): ?User;
}
