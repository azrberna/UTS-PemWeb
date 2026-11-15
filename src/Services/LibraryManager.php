<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\AudioBook;
use App\Models\Ebook;
use App\Models\MediaItem;
use App\Models\PremiumMember;
use App\Models\RegularMember;
use App\Models\Transaction;
use App\Models\User;

class LibraryManager
{
    private string $dataDir;
    /** @var MediaItem[] */
    private array $mediaList = [];
    /** @var User[] */
    private array $users = [];
    /** @var Transaction[] */
    private array $transactions = [];

    public function __construct(string $dataDir)
    {
        $this->dataDir = rtrim($dataDir, '/\\');
        if (!is_dir($this->dataDir)) {
            mkdir($this->dataDir, 0777, true);
        }
        $this->initializeStorage();
        $this->loadAll();
    }

    private function initializeStorage(): void
    {
        $mediaFile = $this->dataDir . '/media.json';
        $userFile = $this->dataDir . '/users.json';
        $txFile = $this->dataDir . '/transactions.json';

        if (!file_exists($mediaFile)) {
            $seedMedia = [
                [
                    'id' => 'BK-101',
                    'type' => 'ebook',
                    'title' => 'Clean Code: A Handbook of Agile Software Craftsmanship',
                    'author' => 'Robert C. Martin',
                    'category' => 'Pemrograman',
                    'dailyRate' => 6000,
                    'stock' => 5,
                    'pageCount' => 464
                ],
                [
                    'id' => 'BK-102',
                    'type' => 'ebook',
                    'title' => 'Designing Data-Intensive Applications',
                    'author' => 'Martin Kleppmann',
                    'category' => 'Arsitektur Sistem',
                    'dailyRate' => 7500,
                    'stock' => 4,
                    'pageCount' => 616
                ],
                [
                    'id' => 'BK-103',
                    'type' => 'ebook',
                    'title' => 'The Pragmatic Programmer (20th Anniversary Edition)',
                    'author' => 'Andrew Hunt & David Thomas',
                    'category' => 'Pemrograman',
                    'dailyRate' => 6500,
                    'stock' => 6,
                    'pageCount' => 352
                ],
                [
                    'id' => 'BK-104',
                    'type' => 'ebook',
                    'title' => 'Refactoring: Improving the Design of Existing Code',
                    'author' => 'Martin Fowler',
                    'category' => 'Software Engineering',
                    'dailyRate' => 7000,
                    'stock' => 3,
                    'pageCount' => 448
                ],
                [
                    'id' => 'BK-105',
                    'type' => 'ebook',
                    'title' => "Don't Make Me Think, Revisited",
                    'author' => 'Steve Krug',
                    'category' => 'UI/UX Design',
                    'dailyRate' => 5500,
                    'stock' => 5,
                    'pageCount' => 216
                ],
                [
                    'id' => 'BK-106',
                    'type' => 'ebook',
                    'title' => "System Design Interview – An Insider's Guide",
                    'author' => 'Alex Xu',
                    'category' => 'Arsitektur Sistem',
                    'dailyRate' => 7000,
                    'stock' => 4,
                    'pageCount' => 322
                ],
                [
                    'id' => 'AU-201',
                    'type' => 'audiobook',
                    'title' => 'Atomic Habits (Unabridged Audio Edition)',
                    'author' => 'James Clear',
                    'category' => 'Pengembangan Diri',
                    'dailyRate' => 8000,
                    'stock' => 4,
                    'durationMinutes' => 315
                ],
                [
                    'id' => 'AU-202',
                    'type' => 'audiobook',
                    'title' => 'The Phoenix Project: A Novel About IT & DevOps',
                    'author' => 'Gene Kim & Kevin Behr',
                    'category' => 'Manajemen IT',
                    'dailyRate' => 8500,
                    'stock' => 3,
                    'durationMinutes' => 410
                ],
                [
                    'id' => 'AU-203',
                    'type' => 'audiobook',
                    'title' => 'Deep Work: Rules for Focused Success',
                    'author' => 'Cal Newport',
                    'category' => 'Produktivitas',
                    'dailyRate' => 8000,
                    'stock' => 5,
                    'durationMinutes' => 464
                ],
                [
                    'id' => 'AU-204',
                    'type' => 'audiobook',
                    'title' => 'The Psychology of Money (Audio Edition)',
                    'author' => 'Morgan Housel',
                    'category' => 'Bisnis & Keuangan',
                    'dailyRate' => 7500,
                    'stock' => 4,
                    'durationMinutes' => 348
                ]
            ];
            file_put_contents($mediaFile, json_encode($seedMedia, JSON_PRETTY_PRINT));
        }

        if (!file_exists($userFile)) {
            $seedUsers = [
                ['id' => 'MBR-01', 'name' => 'Aminatus Zarodiyah', 'email' => 'amina@luminaread.id', 'tier' => 'Premium'],
                ['id' => 'MBR-02', 'name' => 'Raka Pratama', 'email' => 'raka.pratama@gmail.com', 'tier' => 'Regular'],
                ['id' => 'MBR-03', 'name' => 'Nadia Kusuma', 'email' => 'nadia.kusuma@outlook.com', 'tier' => 'Premium'],
            ];
            file_put_contents($userFile, json_encode($seedUsers, JSON_PRETTY_PRINT));
        }

        if (!file_exists($txFile)) {
            file_put_contents($txFile, json_encode([], JSON_PRETTY_PRINT));
        }
    }

    private function loadAll(): void
    {
        $rawMedia = json_decode((string) file_get_contents($this->dataDir . '/media.json'), true) ?: [];
        foreach ($rawMedia as $m) {
            if (($m['type'] ?? 'ebook') === 'audiobook') {
                $this->mediaList[$m['id']] = new AudioBook(
                    $m['id'], $m['title'], $m['author'], $m['category'],
                    (float) $m['dailyRate'], (int) $m['stock'], (int) ($m['durationMinutes'] ?? 120)
                );
            } else {
                $this->mediaList[$m['id']] = new Ebook(
                    $m['id'], $m['title'], $m['author'], $m['category'],
                    (float) $m['dailyRate'], (int) $m['stock'], (int) ($m['pageCount'] ?? 150)
                );
            }
        }

        $rawUsers = json_decode((string) file_get_contents($this->dataDir . '/users.json'), true) ?: [];
        foreach ($rawUsers as $u) {
            $this->users[$u['id']] = ($u['tier'] === 'Premium')
                ? new PremiumMember($u['id'], $u['name'], $u['email'])
                : new RegularMember($u['id'], $u['name'], $u['email']);
        }

        $rawTx = json_decode((string) file_get_contents($this->dataDir . '/transactions.json'), true) ?: [];
        foreach ($rawTx as $t) {
            $this->transactions[$t['id']] = new Transaction(
                $t['id'], $t['userId'], $t['userName'], $t['mediaId'],
                $t['mediaTitle'], (int) $t['days'], (float) $t['totalPrice'],
                $t['status'], $t['borrowedAt']
            );
        }
    }

    private function saveAll(): void
    {
        $mediaArr = array_values(array_map(fn(MediaItem $m) => $m->toArray(), $this->mediaList));
        $userArr = array_values(array_map(fn(User $u) => $u->toArray(), $this->users));
        $txArr = array_values(array_map(fn(Transaction $t) => $t->toArray(), $this->transactions));

        file_put_contents($this->dataDir . '/media.json', json_encode($mediaArr, JSON_PRETTY_PRINT));
        file_put_contents($this->dataDir . '/users.json', json_encode($userArr, JSON_PRETTY_PRINT));
        file_put_contents($this->dataDir . '/transactions.json', json_encode($txArr, JSON_PRETTY_PRINT));
    }

    public function getDashboardState(): array
    {
        return [
            'media' => array_values(array_map(fn(MediaItem $m) => $m->toArray(), $this->mediaList)),
            'users' => array_values(array_map(fn(User $u) => $u->toArray(), $this->users)),
            'transactions' => array_reverse(array_values(array_map(fn(Transaction $t) => $t->toArray(), $this->transactions))),
        ];
    }

    public function registerUser(string $name, string $email, string $tier): User
    {
        $name = trim($name);
        $email = trim($email);
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Nama lengkap wajib diisi dan format email harus valid.');
        }

        $id = 'MBR-' . strtoupper(substr(uniqid(), -3));
        $user = ($tier === 'Premium')
            ? new PremiumMember($id, $name, $email)
            : new RegularMember($id, $name, $email);

        $this->users[$id] = $user;
        $this->saveAll();
        return $user;
    }

    public function addMedia(array $data): MediaItem
    {
        $title = trim((string) ($data['title'] ?? ''));
        $author = trim((string) ($data['author'] ?? ''));
        $category = trim((string) ($data['category'] ?? 'Pemrograman'));
        $type = (string) ($data['type'] ?? 'ebook');
        $dailyRate = (float) ($data['dailyRate'] ?? 0);
        $stock = (int) ($data['stock'] ?? 1);
        $extra = (int) ($data['extraValue'] ?? 200);

        if ($title === '' || $author === '' || $dailyRate <= 0 || $stock <= 0) {
            throw new \InvalidArgumentException('Lengkapi seluruh informasi buku dengan nilai yang valid.');
        }

        $prefix = $type === 'audiobook' ? 'AU-' : 'BK-';
        $id = $prefix . strtoupper(substr(uniqid(), -3));
        $media = ($type === 'audiobook')
            ? new AudioBook($id, $title, $author, $category, $dailyRate, $stock, $extra)
            : new Ebook($id, $title, $author, $category, $dailyRate, $stock, $extra);

        $this->mediaList[$id] = $media;
        $this->saveAll();
        return $media;
    }

    public function rentMedia(string $userId, string $mediaId, int $days): Transaction
    {
        if (!isset($this->users[$userId])) {
            throw new \InvalidArgumentException('Akun member tidak ditemukan.');
        }
        if (!isset($this->mediaList[$mediaId])) {
            throw new \InvalidArgumentException('Koleksi buku tidak ditemukan.');
        }
        if ($days < 1 || $days > 30) {
            throw new \InvalidArgumentException('Durasi sewa harus antara 1 sampai 30 hari.');
        }

        $user = $this->users[$userId];
        $media = $this->mediaList[$mediaId];

        $activeCount = 0;
        foreach ($this->transactions as $tx) {
            if ($tx->getUserId() === $userId && $tx->getStatus() === 'active') {
                $activeCount++;
            }
        }
        if ($activeCount >= $user->getMaxBorrowLimit()) {
            throw new \RuntimeException(
                "Kuota penuh: Akun {$user->getMembershipTier()} maksimal menyewa {$user->getMaxBorrowLimit()} koleksi aktif."
            );
        }

        $basePrice = $media->calculateBasePrice($days);
        $finalPrice = $user->applyDiscount($basePrice);

        $media->decrementStock();

        $txId = 'INV-' . strtoupper(substr(uniqid(), -5));
        $transaction = new Transaction(
            $txId,
            $user->getId(),
            $user->getName(),
            $media->getId(),
            $media->getTitle(),
            $days,
            $finalPrice
        );

        $this->transactions[$txId] = $transaction;
        $this->saveAll();
        return $transaction;
    }

    public function returnMedia(string $txId): void
    {
        if (!isset($this->transactions[$txId])) {
            throw new \InvalidArgumentException('Data peminjaman tidak ditemukan.');
        }
        $tx = $this->transactions[$txId];
        if ($tx->getStatus() === 'returned') {
            throw new \RuntimeException('Koleksi ini sudah dikembalikan sebelumnya.');
        }

        $tx->markReturned();
        if (isset($this->mediaList[$tx->getMediaId()])) {
            $this->mediaList[$tx->getMediaId()]->incrementStock();
        }
        $this->saveAll();
    }

    public function restockMedia(string $mediaId, int $amount): MediaItem
    {
        if (!isset($this->mediaList[$mediaId])) {
            throw new \InvalidArgumentException('Koleksi buku tidak ditemukan.');
        }
        $media = $this->mediaList[$mediaId];
        $media->restock($amount);
        $this->saveAll();
        return $media;
    }
}