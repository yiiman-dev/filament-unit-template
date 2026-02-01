<?php

namespace Units\Approval\Manage\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Basic\BaseKit\Model\BaseSqlModel;


class ProcessApprovalFlow extends BaseSqlModel
{
    protected $guarded = ['id'];

    public static function getList(): \Illuminate\Contracts\Pagination\LengthAwarePaginator|LengthAwarePaginator|array
    {
        return self::query()->with(['steps'])->paginate();
    }

    public function steps(): HasMany
    {
        return $this->hasMany(ProcessApprovalFlowStep::class);
    }

    public function original_connection(): string
    {
        return 'manage';
    }
}
