<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Units\Auth\Common\Jobs\NaturalInquiryJob;
use Units\Auth\Common\Jobs\LegalInquiryJob;
use Units\Corporates\Placed\Common\Enums\LegalModeEnum;
use Units\Corporates\Registering\Common\Models\CorporatesRegisteringModel;

class DispatchAuthInquiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:dispatch-auth-inquiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch authentication inquiry jobs for natural and legal entities';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $registerId=CorporatesRegisteringModel::where('id',"18def0ba-d5c0-4c3e-a06b-40ebd6d43e3b")->first()->id;

            $this->info("Dispatching NaturalInquiryJob for register ID: $registerId");
            NaturalInquiryJob::dispatch(register_id:$registerId);

        $registerId=CorporatesRegisteringModel::where('id','18def0ba-d5c0-4c3e-a06b-40ebd6d43e3b')->first()->id;


            $this->info("Dispatching LegalInquiryJob for register ID: $registerId");
            LegalInquiryJob::dispatch(register_id:$registerId);


        return 1;

        $this->info('Authentication inquiry jobs dispatched successfully!');
        return 0;
    }
}
