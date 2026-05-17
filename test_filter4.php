<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pdo = \DB::connection()->getPdo();
$stmt = $pdo->query("SELECT id, extra_data FROM media WHERE extra_data LIKE '%Acci%'");
foreach($stmt->fetchAll() as $row) {
    echo "ID: {$row['id']} | Extra: {$row['extra_data']}\n";
}
