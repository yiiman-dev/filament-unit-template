<?php

namespace Units\Auth\Common\Jobs;

use EightyNine\FilamentPageAlerts\PageAlert;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Basic\BaseKit\BaseJob;
use Modules\Basic\Helpers\Helper;
use Units\Corporates\Registering\Common\Models\CorporatesRegisteringModel;
use Units\FinnoTech\Common\Jobs\CorporateJournalInquiryJob;
use Units\FinnoTech\Common\Jobs\MobileAndNationalCodeVerifyJob;
use Units\FinnoTech\Common\Services\dto\FinnoTechCorporateJournalDto;
use Units\FinnoTech\Common\Services\dto\FinnoTechErrorDto;
use Units\FinnoTech\Common\Services\dto\FinnoTechMobileAndNationalCodeVerifyDto;
use Units\FinnoTech\Common\Services\FinnoTechService;

/**
 * @method static void dispatch(string $register_id)
 * @method static void dispatchSync(string $register_id)
 */
class LegalInquiryJob extends BaseJob
{
    public string $register_id;


    public function __construct(string $register_id)
    {
        $this->register_id = $register_id;
    }


    public function handle()
    {
        try {
            NaturalInquiryJob::dispatchSync(register_id:$this->register_id);
            $trackId = Str::uuid();
            $cr = CorporatesRegisteringModel::where('id', $this->register_id)->first();

            $corporateJournal=CorporateJournalInquiryJob::dispatchSync(companyId:$cr->national_id,trackId:$trackId);
            /**
             * @var FinnoTechErrorDto $corporateJournal
             */
            if ($corporateJournal->isSuccess()){
                $cr->addMetaKey('finnotech::journal', $corporateJournal->toArray());
                $cr->addMetaKey('finnotech::journal-date', date('Y-m-d H:i:s'));
                $cr->update(['validated_company_at'=>date('Y-m-d H:i:s')]);
            }else{
                $cr->addMetaKey('finnotech::journal_error', $corporateJournal->message);
            }

            return $corporateJournal;
        } catch (\Exception $e) {
            Log::error('LegalInquiryJob failed with exception', [
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
