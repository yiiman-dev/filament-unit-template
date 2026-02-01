<?php

namespace RingleSoft\LaravelProcessApproval\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Basic\BaseKit\Model\BaseSqlModel;
use RingleSoft\LaravelProcessApproval\Traits\MultiTenant;

class ProcessApproval extends BaseSqlModel
{
    use MultiTenant;

    public $guarded = ['id'];

    public $with = ['user'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('process_approval.users_model'));
    }

    public function processApprovalFlowStep(): BelongsTo
    {
        return $this->belongsTo(ProcessApprovalFlowStep::class);
    }

    public function approvable(): MorphTo
    {
        return $this->morphTo('approvable');
    }

    public function getSignature()
    {
        if (method_exists($this->user, 'getSignature')) {
            return $this->user?->getSignature();
        }
        return null;
    }

    public function original_connection(): string
    {
        return 'manage';
    }
}
