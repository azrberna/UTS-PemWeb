<?php
declare(strict_types=1);

namespace App\Models;

use App\Contracts\RentableInterface;

abstract class MediaItem implements RentableInterface
{
    protected string $id;
    protected string $title;
    protected string $author;
    protected string $category;
    protected float $dailyRate;
    protected int $stock;

    public function __construct(
        string $id,
        string $title,
        string $author,
        string $category,
        float $dailyRate,
        int $stock
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->category = $category;
        $this->dailyRate = $dailyRate;
        $this->stock = max(0, $stock);
    }

    public function getId(): string { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getAuthor(): string { return $this->author; }
    public function getCategory(): string { return $this->category; }
    public function getDailyRate(): float { return $this->dailyRate; }
    public function getStock(): int { return $this->stock; }

    public function decrementStock(): void
    {
        if ($this->stock <= 0) {
            throw new \RuntimeException("Stok lisensi '{$this->title}' sedang habis.");
        }
        $this->stock--;
    }

    public function incrementStock(): void
    {
        $this->stock++;
    }

    public function restock(int $amount): void
    {
        if ($amount < 1) {
            throw new \InvalidArgumentException('Jumlah penambahan stok minimal 1 lisensi.');
        }
        $this->stock += $amount;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'category' => $this->category,
            'dailyRate' => $this->dailyRate,
            'stock' => $this->stock,
            'mediaType' => $this->getMediaType(),
        ];
    }
}