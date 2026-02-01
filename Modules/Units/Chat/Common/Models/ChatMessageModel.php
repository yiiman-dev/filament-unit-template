<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/8/25, 7:57 PM
 */

namespace Units\Chat\Common\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Basic\BaseKit\Observers\BaseObserver;
use Modules\Basic\Concerns\HasUUID;
use Units\ActLog\Admin\Observsers\ChangeModelLogObserver;

/**
 * ChatMessageModel
 *
 * Model representing a chat message within a chat thread.
 * Each message belongs to a specific chat thread and contains content,
 * sender information, and read status tracking.
 *
 * Eloquent Model Methods:
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel whereChatThreadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel whereSenderType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel whereIsSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel whereSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel whereIn($column, $values, $boolean = 'and', $not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel whereNotIn($column, $values, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel whereNull($column, $boolean = 'and', $not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel whereNotNull($column, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel whereBetween($column, $values, $boolean = 'and', $not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel whereDate($column, $operator, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel whereMonth($column, $operator, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel whereYear($column, $operator, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel whereDay($column, $operator, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel orWhere($column, $operator = null, $value = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel orderBy($column, $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel orderByDesc($column)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel orderByRaw($sql, $bindings = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel groupBy($column)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel having($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel limit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel offset($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel take($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel skip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel with($relations)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel without($relations)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel withCount($relations)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel withSum($relation, $column)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel withAvg($relation, $column)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel withMax($relation, $column)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessageModel withMin($relation, $column)
 * @method static \Illuminate\Database\Eloquent\Collection|ChatMessageModel[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Collection|ChatMessageModel[] all($columns = ['*'])
 * @method static ChatMessageModel|null find($id, $columns = ['*'])
 * @method static ChatMessageModel|null findOrFail($id, $columns = ['*'])
 * @method static ChatMessageModel|null first($columns = ['*'])
 * @method static ChatMessageModel|null firstOrFail($columns = ['*'])
 * @method static ChatMessageModel|null firstOrNew(array $attributes = [])
 * @method static ChatMessageModel|null firstOrCreate(array $attributes = [], array $values = [])
 * @method static ChatMessageModel create(array $attributes)
 * @method static ChatMessageModel forceCreate(array $attributes)
 * @method static bool update(array $attributes)
 * @method static bool delete()
 * @method static int increment($column, $amount = 1, array $extra = [])
 * @method static int decrement($column, $amount = 1, array $extra = [])
 * @method static bool forceDelete()
 * @method static \Illuminate\Database\Query\Builder|ChatMessageModel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ChatMessageModel withoutTrashed()
 * @method static \Illuminate\Database\Query\Builder|ChatMessageModel onlyTrashed()
 * @method static \Illuminate\Database\Query\Builder|ChatMessageModel join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
 * @method static \Illuminate\Database\Query\Builder|ChatMessageModel leftJoin($table, $first, $operator = null, $second = null)
 * @method static \Illuminate\Database\Query\Builder|ChatMessageModel rightJoin($table, $first, $operator = null, $second = null)
 * @method static \Illuminate\Database\Query\Builder|ChatMessageModel select($columns = ['*'])
 * @method static \Illuminate\Database\Query\Builder|ChatMessageModel selectRaw($expression, $bindings = [])
 * @method static \Illuminate\Database\Query\Builder|ChatMessageModel addSelect($column)
 * @method static int count($columns = '*')
 * @method static mixed max($column)
 * @method static mixed min($column)
 * @method static mixed sum($column)
 * @method static mixed avg($column)
 * @method static mixed average($column)
 * @method static bool exists()
 * @method static bool doesntExist()
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
 * @method static \Illuminate\Contracts\Pagination\Paginator simplePaginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
 * @method static \Illuminate\Contracts\Pagination\CursorPaginator cursorPaginate($perPage = 15, $columns = ['*'], $cursorName = 'cursor', $cursor = null)
 *
 * @property string $id
 * @property string $chat_thread_id
 * @property string $sender_type
 * @property string $sender_id
 * @property string $content
 * @property bool $is_seen
 * @property \Illuminate\Support\Carbon|null $seen_at
 * @property array|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $created_by
 * @property ChatThreadModel $chatThread
 */
class ChatMessageModel extends Model
{
    use HasUUID;

    /**
     * Indicates if the IDs are auto-incrementing
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key ID
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Boot the model and register observers
     *
     * @return void
     */
    protected static function booted()
    {
        static::observe(ChangeModelLogObserver::class);
        static::observe(BaseObserver::class);
        parent::booted();
    }

    /**
     * The database table name
     *
     * @var string
     */
    protected $table = 'chat_messages';

    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = [
        'chat_thread_id',
        'sender_type',
        'sender_id',
        'content',
        'is_seen',
        'seen_at',
        'meta',
    ];

    /**
     * The attributes that should be cast to native types
     *
     * @var array
     */
    protected $casts = [
        'is_seen' => 'boolean',
        'seen_at' => 'datetime',
        'meta' => 'array',
        'id' => 'string'
    ];

    /**
     * Get the chat thread that this message belongs to
     *
     * This relationship returns the parent chat thread for this message.
     * Each message must belong to exactly one chat thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chatThread(): BelongsTo
    {
        return $this->belongsTo(ChatThreadModel::class, 'chat_thread_id');
    }

    /**
     * Mark this message as seen by the recipient
     *
     * Updates the is_seen flag to true and sets the seen_at timestamp
     * to the current time. This method is typically called when a user
     * views a message in the chat interface.
     *
     * @return void
     */
    public function markAsSeen(): void
    {
        $this->update([
            'is_seen' => true,
            'seen_at' => now(),
        ]);
    }
}
