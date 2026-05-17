<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$requestGenre = 'Acción';
$escapedGenre = trim(json_encode($requestGenre), '"'); 

$filteredItems = \App\Models\Media::whereRaw("extra_data LIKE ?", ['%"' . $escapedGenre . '"%'])->get();
echo "Items matching genre via Raw LIKE: " . $filteredItems->count() . "\n";
