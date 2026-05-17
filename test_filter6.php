<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pdo = \DB::connection()->getPdo();
$str = '%"Acci\u00f3n"%';
$stmt = $pdo->prepare("SELECT id FROM media WHERE extra_data LIKE ?");
$stmt->execute([$str]);
echo "Rows matched: " . count($stmt->fetchAll()) . "\n";
