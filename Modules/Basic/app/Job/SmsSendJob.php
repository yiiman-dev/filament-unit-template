<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/11/25, 8:36 PM
 */

namespace Modules\Basic\Job;

use Illuminate\Support\Facades\Log;
use Kavenegar\KavenegarApi;
use Modules\Basic\BaseKit\BaseJob;
use Modules\Basic\Helpers\Helper;

/**
 * Send norman SMS Job
 */
class SmsSendJob extends BaseJob
{
    public string $sender;
    public string $receptor;
    public string $message;
    public string|null $date = null;
    public string|null $type = null;
    public string|null $localid = null;

    public function __construct(
        $receptor,
        $message,
        $date = null,
        $type = null,
        $localid = null
    ) {
       $this->receptor=$receptor;
       $this->message=$message;
       $this->date=$date;
       $this->type=$type;
       $this->localid=$localid;
    }

    public function handle()
    {
        $this->receptor=Helper::denormalize_phone_number($this->receptor);
        Log::info('Sender: ' . env('KAVENEGAR_SENDER'));
        Log::info('Receptor: ' . $this->receptor);
        Log::info('Message: ' . $this->message);
        Log::info('Date: ' . $this->date);
        Log::info('Type: ' . $this->type);
        Log::info('Localid: ' . $this->localid);
        if (env('SMS_ENABLED')){
            $result = app(KavenegarApi::class)->Send(env('KAVENEGAR_SENDER'), $this->receptor, $this->message);
            if($result){
                Log::info('SMS sent successfully');
            }else{
                Log::error('SMS sent failed');
            }
        }else{
            Log::info('SMS not sent to : ' . $this->receptor.' because SMS_ENABLE environment is disable');
        }
    }

}
