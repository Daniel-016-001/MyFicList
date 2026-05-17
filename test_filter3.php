<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$requestGenre = 'Acción';

// Intentar con LIKE puro usando json_encode para emular cómo lo guarda Laravel
$escapedGenre = trim(json_encode($requestGenre), '"'); // "Acci\u00f3n"
$filteredItems = \App\Models\Media::where('extra_data', 'like', '%"' . $escapedGenre . '"%')->get();
echo "Items matching genre via LIKE: " . $filteredItems->count() . "\n";
