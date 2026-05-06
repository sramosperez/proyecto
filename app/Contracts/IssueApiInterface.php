<?php

namespace App\Contracts;

use App\DTOs\IssueDTO;

interface IssueApiInterface
{
    public function find(int $id): ?IssueDTO;

    public function getAllIssues(?string $storeCode = null): array;

    public function updateIssue(int $id, array $data, int $userId): bool;
}
