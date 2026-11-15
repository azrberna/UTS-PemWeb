<?php
declare(strict_types=1);

namespace App\Contracts;

interface RentableInterface
{
    public function calculateBasePrice(int $days): float;
    public function getMediaType(): string;
}