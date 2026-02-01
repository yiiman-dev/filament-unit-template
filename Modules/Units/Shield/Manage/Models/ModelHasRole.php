<?php

namespace Units\Shield\Manage\Models;


use Illuminate\Database\Eloquent\Model;

class ModelHasRole extends Model
{
    protected $primaryKey = ['role_id', 'model_id', 'model_type'];
    public $incrementing = false;
    protected $table = 'model_has_roles';
    protected $fillable = [
        'role_id',
        'model_type',
        'model_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_reason'
    ];

    public function getConnectionName(): string
    {
        $this->connection = 'manage';

        return $this->connection;
    }
}
