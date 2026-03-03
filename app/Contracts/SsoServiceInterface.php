<?php

namespace App\Interfaces;

use App\Models\User;

interface SsoServiceInterface
{
    public function validate(int $employeeId, string $password): ?User;
}
