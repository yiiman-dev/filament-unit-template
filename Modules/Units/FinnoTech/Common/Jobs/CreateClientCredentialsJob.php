<?php

namespace Units\FinnoTech\Common\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Units\FinnoTech\Common\Services\FinnoTechService;
use Units\FinnoTech\Common\Services\exceptions\FinnoTechServiceCreateTokenException;

class CreateClientCredentialsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(FinnoTechService $finnoTechService)
    {
        try {
            \Log::info('Starting Finnotech client credentials creation job');

            $result = $finnoTechService->createClientCredentials();

            \Log::info('Successfully created Finnotech client credentials', [
                'status' => $result['status'] ?? 'unknown',
                'expires_at' => $result['result']['lifeTime'] ?? null
            ]);

            return $result;
        } catch (FinnoTechServiceCreateTokenException $e) {
            \Log::error('Failed to create Finnotech client credentials', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Unexpected error in Finnotech client credentials creation job', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
