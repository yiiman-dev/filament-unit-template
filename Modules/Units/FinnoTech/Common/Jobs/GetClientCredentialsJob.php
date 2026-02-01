<?php

namespace Units\FinnoTech\Common\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Units\FinnoTech\Common\Services\FinnoTechService;
use Units\FinnoTech\Common\Services\exceptions\FinnoTechServiceCreateTokenException;

class GetClientCredentialsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(FinnoTechService $finnoTechService)
    {
        try {
            \Log::info('Starting Finnotech client credentials retrieval job');

            $result = $finnoTechService->getClientCredentials();

            \Log::info('Successfully retrieved Finnotech client credentials', [
                'has_cached_token' => cache()->has('finnotech-client-credentials'),
                'token_status' => $result['status'] ?? 'unknown'
            ]);

            return $result;
        } catch (FinnoTechServiceCreateTokenException | ContainerExceptionInterface | NotFoundExceptionInterface $e) {
            \Log::error('Failed to get Finnotech client credentials', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Unexpected error in Finnotech client credentials retrieval job', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
