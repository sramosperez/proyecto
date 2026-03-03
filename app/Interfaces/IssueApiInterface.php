<?php

namespace App\Interfaces;

use App\DTOs\IssueDTO;

interface IssueApiInterface
{
    public function findIssue(int $id): ?IssueDTO;

    public function updateIssue(int $id, array $data, int $userId): bool;
}
