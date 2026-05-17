<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$items = \App\Models\Media::where('extra_data->full_details_loaded', true)->get();
foreach($items as $item) {
    echo "Title: {$item->title}\n";
    echo "Genres: " . json_encode($item->extra_data['genres'] ?? []) . "\n";
}
