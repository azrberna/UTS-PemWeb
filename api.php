<?php
declare(strict_types=1);

require_once __DIR__ . '/autoload.php';

use App\Services\LibraryManager;

header('Content-Type: application/json; charset=utf-8');

try {
    $manager = new LibraryManager(__DIR__ . '/data');
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        echo json_encode([
            'success' => true,
            'data' => $manager->getDashboardState(),
        ]);
        exit;
    }

    $input = json_decode((string) file_get_contents('php://input'), true) ?? [];
    $action = $input['action'] ?? '';

    switch ($action) {
        case 'rent':
            $tx = $manager->rentMedia(
                (string) ($input['userId'] ?? ''),
                (string) ($input['mediaId'] ?? ''),
                (int) ($input['days'] ?? 1)
            );
            $message = "Berhasil menyewa '{$tx->toArray()['mediaTitle']}'!";
            break;

        case 'return':
            $manager->returnMedia((string) ($input['transactionId'] ?? ''));
            $message = 'Media berhasil dikembalikan dan stok diperbarui.';
            break;

        case 'restock':
            $amount = (int) ($input['amount'] ?? 1);
            $media = $manager->restockMedia((string) ($input['mediaId'] ?? ''), $amount);
            $message = "Stok '{$media->getTitle()}' berhasil ditambah (+{$amount} lisensi).";
            break;

        case 'register_user':
            $user = $manager->registerUser(
                (string) ($input['name'] ?? ''),
                (string) ($input['email'] ?? ''),
                (string) ($input['tier'] ?? 'Regular')
            );
            $message = "Member {$user->getName()} ({$user->getMembershipTier()}) berhasil didaftarkan.";
            break;

        case 'add_media':
            $media = $manager->addMedia($input);
            $message = "{$media->getMediaType()} '{$media->getTitle()}' berhasil ditambahkan ke katalog.";
            break;

        default:
            throw new \InvalidArgumentException('Aksi API tidak dikenali.');
    }

    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => $manager->getDashboardState(),
    ]);
} catch (\Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}