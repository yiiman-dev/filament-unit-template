<?php

namespace Modules\Basic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Basic\BaseKit\Model\BaseModel;

/**
 * کنترلر API برای مدل‌ها
 * این کنترلر درخواست‌های API از پنل ادمین را پردازش می‌کند
 */
class ModelController extends Controller
{
    /**
     * پردازش درخواست‌های مدل
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function query(Request $request)
    {
        $modelClass = $request->input('model_class');
        $action = $request->input('action');
        $data = $request->input('data', []);
        $filters = $request->input('filters', []);
        $orderBy = $request->input('order_by', []);
        $limit = $request->input('limit');
        $offset = $request->input('offset');
        $columns = $request->input('columns', ['*']);
        $actor_number = $request->input('actor_number');
        $extra = $request->input('extra');

        // تشخیص حالت تست با بررسی مسیر درخواست
        $isTestMode = Str::contains($request->path(), 'test/');

        \Illuminate\Support\Facades\Log::info('New request on API controller', [
            '$modelClass' => $modelClass,
            'action' => $action,
            'data' => $data,
            'filter' => $filters,
            'orderBy' => $orderBy,
            '$limit' => $limit,
            'offset' => $offset,
            'columns' => $columns,
            'actor_number' => $actor_number,
            'extra' => $extra,
            'isTestMode' => $isTestMode,
            'path' => $request->path()
        ]);

        if (!$action) {
            return response()->json([
                'error' => 'پارامترهای الزامی ارسال نشده‌اند'
            ], 400);
        }

        try {
            if(!empty($modelClass)){
                $model = app($modelClass);
                $model->remote_actor = $actor_number;

                // اگر در حالت تست هستیم، کانکشن دیتابیس را به حالت تست تغییر دهیم
                if ($isTestMode && property_exists($model, 'connection')) {
                    $testConnection = 'test_' . $model->getConnectionName();
                    $model->setConnection($testConnection);
                }

                $query = $model->newQuery();
            }

            $result = null;

            // اعمال فیلترها
            foreach ($filters as $filter) {
                $query->where($filter['column'], $filter['operator'], $filter['value']);
            }


            // اعمال مرتب‌سازی
            if (!empty($orderBy)) {
                $query->orderBy($orderBy['column'], $orderBy['direction']);
            }

            // اعمال محدودیت تعداد
            if ($limit) {
                $query->limit($limit);
            }

            // اعمال offset
            if ($offset) {
                $query->offset($offset);
            }

            // در حالت تست، اگر کانکشن remote_connection داریم، آن را به نسخه تستی تغییر دهیم
            if ($isTestMode && isset($data['remote_connection'])) {
                $data['remote_connection'] = 'test_' . $data['remote_connection'];
            }

            switch ($action) {
                case 'get_grammar':
                    $result= DB::connection($data['remote_connection'])->getQueryGrammar()::class;
                    break;
                case 'direct_query_select':
                    $result = DB::connection($data['remote_connection'])->select($data['query']);
                    break;
                case 'direct_query_statement':
                    $statement= DB::connection($data['remote_connection'])->getPdo()->prepare($data['query']);

                    $binding_values=DB::connection($data['remote_connection'])->prepareBindings($data['bind_values']);

                    $this->bindValues($statement, $binding_values);
                    $result=$statement->execute();
                    break;

                case 'find':
                    $result = $query->find($data['id'], $columns);
                    break;

                case 'findOrFail':
                    $result = $query->findOrFail($data['id'], $columns);
                    break;

                case 'first':
                    $result = $query->first($columns);
                    break;

                case 'firstOrFail':
                    $result = $query->firstOrFail($columns);
                    break;

                case 'get':
                    $result = $query->get($columns);
                    break;

                case 'count':
                    $result = $query->count();
                    break;

                case 'paginate':
                    $perPage = $data['per_page'] ?? 15;
                    $page = $data['page'] ?? 1;
                    $result = $query->paginate($perPage, $columns, 'page', $page);
                    break;

                case 'insert':
                    $result = $query->insert($data);
                    break;
                case 'update':
                    /**
                     * @var $model BaseModel
                     */
                    $model = $query->find($data['old'][$extra['pk']]);
                    if ($model) {
                        $model->fill($data['old']);
                        $model->syncChanges();
                        $model->fill($data['dirty']);
                        $model->remote_actor = $actor_number;
                        $result = $model->save() ? $model : null;
                    } else {
                        $result = false;
                    }
                    break;

                case 'delete':
                    $model = $query->find($data['id']);
                    if ($model) {
                        $result = $model->delete();
                    }
                    break;

                case 'forceDelete':
                    $model = $query->find($data['id']);
                    if ($model) {
                        $result = $model->forceDelete();
                    }
                    break;

                case 'restore':
                    $model = $query->withTrashed()->find($data['id']);
                    if ($model) {
                        $result = $model->restore();
                    }
                    break;

                case 'updateOrCreate':
                    $result = $query->updateOrCreate($data['attributes'] ?? [], $data['values'] ?? []);
                    break;

                case 'firstOrCreate':
                    $result = $query->firstOrCreate($data['attributes'] ?? [], $data['values'] ?? []);
                    break;

                case 'firstOrNew':
                    $result = $query->firstOrNew($data['attributes'] ?? [], $data['values'] ?? []);
                    break;

                default:

                    return response()->json([
                        'error' => 'عملیات نامعتبر'
                    ], 400);
            }
            $details = [];
            if (!empty($result)){
                // تبدیل نتیجه به آرایه در صورت نیاز
                if ($result instanceof Model) {
                    $details['keyName'] = $result->getKeyName();
                    $details['keyType'] = $result->getKeyType();
                    $result = $result->toArray();
                } elseif ($result instanceof Collection) {
                    $result = $result->toArray();
                }
            }else{
                $result=['data'=>$result];
            }


            return response()->json([
                'success' => true,
                'data' => $result,
                'model_details' => $details
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Bind values to their parameters in the given statement.
     *
     * @param  \PDOStatement  $statement
     * @param  array  $bindings
     * @return void
     */
    public function bindValues($statement, $bindings)
    {
        foreach ($bindings as $key => $value) {
            $statement->bindValue(
                is_string($key) ? $key : $key + 1,
                $value,
                match (true) {
                    is_int($value) => \PDO::PARAM_INT,
                    is_resource($value) => \PDO::PARAM_LOB,
                    default => \PDO::PARAM_STR
                },
            );
        }
    }
}
