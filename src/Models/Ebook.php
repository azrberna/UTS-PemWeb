<?php
declare(strict_types=1);

namespace App\Models;

class Ebook extends MediaItem
{
    private int $pageCount;

    public function __construct(
        string $id,
        string $title,
        string $author,
        string $category,
        float $dailyRate,
        int $stock,
        int $pageCount = 150
    ) {
        parent::__construct($id, $title, $author, $category, $dailyRate, $stock);
        $this->pageCount = $pageCount;
    }

    public function getPageCount(): int { return $this->pageCount; }

    public function calculateBasePrice(int $days): float
    {
        return $this->dailyRate * $days;
    }

    public function getMediaType(): string
    {
        return 'E-Book';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'type' => 'ebook',
            'extraInfo' => $this->pageCount . ' Halaman',
            'pageCount' => $this->pageCount,
        ]);
    }
}