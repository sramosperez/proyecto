<?php

namespace App\DTOs;

class IssueDTO
{
    public function __construct(
        public int $id,
        public string $reference,
        public ?string $orderNumber,
        public ?string $createdAt,
        public string $description,
        public string $resolution,
        public ?string $comment,
        public ?string $customerName,
        public ?string $customerEmail,
        public string $status,
        public ?string $storeCode = null,
        public ?int $updatedBy = null,
    ) {}

    public static function fromArray(array $data): self
    {
        $rawStatus = $data['status'] ?? 'Open';
        $rawStoreCode = $data['storeCode'] ?? null;
        $rawUpdatedBy = $data['updatedBy'] ?? null;

        // Compatibilidad con datos legacy: status booleano en Firebase.
        $normalizedStatus = match (true) {
            is_bool($rawStatus) => $rawStatus ? 'Open' : 'Closed',
            is_string($rawStatus) && strtolower($rawStatus) === 'closed' => 'Closed',
            default => 'Open',
        };

        $normalizedStoreCode = is_string($rawStoreCode) && trim($rawStoreCode) === ''
            ? null
            : $rawStoreCode;

        $normalizedUpdatedBy = is_numeric($rawUpdatedBy)
            ? (int) $rawUpdatedBy
            : null;

        return new self(
            id: $data['id'],
            reference: $data['reference'] ?? 'N/A',
            orderNumber: $data['orderNumber'] ?? null,
            createdAt: $data['createdAt'] ?? null,
            description: $data['description'],
            resolution: $data['resolution'],
            comment: $data['comment'] ?? null,
            customerName: $data['customerName'] ?? null,
            customerEmail: $data['customerEmail'] ?? null,
            status: $normalizedStatus,
            storeCode: $normalizedStoreCode,
            updatedBy: $normalizedUpdatedBy,
        );
    }

    public function isOpen(): bool
    {
        return $this->status !== 'Closed' && $this->status !== 'Resolved';
    }
}
