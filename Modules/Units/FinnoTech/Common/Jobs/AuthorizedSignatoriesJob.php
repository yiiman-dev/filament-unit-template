<?php

namespace Units\FinnoTech\Common\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Basic\BaseKit\BaseJob;
use Units\FinnoTech\Common\Services\dto\FinnoTechErrorDto;
use Units\FinnoTech\Common\Services\FinnoTechService;
use Units\FinnoTech\Common\Services\dto\FinnoTechAuthorizedSignatoriesDto;

/**
 * @method static FinnoTechAuthorizedSignatoriesDto|FinnoTechErrorDto dispatchSync(string $companyId, string $trackId)
 * @method static void dispatch(string $companyId, string $trackId)
 *
 */
class AuthorizedSignatoriesJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected string $companyId;
    protected string $trackId;

    public function __construct(string $companyId, string $trackId)
    {
        $this->companyId = $companyId;
        $this->trackId = $trackId;
    }

    public function handle(): FinnoTechAuthorizedSignatoriesDto|FinnoTechErrorDto
    {
        try {
            $finnoTechService = new FinnoTechService();
            \Log::info('Starting Finnotech authorized signatories job', [
                'company_id' => $this->companyId
            ]);

            $result = $finnoTechService->authorizedSignatories($this->companyId, $this->trackId);

            if ($result->isSuccess()){
                \Log::info('Completed Finnotech mobile and national code verification job', [
                    'trackId'       => $this->trackId,
                    'boardMembers' => $result->boardMembers,
                    'newspaperDate'=>$result->newspaperDate
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            \Log::error('Failed in Finnotech authorized signatories job', [
                'company_id' => $this->companyId,
                'error' => $e->getMessage(),
                'trackId' => $this->trackId,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
