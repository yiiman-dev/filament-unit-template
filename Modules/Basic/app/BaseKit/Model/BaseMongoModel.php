<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/11/25, 6:42 PM
 */

namespace Modules\Basic\BaseKit\Model;

use Modules\Basic\BaseKit\Model\Contracts\BaseModelContract;
use MongoDB\Laravel\Eloquent\Builder;
use MongoDB\Laravel\Eloquent\Model;

/**
 *
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseMongoModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseMongoModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseMongoModel query()
 * @mixin \Eloquent
 */
abstract class BaseMongoModel extends Model implements BaseModelContract
{
    public function getConnectionName()
    {
        $filament_id=filament()->getId();
        if ($filament_id==$this->original_connection()){
            $this->connection=$filament_id;
            return $this->connection;
        }else{
            $this->connection='api_'.$this->original_connection();
            return $this->connection;
        }
    }
}
