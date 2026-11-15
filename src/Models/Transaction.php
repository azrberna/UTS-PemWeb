<?php
declare(strict_types=1);

namespace App\Models;

class Transaction
{
    public function __construct(
        private string $id,
        private string $userId,
        private string $userName,
        private string $mediaId,
        private string $mediaTitle,
        private int $days,
        private float $totalPrice,
        private string $status = 'active',
        private string $borrowedAt = ''
    ) {
        if ($this->borrowedAt === '') {
            $this->borrowedAt = date('Y-m-d H:i');
        }
    }

    public function getId(): string { return $this->id; }
    public function getUserId(): string { return $this->userId; }
    public function getMediaId(): string { return $this->mediaId; }
    public function getStatus(): string { return $this->status; }

    public function markReturned(): void
    {
        $this->status = 'returned';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'userName' => $this->userName,
            'mediaId' => $this->mediaId,
            'mediaTitle' => $this->mediaTitle,
            'days' => $this->days,
            'totalPrice' => $this->totalPrice,
            'status' => $this->status,
            'borrowedAt' => $this->borrowedAt,
        ];
    }
}