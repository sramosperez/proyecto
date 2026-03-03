<?php

namespace App\DTOs;

class IssueDTO
{
    public function __construct(
        public int $id,
        public string $reference,
        public string $description,
        public string $resolution,
        public string $status,
        public ?string $storeCode = null,
        public ?string $updatedBy = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            reference: $data['reference'] ?? 'N/A',
            description: $data['description'],
            resolution: $data['resolution'],
            status: $data['status'],
            storeCode: $data['storeCode'] ?? null,
            updatedBy: $data['updatedBy'] ?? null,
        );
    }

    public function isOpen(): bool
    {
        return $this->status !== 'Closed' && $this->status !== 'Resolved';
    }
}
