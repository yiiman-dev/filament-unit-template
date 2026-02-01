<?php

namespace Modules\Basic\Models\Concens;

interface ModelApiInterface
{
    /**
     * آدرس اینترنتی مدل هدف را با استفاده از این تابع اعلام کنید
     * @return string
     */
    public function get_remote_base_api():string;
}
