<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/13/25, 1:38 PM
 */

namespace Modules\Basic\Concerns;

use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\This;

trait HasUUID
{

    /**
     * کلید هایی که باید برای انها قبل از ایجاد رکورد یو یو ای دی ژنریت شود را درج کنید.
     * @return void
     */
    protected static function get_uuid_attributes():array
    {
        return ['id'];
    }
    protected static function bootHasUuid()
    {
        $attributes=self::get_uuid_attributes();
        static::creating(function ($model) use($attributes){
            foreach ($attributes as $attr){
                $model->{$attr} = $model->{$attr} ?? Str::uuid()->toString();
            }
        });
    }
}
