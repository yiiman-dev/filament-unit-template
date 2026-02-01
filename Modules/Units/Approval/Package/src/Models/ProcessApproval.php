<?php

namespace EightyNine\Approvals\Models;

class ProcessApproval extends \RingleSoft\LaravelProcessApproval\Models\ProcessApproval
{
    protected $casts = [
        'approvable_id' => 'string',
    ];
}
