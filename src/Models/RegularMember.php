<?php
declare(strict_types=1);

namespace App\Models;

class RegularMember extends User
{
    public function getMembershipTier(): string { return 'Regular'; }
    public function getMaxBorrowLimit(): int { return 2; }
    public function applyDiscount(float $amount): float { return $amount; }
}