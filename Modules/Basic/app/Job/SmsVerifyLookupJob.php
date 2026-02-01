<?php

namespace Modules\Basic\Job;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Kavenegar\KavenegarApi;
use Modules\Basic\BaseKit\BaseJob;
use Carbon\Carbon;

class SmsVerifyLookupJob extends BaseJob
{
    use Queueable;

    public string $receptor;
    public string $token;
    public string|null $token2;
    public string|null $token3;
    public string $template;
    public string|null $type;
    public Carbon $created_at;

    public function __construct(
        $receptor,
        $token,
        $token2,
        $token3,
        $template,
        $type = null
    ) {
        $this->receptor = $receptor;
        $this->token = $token;
        $this->token2 = $token2;
        $this->token3 = $token3;
        $this->template = $template;
        $this->type = $type;
        $this->created_at = now();
    }

    public function handle(): void
    {
        // در محیط تست از ارسال پیامک جلوگیری می‌شود
        if (app()->environment('testing')) {
            return;
        }

        Log::info('SMS Job and env(SMS_ENABLED) is '.env('SMS_ENABLED'));
        if($this->created_at->diffInMinutes() > 10){
            Log::info('Verify lookup SMS sent has timeout');
            return;
        }

//        if(!(boolean)env('SMS_ENABLED','false')){
//            Log::info('SMS: system in maintenance mode, dont send SMS'  );
//            return;
//        }
        if (env('SMS_ENABLED')){
            $result = app(KavenegarApi::class)->VerifyLookup(
                str_replace('+98', '0', $this->receptor),
                $this->token,
                $this->token2,
                $this->token3,
                $this->template,
                $this->type
            );
            if ($result) {
                Log::info('Verify lookup SMS sent successfully');
                Log::info('Receptor: ' . $this->receptor);
                Log::info('Template: ' . $this->template);
            } else {
                Log::error('Verify lookup SMS sent failed');
            }
        }
    }
}
