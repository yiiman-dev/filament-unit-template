<?php

namespace Modules\Basic\Rules;


use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;
use Modules\Basic\Models\APIModel;

class RemoteUnique implements Rule
{
    protected APIModel|Model $table;
    protected string $column = '';
    protected string $label = '';
    protected $value = '';

    public function __construct(APIModel|Model $table, string $column, string $label)
    {
        $this->table = $table;
        $this->column = $column;
        $this->label = $label;
    }


    public function passes($attribute, $value)
    {
        $this->value = $value;
        /**
         * @var $model APIModel
         */
        $model = $this->table->where($this->column, $value)->first();
        if(!empty($model) && $exists=$model->exists && !$isDirty=$this->table->isDirty($this->column)){
            return true;
        }
        if (!empty($model)) {
            return false;
        } else {
            return true;
        }
    }

    public function message()
    {
//        return trans('basic:rules.remote_unique', ['label' => $this->label,'value'=>$this->value]);
        return str_replace(search: [':label', ':value'],
            replace: ['label' => $this->label, 'value' => $this->value],
            subject: ':label با مقدار :value قبل تر ثبت شده است.');
    }
}
