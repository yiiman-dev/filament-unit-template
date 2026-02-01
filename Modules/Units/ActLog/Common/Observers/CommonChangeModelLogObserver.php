<?php

namespace Units\ActLog\Common\Observers;

use Illuminate\Support\Facades\Log;
use Modules\Basic\Observers\BaseModelChangeLogObserver;


class CommonChangeModelLogObserver extends BaseModelChangeLogObserver
{
    /**
     * @throws \Exception
     */
    public function __construct()
    {
        try {
//            parent::__construct();
            switch (filament()->getCurrentPanel()->getId()){
                case 'admin':
                    $this->actLogService = app( \Units\ActLog\Admin\Services\ActLogService::class);
                    break;
                case 'manage':
                    $this->actLogService = app( \Units\ActLog\Manage\Services\ActLogService::class);
                    break;
                case 'my':
                    $this->actLogService = app( \Units\ActLog\My\Services\ActLogService::class);
                    break;
            }
        }catch(\Exception $e){
            Log::error('Can not create observer service',(array)$e->getTrace());
        }

    }
}
