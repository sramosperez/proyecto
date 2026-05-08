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
        public ?string $name,
        public ?string $surname,
        public ?string $email,
        public string $status,
        public ?string $storeCode,
        public ?int $updatedBy,
    ) {}

    public static function fromArray(array $data): self
    {

        $status = 'Open';

        if (isset($data['status'])) {
            if ($data['status'] === false || strtolower($data['status']) === 'closed') {
                $status = 'Closed';
            }
        }

        $storeCode = $data['storeCode'] ?? null;

        if ($storeCode === '') {
            $storeCode = null;
        }

        $updatedBy = isset($data['updatedBy']) ? (int) $data['updatedBy'] : null;

        return new self(
            id: $data['id'],
            reference: $data['reference'] ?? 'N/A',
            orderNumber: $data['orderNumber'] ?? null,
            createdAt: $data['createdAt'] ?? null,
            description: $data['description'],
            resolution: $data['resolution'],
            comment: $data['comment'] ?? null,
            name: $data['name'] ?? null,
            surname: $data['surname'] ?? null,
            email: $data['email'] ?? null,
            status: $status,
            storeCode: $storeCode,
            updatedBy: $updatedBy,
        );
    }
}
