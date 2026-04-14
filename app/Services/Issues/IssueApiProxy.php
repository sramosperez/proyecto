<?php

namespace App\Services\Issues;

use App\Contracts\IssueApiInterface;
use App\DTOs\IssueDTO;

class IssueApiProxy implements IssueApiInterface
{
    private function getRawData(): array
    {
        $path = storage_path('app/issues.json');

        if (! file_exists($path)) {
            return [];
        }

        $content = file_get_contents($path);

        return json_decode($content, true) ?? [];
    }

    public function find(int $id): ?IssueDTO
    {
        $issues = $this->getRawData();

        foreach ($issues as $issue) {
            if ($issue['id'] == $id) {
                return IssueDTO::fromArray($issue);
            }
        }

        return null;
    }

    public function updateIssue(int $id, array $data, int $userId): bool
    {
        $allIssues = $this->getRawData();
        $updated = false;

        foreach ($allIssues as $key => $item) {
            if ($item['id'] == $id) {
                $allIssues[$key]['status'] = $data['status'] ?? $item['status'];
                $allIssues[$key]['storeCode'] = $data['storeCode'] ?? $item['storeCode'] ?? null;
                $allIssues[$key]['comment'] = $data['comment'] ?? null;
                $allIssues[$key]['updatedBy'] = $userId;

                $updated = true;
                break;
            }
        }

        if ($updated) {
            file_put_contents(storage_path('app/issues.json'), json_encode($allIssues, JSON_PRETTY_PRINT));
        }

        return $updated;
    }
}
