<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ImportSearchResultsJob implements ShouldQueue
{
    use Queueable;

    protected $results;

    /**
     * Create a new job instance.
     */
    public function __construct(array $results)
    {
        $this->results = $results;
    }

    /**
     * Execute the job.
     */
    public function handle(\App\Services\MediaIntegrationService $mediaService): void
    {
        foreach ($this->results as $result) {
            try {
                $mediaService->importSearchResult($result);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Error importando resultado en background: " . $e->getMessage());
            }
        }
    }
}
