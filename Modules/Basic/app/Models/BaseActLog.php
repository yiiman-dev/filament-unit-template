<?php

namespace Modules\Basic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Units\Auth\Admin\Models\UserModel;

/**
 * Base Activity Log Model
 *
 * This model represents activity logs in the system.
 *
 * @property string $action The action performed
 * @property string $type The type of action
 * @property string $ip_address The IP address from which the action was performed
 * @property string $user_agent The user agent information
 * @property string $target_url The URL that was targeted
 * @property string $target_title The title of the target
 * @property array $details Additional details stored as JSON
 * @property string $actor_number The phone number of the actor
 * @property string $hash The hash of the current log
 * @property string $previous_hash The hash of the previous log
 * @property \Carbon\Carbon $created_at When the log was created
 * @property \Carbon\Carbon $updated_at When the log was last updated
 * @property-read UserModel|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseActLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseActLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseActLog query()
 * @mixin \Eloquent
 */
class BaseActLog extends Model
{
    protected $table = 'act_logs';

    protected $fillable = [
        'action',
        'type',
        'ip_address',
        'user_agent',
        'target_url',
        'target_title',
        'details',
        'actor_number',
        'hash',
        'previous_hash'
    ];

    protected $casts = [
        'details' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];


}









