<?php

namespace Modules\Basic\Helpers;

use Illuminate\Support\Facades\Log;

class TransactionHelper
{

    /**
     * @throws \Throwable
     */
    public static function begin():void
    {
        \DB::connection('my')->beginTransaction();
        \DB::connection('manage')->beginTransaction();
        \DB::connection('admin')->beginTransaction();
        \DB::connection('api_my')->beginTransaction();
        \DB::connection('api_manage')->beginTransaction();
        \DB::connection('api_admin')->beginTransaction();
    }

    /**
     * @throws \Throwable
     */
    public static function rollback():void
    {
        \DB::connection('my')->rollBack();
        \DB::connection('manage')->rollBack();
        \DB::connection('admin')->rollBack();
        \DB::connection('laravel')->rollBack();
        \DB::connection('api_my')->rollBack();
        \DB::connection('api_manage')->rollBack();
        \DB::connection('api_admin')->rollBack();
    }

    /**
     * @throws \Throwable
     */
    public static function commit():void
    {
        \DB::connection('my')->commit();
        \DB::connection('manage')->commit();
        \DB::connection('admin')->commit();
        \DB::connection('laravel')->commit();
        \DB::connection('api_my')->commit();
        \DB::connection('api_manage')->commit();
        \DB::connection('api_admin')->commit();
    }


    public static function code(callable $code_block,callable $cache_block=null)
    {
        try{
            static::begin();
            $result=$code_block();
            static::commit();
            return $result;
        }catch (\Exception $e){
            Log::error($e->getMessage(),['trace'=>$e->getTraceAsString()]);
            static::rollback();
            if (!empty($cache_block)){
                $cache_block();
            }
        }
    }

}
