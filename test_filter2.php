<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$requestGenre = 'Acción';
$filteredItems = \App\Models\Media::whereJsonContains('extra_data->genres', $requestGenre)->get();
echo "Items matching genre '$requestGenre': " . $filteredItems->count() . "\n";

$requestGenre2 = 'Comedia';
$filteredItems2 = \App\Models\Media::whereJsonContains('extra_data->genres', $requestGenre2)->get();
echo "Items matching genre '$requestGenre2': " . $filteredItems2->count() . "\n";
