<?php

namespace PipLines\GetData\StateCity;

use Modules\Basic\BaseKit\BaseService;
use Units\StateCity\Common\Models\Province;

class GetProvincesData extends BaseService
{
    public function handle(): self
    {
        $provinces = Province::all();

        $this->setSuccessResponse(compact('provinces'));
    
        return $this;
    }

}
