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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Basic\BaseKit\Observers\BaseObserver;
use Modules\Basic\Concerns\HasUUID;
use Units\ActLog\Admin\Observsers\ChangeModelLogObserver;
use Units\Chat\Common\Models\ChatMessageModel;

/**
 * ChatThreadModel
 *
 * Model representing a chat thread that can be associated with any model through polymorphic relationships.
 * Each chat thread contains multiple messages and can be linked to different entities in the system.
 *
 * Eloquent Model Methods:
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel wherePersona($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel whereIn($column, $values, $boolean = 'and', $not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel whereNotIn($column, $values, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel whereNull($column, $boolean = 'and', $not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel whereNotNull($column, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel whereBetween($column, $values, $boolean = 'and', $not = false)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel whereDate($column, $operator, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel whereMonth($column, $operator, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel whereYear($column, $operator, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel whereDay($column, $operator, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel orWhere($column, $operator = null, $value = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel orderBy($column, $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel orderByDesc($column)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel orderByRaw($sql, $bindings = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel groupBy($column)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel having($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel limit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel offset($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel take($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel skip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel with($relations)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel without($relations)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel withCount($relations)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel withSum($relation, $column)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel withAvg($relation, $column)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel withMax($relation, $column)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatThreadModel withMin($relation, $column)
 * @method static \Illuminate\Database\Eloquent\Collection|ChatThreadModel[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Collection|ChatThreadModel[] all($columns = ['*'])
 * @method static ChatThreadModel|null find($id, $columns = ['*'])
 * @method static ChatThreadModel|null findOrFail($id, $columns = ['*'])
 * @method static ChatThreadModel|null first($columns = ['*'])
 * @method static ChatThreadModel|null firstOrFail($columns = ['*'])
 * @method static ChatThreadModel|null firstOrNew(array $attributes = [])
 * @method static ChatThreadModel|null firstOrCreate(array $attributes = [], array $values = [])
 * @method static ChatThreadModel create(array $attributes)
 * @method static ChatThreadModel forceCreate(array $attributes)
 * @method static bool update(array $attributes)
 * @method static bool delete()
 * @method static int increment($column, $amount = 1, array $extra = [])
 * @method static int decrement($column, $amount = 1, array $extra = [])
 * @method static bool forceDelete()
 * @method static \Illuminate\Database\Query\Builder|ChatThreadModel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ChatThreadModel withoutTrashed()
 * @method static \Illuminate\Database\Query\Builder|ChatThreadModel onlyTrashed()
 * @method static \Illuminate\Database\Query\Builder|ChatThreadModel join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
 * @method static \Illuminate\Database\Query\Builder|ChatThreadModel leftJoin($table, $first, $operator = null, $second = null)
 * @method static \Illuminate\Database\Query\Builder|ChatThreadModel rightJoin($table, $first, $operator = null, $second = null)
 * @method static \Illuminate\Database\Query\Builder|ChatThreadModel select($columns = ['*'])
 * @method static \Illuminate\Database\Query\Builder|ChatThreadModel selectRaw($expression, $bindings = [])
 * @method static \Illuminate\Database\Query\Builder|ChatThreadModel addSelect($column)
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
 * @property string $model_type
 * @property string $model_id
 * @property string|null $title
 * @property string|null $description
 * @property array|null $meta
 * @property string $persona
 * @property string $tenant_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ChatThreadModel extends Model
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
    protected $table = 'chat_threads';

    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = [
        'model_type',
        'model_id',
        'title',
        'description',
        'meta',
    ];

    /**
     * The attributes that should be cast to native types
     *
     * @var array
     */
    protected $casts = [
        'meta' => 'array',
        'id' => 'string'
    ];

    /**
     * Get the model that the chat thread belongs to (polymorphic relationship)
     *
     * This relationship allows the chat thread to be associated with any model
     * in the system through the model_type and model_id columns.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model(): MorphTo
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    /**
     * Get the messages that belong to this chat thread
     *
     * This relationship returns all messages associated with this chat thread.
     * Messages are ordered by creation date (oldest first).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessageModel::class, 'chat_thread_id');
    }
}
