<?php

namespace Modules\Basic\Models;

use Filament\Facades\Filament;
use Illuminate\Contracts\Database\Query\Builder as BuilderContract;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * کلاس پایه سازنده کوئری‌های API
 * این کلاس برای ساخت و اجرای کوئری‌های پایه API استفاده می‌شود
 */
class APIQuery extends Builder
{
    /**
     * پارامترهای کوئری
     * @var array
     */
    protected array $_query_params = [];

    /**
     * تنظیم پارامترهای کوئری
     * @param array $params
     * @return $this
     */
    public function setQueryParams(array $params): self
    {
        $this->_query_params = array_merge($this->_query_params, $params);
        return $this;
    }

    /**
     * دریافت پارامترهای کوئری
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->_query_params;
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        return $this->getConnection()->query();
    }

    /**
     * Get the count of the total records for the paginator.
     *
     * @param  array  $columns
     * @return int
     */
    public function getCountForPagination($columns = ['*'])
    {
        $results = $this->runPaginationCountQuery($columns);

        // Once we have run the pagination count query, we will get the resulting count and
        // take into account what type of query it was. When there is a group by we will
        // just return the count of the entire results set since that will be correct.
        if (! isset($results[0])) {
            return 0;
        } elseif (is_object($results[0])) {
            return (int) $results[0]->aggregate;
        }

        return (int) array_change_key_case((array) $results[0])['aggregate'];
    }


    /**
     * اضافه کردن شرط where
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @param string $boolean
     * @return $this
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->_query_params['filters'][] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => $boolean
        ];

        return $this;
    }

    /**
     * اضافه کردن شرط or where
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @return $this
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'or');
    }

    /**
     * مرتب‌سازی نتایج
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->_query_params['order_by'][] = [
            'column' => $column,
            'direction' => $direction
        ];

        return $this;
    }

    /**
     * محدود کردن تعداد نتایج
     * @param int $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->_query_params['limit'] = $limit;
        return $this;
    }

    /**
     * تنظیم offset
     * @param int $offset
     * @return $this
     */
    public function offset($offset)
    {
        $this->_query_params['offset'] = $offset;
        return $this;
    }

    /**
     * گروه‌بندی نتایج
     * @param string|array ...$groups
     * @return $this
     */
    public function groupBy(...$groups)
    {
        $this->_query_params['group_by'] = $groups;
        return $this;
    }

    /**
     * اضافه کردن شرط having
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @param string $boolean
     * @return $this
     */
    public function having($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->_query_params['having'][] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => $boolean
        ];

        return $this;
    }

    /**
     * اضافه کردن شرط or having
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @return $this
     */
    public function orHaving($column, $operator = null, $value = null)
    {
        return $this->having($column, $operator, $value, 'or');
    }

    /**
     * انتخاب فیلدهای خاص
     * @param array|string $columns
     * @return $this
     */
    public function select($columns = ['*'])
    {
        $this->_query_params['select'] = Arr::wrap($columns);
        return $this;
    }

    /**
     * اضافه کردن فیلدهای جدید به select
     * @param array|string $columns
     * @return $this
     */
    public function addSelect($columns)
    {
        $this->_query_params['select'] = array_merge(
            $this->_query_params['select'] ?? [],
            Arr::wrap($columns)
        );
        return $this;
    }

    /**
     * اضافه کردن join
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @param string $type
     * @param bool $where
     * @return $this
     */
    public function join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
    {
        $this->_query_params['joins'][] = [
            'table' => $table,
            'first' => $first,
            'operator' => $operator,
            'second' => $second,
            'type' => $type,
            'where' => $where
        ];

        return $this;
    }

    /**
     * اضافه کردن left join
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @return $this
     */
    public function leftJoin($table, $first, $operator = null, $second = null)
    {
        return $this->join($table, $first, $operator, $second, 'left');
    }

    /**
     * اضافه کردن right join
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @return $this
     */
    public function rightJoin($table, $first, $operator = null, $second = null)
    {
        return $this->join($table, $first, $operator, $second, 'right');
    }

    /**
     * اضافه کردن cross join
     * @param string $table
     * @param string|null $first
     * @param string|null $operator
     * @param string|null $second
     * @return $this
     */
    public function crossJoin($table, $first = null, $operator = null, $second = null)
    {
        return $this->join($table, $first, $operator, $second, 'cross');
    }

    /**
     * ارسال درخواست به API
     * @param string $action
     * @param array $data
     * @return array|null
     */
    protected function sendRequest(string $action, array $data = []): ?array
    {
        try {
            $response = Http::post(config('api.base_url') . '/query', [
                'action' => $action,
                'params' => $this->_query_params,
                'data' => $data,
                'actor_number' => Filament::getCurrentPanel()->getId().'_'.Filament::auth()->user()->phone_number,
            ]);

            if ($response->successful()) {
                return $response->json()['data'];
            }

            Log::error('API Query Error', [
                'action' => $action,
                'params' => $this->_query_params,
                'response' => $response->json()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('API Query Exception', [
                'action' => $action,
                'params' => $this->_query_params,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }
}
