<?php
declare(strict_types=1);

namespace App\Models;

abstract class User
{
    protected string $id;
    protected string $name;
    protected string $email;

    public function __construct(string $id, string $name, string $email)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }

    public function getId(): string { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }

    abstract public function getMembershipTier(): string;
    abstract public function getMaxBorrowLimit(): int;
    abstract public function applyDiscount(float $amount): float;

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'tier' => $this->getMembershipTier(),
            'maxLimit' => $this->getMaxBorrowLimit(),
        ];
    }
}