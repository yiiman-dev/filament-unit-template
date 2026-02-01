<?php

namespace Units\Auth\Common\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Basic\BaseKit\BaseJob;
use Modules\Basic\Helpers\Helper;
use Units\Corporates\Registering\Common\Models\CorporatesRegisteringModel;
use Units\FinnoTech\Common\Jobs\CorporateJournalInquiryJob;
use Units\FinnoTech\Common\Jobs\MobileAndNationalCodeVerifyJob;
use Units\FinnoTech\Common\Services\dto\FinnoTechAuthorizedSignatoriesDto;
use Units\FinnoTech\Common\Services\dto\FinnoTechCorporateJournalDto;
use Units\FinnoTech\Common\Services\dto\FinnoTechErrorDto;
use Units\FinnoTech\Common\Services\dto\FinnoTechMobileAndNationalCodeVerifyDto;
use Units\FinnoTech\Common\Services\FinnoTechService;

/**
 * @method static FinnoTechMobileAndNationalCodeVerifyDto|FinnoTechErrorDto dispatchSync(string $register_id)
 * @method static void dispatch(string $register_id)
 *
 */
class NaturalInquiryJob extends BaseJob
{
    public string $register_id;

    public function __construct(string $register_id)
    {
        $this->register_id = $register_id;
    }


    public function handle()
    {
        try {

            $trackId = Str::uuid();
            $cr = CorporatesRegisteringModel::where('id', $this->register_id)->first();
            if (!$cr) {
                Log::error('LegalInquiryJob - Corporate record not found', [
                    'register_id' => $this->register_id,
                    'step' => 'fetch_corporate_record'
                ]);
                return;
            }
            $cr->addMetaKey('finnotech::ceo_inquiry-track_id', $trackId);
            $cr->addMetaKey('finnotech::ceo_inquiry-date', date('Y-m-d H:i:s'));
            if (empty($cr->meta['finnotech::ceo_validation_result'])){
                $ceoShahkar = MobileAndNationalCodeVerifyJob::dispatchSync(
                    mobileNumber: Helper::denormalize_phone_number($cr->ceo_mobile),
                    nationalCode: $cr->ceo_national_code,
                    trackId: $trackId,
                );
                if ($ceoShahkar->isSuccess()){
                    $cr->update(['validated_ceo_at' => date('Y-m-d H:i:s')]);
                    $cr->addMetaKey('finnotech::ceo_validation_result', $ceoShahkar->isValid);
                }
            }



            if (!empty($cr->agent_national_code) && !empty($cr->agent_mobile)) {
                $trackId = Str::uuid();
                $cr->addMetaKey('finnotech::agent_inquiry-track_id', $trackId);
                $cr->addMetaKey('finnotech::agent_inquiry-date', date('Y-m-d H:i:s'));
                if (empty($cr->meta['finnotech::agent_validation_result'])){
                    $cr->update(['validated_agent_at' => date('Y-m-d H:i:s')]);
                    $agentShahkar = MobileAndNationalCodeVerifyJob::dispatchSync(
                        mobileNumber: Helper::denormalize_phone_number($cr->agent_mobile),
                        nationalCode: $cr->agent_national_code,
                        trackId: $trackId,
                    );
                    /**
                     * @var FinnoTechMobileAndNationalCodeVerifyDto $agentShahkar
                     */
                    if ($agentShahkar->isSuccess()){
                        $cr->addMetaKey('finnotech::agent_validation_result', $agentShahkar->isValid);
                    }
                }
            }





        } catch (\Exception $e) {
            Log::error('NaturalInquiryJob failed with exception', [
                'register_id' => $this->register_id,
                'step' => 'exception_handler',
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }


}
