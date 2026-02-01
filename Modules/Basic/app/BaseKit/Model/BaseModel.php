<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/8/25, 3:13 PM
 */

namespace Modules\Basic\BaseKit\Model;

use Illuminate\Database\Eloquent\Model;
use Modules\Basic\BaseKit\Model\Contracts\BaseModelContract;
use phpDocumentor\Reflection\Types\This;

/**
 *
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel query()
 * @mixin \Eloquent
 */
abstract class BaseModel extends Model implements BaseModelContract
{
    public string|null $remote_actor=null;
    protected $hidden = ['remote_actor'];
    public static string $factory;
    public function getConnectionName()
    {
        $filament_id=filament()->getId();
        if ($filament_id==$this->original_connection()){
            $this->connection=$filament_id;
            return $this->connection;
        }else{
            if (app()->runningUnitTests()){
                $this->connection='test_'.$this->original_connection();
            }else{
                $this->connection='api_'.$this->original_connection();
            }
            return $this->connection;
        }
    }


    protected static function newFactory()
    {
        return static::$factory::new();
    }
}
