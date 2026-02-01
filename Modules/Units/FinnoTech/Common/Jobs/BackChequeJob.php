<?php

namespace Units\FinnoTech\Common\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Units\FinnoTech\Common\Services\dto\FinnoTechErrorDto;
use Units\FinnoTech\Common\Services\FinnoTechService;
use Units\FinnoTech\Common\Services\dto\FinnoTechBackChequeDto;

class BackChequeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $user;

    public function __construct(string $user)
    {
        $this->user = $user;
    }

    public function handle(FinnoTechService $finnoTechService): FinnoTechBackChequeDto|FinnoTechErrorDto
    {
        try {
            \Log::info('Starting Finnotech back cheque job', [
                'user' => $this->user
            ]);

            $result = $finnoTechService->backCheque($this->user);

            \Log::info('Completed Finnotech back cheque job', [
                'user' => $this->user,
                'has_result' => $result !== null
            ]);

            return $result;
        } catch (\Exception $e) {
            \Log::error('Failed in Finnotech back cheque job', [
                'user' => $this->user,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
