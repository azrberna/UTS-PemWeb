<?php
declare(strict_types=1);

namespace App\Models;

class AudioBook extends MediaItem
{
    private int $durationMinutes;

    public function __construct(
        string $id,
        string $title,
        string $author,
        string $category,
        float $dailyRate,
        int $stock,
        int $durationMinutes = 120
    ) {
        parent::__construct($id, $title, $author, $category, $dailyRate, $stock);
        $this->durationMinutes = $durationMinutes;
    }

    public function getDurationMinutes(): int { return $this->durationMinutes; }

    // Polymorphism: AudioBook memiliki biaya streaming tambahan Rp 2.000 per transaksi
    public function calculateBasePrice(int $days): float
    {
        return ($this->dailyRate * $days) + 2000.0;
    }

    public function getMediaType(): string
    {
        return 'AudioBook';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'type' => 'audiobook',
            'extraInfo' => $this->durationMinutes . ' Menit',
            'durationMinutes' => $this->durationMinutes,
        ]);
    }
}