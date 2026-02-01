<?php

namespace Modules\Basic\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Basic\Models\Concens\ModelApiInterface;


/**
 * کلاس پایه برای مدل‌های API
 * این کلاس به جای اتصال مستقیم به دیتابیس، از طریق وب‌سرویس با پنل‌های دیگر ارتباط برقرار می‌کند
 */
abstract class APIModel extends Model implements ModelApiInterface
{
    /**
     * کلاس سازنده کوئری
     * @var string
     */
    protected static string $builder = APIQueryBuilder::class;

    /**
     * آدرس پایه API
     * @var string
     */
    protected $apiBaseUrl;

    /**
     * نام مدل در پنل مقصد
     * @var string
     */
    public $remoteModel;

    /**
     * زمان انقضای کش (به ثانیه)
     * @var int
     */
    protected $cacheTtl = 60;

    /**
     * سازنده کلاس
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->apiBaseUrl = $this->get_remote_base_api();
    }


    public function getTable()
    {
        return app($this->remoteModel)->getTable();
    }

    /**
     * تبدیل مدل به آرایه
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * ارسال درخواست به API
     * @param string $action
     * @param array $data
     * @return array|null
     */
    protected function sendRequest(string $action, array $data = [], $extra = []): ?array
    {
        $cacheKey = "api_model_{$this->getTable()}_{$action}_" . md5(json_encode($data));

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($action, $data, $extra) {

            $url = "{$this->apiBaseUrl}/models";
            $response = Http::post($url, [
                'model_class' => $this->remoteModel,
                'action' => $action,
                'data' => $data,
                'actor_number' => Filament::getCurrentPanel()->getId().'_'.Filament::auth()->user()->phone_number,
                'extra' => $extra
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('API Model call error:' . get_called_class() . ' => ' . $this->remoteModel, [
                'response_code' => $response->getStatusCode(),
                'response_body' => $response->dump()
            ]);
            return null;
        });
    }

    /**
     * دریافت رکورد با ID مشخص
     * @param int $id
     * @return static|null
     */
    public static function find($id)
    {
        $instance = new static;
        $data = $instance->sendRequest('find', ['id' => $id]);

        if ($data) {
            return $instance->newFromBuilder($data);
        }

        return null;
    }

    /**
     * دریافت تمام رکوردها
     * @param string[] $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function all($columns = ['*'])
    {
        $instance = new static;
        $data = $instance->sendRequest('all');

        if ($data) {
            return $instance->newCollection($data);
        }

        return $instance->newCollection();
    }

    /**
     * ذخیره مدل
     * @return bool
     */
    public function save(array $options = [])
    {
        $this->mergeAttributesFromCachedCasts();

        $query = $this->newModelQuery();

        // If the "saving" event returns false we'll bail out of the save and return
        // false, indicating that the save failed. This provides a chance for any
        // listeners to cancel save operations if validations fail or whatever.
        if ($this->fireModelEvent('saving') === false) {
            return false;
        }

        // If the model already exists in the database we can just update our record
        // that is already in this database using the current IDs in this "where"
        // clause to only update this model. Otherwise, we'll just insert them.
        if ($this->exists) {
            $saved = $this->isDirty() ?
                $this->performUpdate($query) : true;
        }

        // If the model is brand new, we'll insert it into our database and set the
        // ID attribute on the model to the value of the newly inserted row's ID
        // which is typically an auto-increment value managed by the database.
        else {
            $saved = $this->performInsert($query);
        }

        // If the model is successfully saved, we need to do a few more things once
        // that is done. We will call the "saved" method here to run any actions
        // we need to happen after a model gets successfully saved right here.
        if ($saved) {
            $this->finishSave($options);
        }

        return $saved;
    }


    /**
     * Insert new records into the database.
     *
     * @return bool
     */
    public function insert(array $values)
    {
        // Since every insert gets treated like a batch insert, we will make sure the
        // bindings are structured in a way that is convenient when building these
        // inserts statements by verifying these elements are actually an array.
        if (empty($values)) {
            return true;
        }

        if (!is_array(reset($values))) {
            $values = [$values];
        }

        // Here, we will sort the insert keys for every record so that each insert is
        // in the same order for the record. We need to make sure this is the case
        // so there are not any errors or problems when inserting these records.
        else {
            foreach ($values as $key => $value) {
                ksort($value);

                $values[$key] = $value;
            }
        }

        $this->applyBeforeQueryCallbacks();

        // Finally, we will run this query against the database connection and return
        // the results. We will need to also flatten these bindings before running
        // the query so they are all in one huge, flattened array for execution.
        $data = $this->sendRequest('save', $this->attributes);

        if ($data) {
            $this->fill($data);
            return true;
        }
        return false;
    }


    /**
     * به‌روزرسانی مدل
     * @return bool
     */
    public function update2(array $attributes = [], array $options = [])
    {
        $this->fill($attributes);
        $data=[
            'old' => $this->attributes,
            'dirty' => $this->getChanges()
        ];
        $data = $this->sendRequest('update', $data, [
            'pk' => $this->primaryKey
        ]);

        if ($data) {
            $this->fill($data);
            return true;
        }

        return false;
    }

    /**
     * حذف مدل
     * @return bool|null
     */
    public function delete()
    {
        $data = $this->sendRequest('delete', ['id' => $this->id]);

        if ($data) {
            return true;
        }

        return false;
    }
}
