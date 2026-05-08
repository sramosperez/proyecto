<?php

namespace App\Services\Issues;

use App\Contracts\IssueApiInterface;
use App\DTOs\IssueDTO;
use App\Exceptions\ServiceUnavailableException;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Database;
use Throwable;

class IssueApiProxy implements IssueApiInterface
{
    private Database $database;

    private array $basePaths = ['issues'];

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    private function findFirebaseIssueById(int $id)
    {
        try {
            foreach ($this->basePaths as $basePath) {
                $issues = $this->database
                    ->getReference($basePath)
                    ->getValue();

                if (! is_array($issues) || empty($issues)) {
                    continue;
                }

                foreach ($issues as $firebaseKey => $issueData) {
                    if (! is_array($issueData)) {
                        continue;
                    }

                    $issueId = isset($issueData['id']) ? (int) $issueData['id'] : (int) $firebaseKey;

                    if ($issueId !== $id) {
                        continue;
                    }

                    return [
                        'path' => $basePath,
                        'key' => (string) $firebaseKey,
                        'data' => $issueData,
                    ];
                }
            }

            return null;
        } catch (Throwable $e) {
            Log::warning("Error en Firebase para issue {$id}: ".$e->getMessage());
            throw new ServiceUnavailableException('Sistema de incidencias no disponible.', 0, $e);
        }
    }

    public function find(int $id): ?IssueDTO
    {
        $result = $this->findFirebaseIssueById($id);

        if (! $result) {
            return null;
        }

        return IssueDTO::fromArray($result['data']);
    }

    public function getAllIssues($storeCode = null): array
    {
        try {
            $issues = $this->database
                ->getReference('issues')
                ->getValue();

            if (! is_array($issues) || empty($issues)) {
                return [];
            }

            $items = [];

            foreach ($issues as $firebaseKey => $issueData) {
                if (! is_array($issueData)) {
                    continue;
                }

                if (! isset($issueData['id'])) {
                    $issueData['id'] = (int) $firebaseKey;
                }

                $dto = IssueDTO::fromArray($issueData);

                if ($storeCode && $dto->storeCode !== $storeCode) {
                    continue;
                }

                $items[] = $dto;
            }

            usort($items, fn (IssueDTO $a, IssueDTO $b) => $a->id <=> $b->id);

            return $items;
        } catch (Throwable $e) {
            Log::error('Error listando incidencias.', [
                'store_code' => $storeCode,
                'error' => $e->getMessage(),
            ]);
            throw new ServiceUnavailableException('Sistema de incidencias no disponible.', 0, $e);
        }
    }

    public function updateIssue(int $id, array $data, int $userId): bool
    {
        try {
            $result = $this->findFirebaseIssueById($id);

            if (! $result) {
                return false;
            }

            $current = $result['data'];
            $currentStoreCode = $current['storeCode'] ?? null;
            $requestedStatus = $data['status'] ?? ($current['status'] ?? 'Open');
            $statusToPersist = is_bool($current['status'] ?? null)
                ? ($requestedStatus === 'Closed' ? false : true)
                : $requestedStatus;

            $payload = [
                'status' => $statusToPersist,
                'storeCode' => $data['storeCode'] ?? $currentStoreCode,
                'updatedBy' => $userId,
            ];

            if (array_key_exists('comment', $data)) {
                $payload['comment'] = $data['comment'] ?? '';
            }

            $path = $result['path'] === '/' ? '/'.$result['key'] : $result['path'].'/'.$result['key'];
            $this->database->getReference($path)->update($payload);

            return true;
        } catch (Throwable $e) {
            Log::error('Error actualizando incidencia.', [
                'issue_id' => $id,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            throw new ServiceUnavailableException('Sistema de incidencias no disponible.', 0, $e);
        }
    }
}
