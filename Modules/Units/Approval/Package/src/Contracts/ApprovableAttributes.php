<?php

namespace Units\Approval\Package\src\Contracts;

interface ApprovableAttributes
{
    public static function flowLabel();

    /**
     * اقداماتی که برای گردش کار بر روی این مدل در دسترس هستند را به صورت یک آرایه لیست کنید
     * @return mixed
     */
    public static function flowActions():array;

    /**
     *  توضیحات لازم در مورد هر یک از اقدامات را در یک آرایه درج کنید
     * @return array
     */
    public static function flowActionDescriptions():array;
}
