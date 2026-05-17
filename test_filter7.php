<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pdo = \DB::connection()->getPdo();
$stmt = $pdo->query("SELECT extra_data FROM media WHERE id = 44");
$data = $stmt->fetchColumn();
file_put_contents('dump.txt', $data);
echo "Dumped to dump.txt";
