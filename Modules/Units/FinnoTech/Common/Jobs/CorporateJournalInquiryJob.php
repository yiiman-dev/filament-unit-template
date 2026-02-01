<?php

namespace Units\FinnoTech\Common\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Units\FinnoTech\Common\Services\dto\FinnoTechErrorDto;
use Units\FinnoTech\Common\Services\FinnoTechService;
use Units\FinnoTech\Common\Services\dto\FinnoTechCorporateJournalDto;

/**
 * @method static void dispatch( string $companyId, string $trackId)
 * @method static FinnoTechCorporateJournalDto|FinnoTechErrorDto dispatchSync( string $companyId, string $trackId)
 */
class CorporateJournalInquiryJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;




    public function __construct(public string $companyId,public string $trackId)
    {

    }

    public function handle(FinnoTechService $finnoTechService): FinnoTechCorporateJournalDto|FinnoTechErrorDto
    {
        try {
            \Log::info('Starting Finnotech corporate journal inquiry job', [
                'companyId' => $this->companyId,
                'trackId' => $this->trackId
            ]);

            $result = $finnoTechService->corporateJournalInquiry($this->companyId, $this->trackId);

            \Log::info('Completed Finnotech corporate journal inquiry job', [
                'companyId' => $this->companyId,
                'trackId' => $this->trackId,
                'has_result' => $result !== null
            ]);

            return $result;
        } catch (\Exception $e) {
            \Log::error('Failed in Finnotech corporate journal inquiry job', [
                'companyId' => $this->companyId,
                'trackId' => $this->trackId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
