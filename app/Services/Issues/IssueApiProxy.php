<?php

namespace App\Services\Issues;

use App\Contracts\IssueApiInterface;
use App\DTOs\IssueDTO;
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

    private function findFirebaseIssueById(int $id): ?array
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
            Log::warning('No se pudo consultar Firebase.', [
                'issue_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function find(int $id): ?IssueDTO
    {
        try {
            $result = $this->findFirebaseIssueById($id);

            if (! $result) {
                return null;
            }

            return IssueDTO::fromArray($result['data']);
        } catch (Throwable $e) {
            Log::error('Error consultando incidencia.', [
                'issue_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function listByStore(?string $storeCode = null): array
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

                // En modo listado por tienda, solo incluir incidencias asignadas a esa tienda.
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

            return [];
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
            $isCurrentStatusBoolean = is_bool($current['status'] ?? null);

            $statusToPersist = $requestedStatus;
            if ($isCurrentStatusBoolean) {
                // En modo booleano legacy: true = Open, false = Closed.
                $statusToPersist = $requestedStatus === 'Closed' ? false : true;
            }

            $payload = [
                'status' => $statusToPersist,
                'storeCode' => $data['storeCode'] ?? $currentStoreCode,
                'updatedBy' => $userId,
            ];

            if (array_key_exists('comment', $data) && $data['comment'] !== null && $data['comment'] !== '') {
                $payload['comment'] = $data['comment'];
            }

            $this->database
                ->getReference($result['path'] === '/' ? '/'.$result['key'] : $result['path'].'/'.$result['key'])
                ->update($payload);

            return true;
        } catch (Throwable $e) {
            Log::error('Error actualizando incidencia.', [
                'issue_id' => $id,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
