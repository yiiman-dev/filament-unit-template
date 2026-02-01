<?php

namespace Units\FinnoTech\Common\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Units\FinnoTech\Common\Services\dto\FinnoTechAuthorizedSignatoriesDto;
use Units\FinnoTech\Common\Services\dto\FinnoTechErrorDto;
use Units\FinnoTech\Common\Services\FinnoTechService;
use Units\FinnoTech\Common\Services\dto\FinnoTechMobileAndNationalCodeVerifyDto;

/**
 * @method static FinnoTechMobileAndNationalCodeVerifyDto|FinnoTechErrorDto dispatchSync(string $mobileNumber, string $nationalCode,string $trackId='')
 * @method static void dispatch(string $mobileNumber, string $nationalCode,string $trackId='')
 *
 */
class MobileAndNationalCodeVerifyJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $mobileNumber;
    protected string $nationalCode;
    protected string $trackId;

    public function __construct(string $mobileNumber, string $nationalCode,string $trackId='')
    {
        if(empty($trackId)){
            $trackId=Str::uuid();
        }
        $this->mobileNumber = $mobileNumber;
        $this->nationalCode = $nationalCode;
        $this->trackId      = $trackId;
    }

    public function handle(FinnoTechService $finnoTechService): FinnoTechMobileAndNationalCodeVerifyDto|FinnoTechErrorDto
    {
        try {
            \Log::info('Starting Finnotech mobile and national code verification job', [
                'mobile_number' => $this->mobileNumber,
                'national_code' => $this->nationalCode,
                'trackId'       => $this->trackId,
            ]);

            $result = $finnoTechService->mobileAndNationalCodeVerify($this->mobileNumber, $this->nationalCode);

            if ($result->isSuccess()){
                \Log::info('Completed Finnotech mobile and national code verification job', [
                    'mobile_number' => $this->mobileNumber,
                    'national_code' => $this->nationalCode,
                    'trackId'       => $this->trackId,
                    'is_valid' => $result->getIsValid()
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            \Log::error('Failed in Finnotech mobile and national code verification job', [
                'mobile_number' => $this->mobileNumber,
                'national_code' => $this->nationalCode,
                'trackId'       => $this->trackId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }
}
