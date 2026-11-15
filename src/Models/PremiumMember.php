<?php
declare(strict_types=1);

namespace App\Models;

class PremiumMember extends User
{
    public function getMembershipTier(): string { return 'Premium'; }
    public function getMaxBorrowLimit(): int { return 5; }
    public function applyDiscount(float $amount): float { return $amount * 0.8; } // Diskon 20%
}