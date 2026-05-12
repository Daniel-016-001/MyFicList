<?php
// Test script to debug RAWG game details

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;
use App\Services\MediaIntegrationService;

$rawgKey = config('services.rawg.key');
echo "RAWG Key: " . ($rawgKey ? substr($rawgKey, 0, 8) . '...' : 'MISSING') . "\n\n";

// Step 1: Test basic search
echo "=== Step 1: RAWG Search ===\n";
try {
    $response = Http::get("https://api.rawg.io/api/games", [
        'key' => $rawgKey,
        'search' => 'The Witcher 3',
        'page_size' => 2
    ]);
    
    $data = $response->json();
    echo "Status: " . $response->status() . "\n";
    
    if (!empty($data['results'])) {
        $game = $data['results'][0];
        $gameId = $game['id'];
        echo "First result: {$game['name']} (ID: {$gameId})\n\n";
    } else {
        echo "No results found\n";
        echo "Response: " . json_encode($data) . "\n";
        exit;
    }
} catch (\Exception $e) {
    echo "Search ERROR: " . $e->getMessage() . "\n";
    exit;
}

// Step 2: Test details fetch
echo "=== Step 2: RAWG Details for ID {$gameId} ===\n";
try {
    $details = Http::get("https://api.rawg.io/api/games/{$gameId}", [
        'key' => $rawgKey
    ]);
    
    $detailsData = $details->json();
    echo "Status: " . $details->status() . "\n";
    
    if (isset($detailsData['detail'])) {
        echo "ERROR: " . $detailsData['detail'] . "\n";
    } else {
        echo "Name: " . ($detailsData['name'] ?? 'N/A') . "\n";
        echo "Released: " . ($detailsData['released'] ?? 'N/A') . "\n";
        echo "Has description: " . (!empty($detailsData['description_raw']) ? 'YES' : 'NO') . "\n";
        echo "Has image: " . (!empty($detailsData['background_image']) ? 'YES' : 'NO') . "\n";
    }
} catch (\Exception $e) {
    echo "Details ERROR: " . $e->getMessage() . "\n";
}

// Step 3: Test movies/trailers
echo "\n=== Step 3: RAWG Movies/Trailers for ID {$gameId} ===\n";
try {
    $movies = Http::get("https://api.rawg.io/api/games/{$gameId}/movies", [
        'key' => $rawgKey
    ]);
    
    echo "Status: " . $movies->status() . "\n";
    $moviesData = $movies->json();
    echo "Results count: " . count($moviesData['results'] ?? []) . "\n";
    
    if (!empty($moviesData['results'])) {
        $trailer = $moviesData['results'][0];
        echo "First trailer: " . ($trailer['name'] ?? 'N/A') . "\n";
        echo "Data max: " . ($trailer['data']['max'] ?? 'N/A') . "\n";
        echo "Data 480: " . ($trailer['data']['480'] ?? 'N/A') . "\n";
    }
} catch (\Exception $e) {
    echo "Movies ERROR: " . $e->getMessage() . "\n";
}

// Step 4: Test the full importToDatabase flow
echo "\n=== Step 4: Full importToDatabase flow ===\n";
try {
    $service = app(MediaIntegrationService::class);
    $media = $service->importToDatabase($gameId, 'RAWG', 'game');
    
    if ($media) {
        echo "SUCCESS! Media imported:\n";
        echo "  ID: {$media->id}\n";
        echo "  Title: {$media->title}\n";
        echo "  Source: {$media->source}\n";
        echo "  Type: {$media->media_type}\n";
        echo "  Extra data keys: " . implode(', ', array_keys($media->extra_data ?? [])) . "\n";
    } else {
        echo "FAILED: importToDatabase returned null\n";
    }
} catch (\Exception $e) {
    echo "importToDatabase ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
