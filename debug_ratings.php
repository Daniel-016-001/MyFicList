<?php

use App\Models\Media;

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

// Check books with ratings
$booksWithRatings = Media::where('media_type', 'book')
    ->with('userRatings')
    ->latest()
    ->limit(3)
    ->get();

echo "=== BOOKS ===\n";
foreach ($booksWithRatings as $book) {
    echo "Book: {$book->title}\n";
    echo "  Ratings count: " . $book->userRatings()->count() . "\n";
    echo "  Avg score: " . ($book->userRatings()->avg('score') ?? 'null') . "\n\n";
}

// Compare with anime
$animeWithRatings = Media::where('media_type', 'anime')
    ->with('userRatings')
    ->latest()
    ->limit(1)
    ->get();

echo "=== ANIME ===\n";
foreach ($animeWithRatings as $anime) {
    echo "Anime: {$anime->title}\n";
    echo "  Ratings count: " . $anime->userRatings()->count() . "\n";
    echo "  Avg score: " . ($anime->userRatings()->avg('score') ?? 'null') . "\n";
}
?>
