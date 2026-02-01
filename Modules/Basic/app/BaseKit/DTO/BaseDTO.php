<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          8/5/25, 12:12 PM
 */

namespace Modules\Basic\BaseKit\DTO;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

use Modules\Basic\Concerns\HasError;

use Modules\Basic\Concerns\InteractWithLog;

use phpDocumentor\Reflection\Types\Self_;

use function MongoDB\object;


/**
 * کلاس پایه برای DTOها
 * مسئول: نگهداری داده، انجام ولیدیشن، ذخیره در مدل Eloquent
 */
abstract class BaseDTO
{
    use HasError;
    use InteractWithLog;

    // فیلدهای DTO به صورت آرایه
    public array $fields = [];
    protected Model $model;
    public array $dependencies = [];

    public static function make()
    {
        return new static();
    }

    /**
     * Load properties from a given model or object into the DTO.
     *
     * Only properties that exist in the DTO will be filled.
     *
     * @param object $model The model or object to load properties from
     * @return static
     */
    public static function loadFromModel(array|Model|Collection|null $model): self
    {
        $dto = new static();
        switch (true) {
            case $model == null:
                return $dto;
            case $model instanceof Model:
                $model = (object)$model->toArray();
                break;
            case $model instanceof Collection:
                $model = (object)$model->first();
                break;
            case is_array($model):
                $model = (object)$model;
                break;
        }


        foreach (get_object_vars($model) as $key => $value) {
            // Check if the DTO has the property
            if ($dto->hasAttribute($key)) {
                $dto->$key = $value;
            }
        }

        return $dto;
    }

    /**
     * Load properties from a given collection of models into an array of DTOs.
     *
     * Each model in the collection will be converted to a DTO instance.
     *
     * @param Collection $models The collection of models to load properties from
     * @return array<self>
     */
    public static function loadFromModelCollection(array|Collection|null $models): array
    {
        $dtos = [];
        if (empty($models)) {
            return $dtos;
        }
        if ($models instanceof Model) {
            $models[] = $models->toArray();
        }
        if ($models instanceof Collection) {
            $models = $models->toArray();
        }
        foreach ($models as $model) {
            $key = null;
            $dto = static::loadFromModel($model);
            if (!empty($dto->getAttribute('id'))) {
                $key = $dto->id;
            }
            if (!empty($key)) {
                $dtos[$key] = $dto;
            } else {
                $dtos[] = $dto;
            }
        }
        if (empty($dtos)) {
            return [];
        }
        return $dtos;
    }

    public function hasAttribute($attribute)
    {
        if (isset($this->fields[$attribute])) {
            return true;
        }
        return false;
    }

    public function getAttribute($attribute_name)
    {
        return $this->{$attribute_name};
    }

    public function setAttribute($attribute,$value)
    {
        $this->{$attribute}=$value;
    }

    /**
     * Define the required DTO dependencies for the current DTO.
     *
     * Should be overridden in child DTO classes to enforce injection of nested DTOs.
     *
     * @return array<string, class-string>  [property_name => class_name]
     */
    protected function requiredDTOs(): array
    {
        return [];
    }

    public function generateUUID($attribute = 'id'): void
    {
        $this->{$attribute} = Str::uuid()->toString();
    }

    // تعریف رول‌های اعتبارسنجی
    abstract public function rules(): array;

    /**
     * تبدیل DTO به آرایه (برای پر کردن مدل)
     * اگر فیلدی از نوع DTO باشد، خودش را به آرایه تبدیل می‌کند.
     */
    public function toArray(): array
    {
        $fields = collect($this->fields)->map(function ($value) {
            return $value instanceof self ? $value->toArray() : $value;
        })->toArray();
        $dependencies = collect($this->dependencies)->map(function ($value) {
            switch (true) {
                case $value instanceof self :
                    return $value->toArray();
                case is_array($value):
                    return collect($value)->map(function ($dependency_value) {
                        return $dependency_value->toArray();
                    })->toArray();
            }
        })->toArray();

        $output = array_merge_recursive($fields, $dependencies);
        return $output;
    }

    public function toCollection(): Collection
    {
        return Collection::make($this->toArray());
    }

    /**
     * مقداردهی به پراپرتی‌ها به صورت داینامیک
     */
    public function __set($name, $value)
    {
        if (!array_key_exists($name, $this->fields) && !isset($this->dependencies[$name])) {
            throw new \Exception("Property {$name} does not exist in DTO.");
        }
        if (isset($this->dependencies[$name])) {
            $this->dependencies[$name] = $value;
        } else {
            $this->fields[$name] = $value;
        }
    }

    /**
     * گرفتن مقدار پراپرتی‌ها به صورت داینامیک
     */
    public function __get($name)
    {
        // Check if the property exists in fields
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        }

        // Check if the property exists in dependencies
        if (isset($this->dependencies[$name])) {
            return $this->dependencies[$name];
        }

        // If property doesn't exist, return null or throw an exception
        return null;
    }

    /**
     * انجام اعتبارسنجی روی فیلدها
     * اگر مقدار فیلدی از نوع DTO بود، اعتبارسنجی آن نیز انجام می‌شود
     */
    public function validate(): bool
    {
        $is_ok = true;
        foreach ($this->fields as $value) {
            if ($value instanceof BaseDTO) {
                if (!$value->validate()) {
                    $this->addError($value->errors);
                    $is_ok = false;
                }
            }
        }

        $validator = Validator::make($this->toArray(), $this->rules());
        if ($validator->fails()) {
            $this->addError($validator->errors()->toArray());
            return false;
        }
        return $is_ok;
    }

    /**
     * ذخیره DTO در مدل Eloquent
     * ابتدا اعتبارسنجی انجام می‌دهد و سپس مقادیر را در مدل می‌ریزد
     * @throws \Exception
     */
    public function save(string $modelClass): bool
    {
        $this->validate();

        if (!class_exists($modelClass)) {
            throw new \Exception("Model $modelClass not found.");
        }

        $model = new $modelClass();
        foreach ($this->toArray() as $key => $value) {
            if ($value instanceof BaseDTO) {
                continue;
            }
            $model->$key = $value;
        }
        $saved = $model->save();
        if ($saved) {
            $this->model = $model;
        }
        return $saved;
    }

    public function getRecord(): Model|null
    {
        return $this->model;
    }

    public function getModel(): Model|null
    {
        return $this->model;
    }

    public function setModel(Model $model): self
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Required validation rule - Makes the field required
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'name' => $this->required(),
     * ];
     * ```
     *
     * @return string
     */
    public function required(): string
    {
        return 'required';
    }

    /**
     * Length validation rule - Sets the exact length for a string field
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'code' => $this->length(6),
     * ];
     * ```
     *
     * @param int $length The exact length required
     * @return string
     */
    public function length($length)
    {
        return 'size:' . $length;
    }

    /**
     * String validation rule - Validates that the field is a string
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'title' => $this->string(),
     * ];
     * ```
     *
     * @return string
     */
    public function string(): string
    {
        return 'string';
    }

    /**
     * Text validation rule - Validates that the field is a string (alias for string)
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'description' => $this->text(),
     * ];
     * ```
     *
     * @return string
     */
    public function text(): string
    {
        return 'string';
    }

    /**
     * Boolean validation rule - Validates that the field is a boolean value
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'is_active' => $this->boolean(),
     * ];
     * ```
     *
     * @return string
     */
    public function boolean()
    {
        return 'boolean';
    }

    /**
     * Integer validation rule - Validates that the field is an integer
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'age' => $this->integer(),
     * ];
     * ```
     *
     * @return string
     */
    public function integer(): string
    {
        return 'integer';
    }

    /**
     * Email validation rule - Validates that the field is a valid email address
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'email' => $this->email(),
     * ];
     * ```
     *
     * @return string
     */
    public function email(): string
    {
        return 'email';
    }

    /**
     * Max validation rule - Sets the maximum value for numeric fields or maximum length for strings
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'age' => $this->max(120),
     *     'name' => $this->max(50),
     * ];
     * ```
     *
     * @param int $value The maximum value
     * @return string
     */
    public function max(int $value): string
    {
        return 'max:' . $value;
    }

    /**
     * Array validation rule - Validates that the field is an array
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'tags' => $this->array(),
     * ];
     * ```
     *
     * @return string
     */
    public function array(): string
    {
        return 'array';
    }

    /**
     * Nullable validation rule - Allows the field to be null
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'middle_name' => $this->nullable(),
     * ];
     * ```
     *
     * @return string
     */
    public function nullable(): string
    {
        return 'nullable';
    }

    /**
     * Unsigned big integer validation rule - Validates that the field is an unsigned big integer
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'user_id' => $this->unsignedBigInteger(),
     * ];
     * ```
     *
     * @return string
     */
    public function unsignedBigInteger(): string
    {
        return 'integer';
    }

    /**
     * UUID validation rule - Validates that the field is a valid UUID string
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'uuid' => $this->uuid(),
     * ];
     * ```
     *
     * @return string
     */
    public function uuid(): string
    {
        return 'string';
    }

    /**
     * Required without validation rule - Makes the field required when other fields are not present
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'phone' => $this->require_without('email'),
     *     'email' => $this->require_without('phone'),
     * ];
     * ```
     *
     * @param array|string $fields The fields to check for absence
     * @return string|void
     */
    public function require_without(array|string $fields)
    {
        switch (true) {
            case is_array($fields):
                return 'required_without:' . implode(',', $fields);
            case is_string($fields):
                return 'required_without:' . $fields;
        }
    }

    /**
     * Min validation rule - Sets the minimum value for numeric fields or minimum length for strings
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'age' => $this->min(0),
     *     'name' => $this->min(2),
     * ];
     * ```
     *
     * @param int $value The minimum value
     * @return string
     */
    public function min(int $value): string
    {
        return 'min:' . $value;
    }

    /**
     * Between validation rule - Sets a range for numeric fields or string length
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'age' => $this->between(18, 120),
     *     'name' => $this->between(2, 50),
     * ];
     * ```
     *
     * @param int $min The minimum value
     * @param int $max The maximum value
     * @return string
     */
    public function between(int $min, int $max): string
    {
        return "between:{$min},{$max}";
    }

    /**
     * In validation rule - Validates that the field value is in a given list
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'status' => $this->in(['active', 'inactive', 'pending']),
     * ];
     * ```
     *
     * @param array $values The list of allowed values
     * @return string
     */
    public function in(array $values): string
    {
        return 'in:' . implode(',', $values);
    }

    /**
     * Not in validation rule - Validates that the field value is not in a given list
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'status' => $this->notIn(['banned', 'deleted']),
     * ];
     * ```
     *
     * @param array $values The list of forbidden values
     * @return string
     */
    public function notIn(array $values): string
    {
        return 'not_in:' . implode(',', $values);
    }

    /**
     * Required with validation rule - Makes the field required when other fields are present
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'phone' => $this->requiredWith('email'),
     * ];
     * ```
     *
     * @param array|string $fields The fields to check for presence
     * @return string|void
     */
    public function requiredWith(array|string $fields)
    {
        switch (true) {
            case is_array($fields):
                return 'required_with:' . implode(',', $fields);
            case is_string($fields):
                return 'required_with:' . $fields;
        }
    }

    /**
     * Required if validation rule - Makes the field required when a condition is met
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'phone' => $this->requiredIf('is_active', true),
     * ];
     * ```
     *
     * @param string $attribute The attribute to check
     * @param mixed $value The value to compare against
     * @return string
     */
    public function requiredIf(string $attribute, mixed $value): string
    {
        return "required_if:{$attribute},{$value}";
    }

    /**
     * Required unless validation rule - Makes the field required unless a condition is met
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'phone' => $this->requiredUnless('is_active', false),
     * ];
     * ```
     *
     * @param string $attribute The attribute to check
     * @param mixed $value The value to compare against
     * @return string
     */
    public function requiredUnless(string $attribute, mixed $value): string
    {
        return "required_unless:{$attribute},{$value}";
    }

    /**
     * Unique validation rule - Validates that the field value is unique in a database table
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'email' => $this->unique('users'),
     * ];
     * ```
     *
     * @param string $table The database table name
     * @param string|null $column The column to check (defaults to field name)
     * @return string
     */
    public function unique(string $table, ?string $column = null): string
    {
        return "unique:{$table}" . ($column ? ",{$column}" : '');
    }

    /**
     * Different validation rule - Validates that the field value is different from another field
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'password_confirmation' => $this->different('password'),
     * ];
     * ```
     *
     * @param string $field The field to compare against
     * @return string
     */
    public function different(string $field): string
    {
        return "different:{$field}";
    }

    /**
     * Same validation rule - Validates that the field value is the same as another field
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'password_confirmation' => $this->same('password'),
     * ];
     * ```
     *
     * @param string $field The field to compare against
     * @return string
     */
    public function same(string $field): string
    {
        return "same:{$field}";
    }

    /**
     * Date validation rule - Validates that the field is a valid date
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'birth_date' => $this->date(),
     * ];
     * ```
     *
     * @return string
     */
    public function date(): string
    {
        return 'date';
    }

    /**
     * Date format validation rule - Validates that the field matches a specific date format
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'birth_date' => $this->dateFormat('Y-m-d'),
     * ];
     * ```
     *
     * @param string $format The date format to validate against
     * @return string
     */
    public function dateFormat(string $format): string
    {
        return "date_format:{$format}";
    }

    /**
     * After validation rule - Validates that the field date is after a given date
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'end_date' => $this->after('2023-01-01'),
     * ];
     * ```
     *
     * @param string $date The date to compare against
     * @return string
     */
    public function after(string $date): string
    {
        return "after:{$date}";
    }

    /**
     * Before validation rule - Validates that the field date is before a given date
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'start_date' => $this->before('2025-01-01'),
     * ];
     * ```
     *
     * @param string $date The date to compare against
     * @return string
     */
    public function before(string $date): string
    {
        return "before:{$date}";
    }

    /**
     * Required when validation rule - Makes the field required when another field has a specific value
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'phone' => $this->requiredWhen('is_active', true),
     * ];
     * ```
     *
     * @param string $attribute The attribute to check
     * @param mixed $value The value to compare against
     * @return string
     */
    public function requiredWhen(string $attribute, mixed $value): string
    {
        return "required_when:{$attribute},{$value}";
    }

    /**
     * Required when not validation rule - Makes the field required when another field does NOT have a specific value
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'phone' => $this->requiredWhenNot('is_active', true),
     * ];
     * ```
     *
     * @param string $attribute The attribute to check
     * @param mixed $value The value to compare against
     * @return string
     */
    public function requiredWhenNot(string $attribute, mixed $value): string
    {
        return "required_when_not:{$attribute},{$value}";
    }

    /**
     * Alpha validation rule - Validates that the field contains only alphabetic characters
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'name' => $this->alpha(),
     * ];
     * ```
     *
     * @return string
     */
    public function alpha(): string
    {
        return 'alpha';
    }

    /**
     * Alpha numeric validation rule - Validates that the field contains only alphanumeric characters
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'username' => $this->alphaNum(),
     * ];
     * ```
     *
     * @return string
     */
    public function alphaNum(): string
    {
        return 'alpha_num';
    }

    /**
     * Alpha dash validation rule - Validates that the field contains only alphanumeric characters, dashes, and underscores
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'slug' => $this->alphaDash(),
     * ];
     * ```
     *
     * @return string
     */
    public function alphaDash(): string
    {
        return 'alpha_dash';
    }

    /**
     * Numeric validation rule - Validates that the field contains only numeric characters
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'price' => $this->numeric(),
     * ];
     * ```
     *
     * @return string
     */
    public function numeric(): string
    {
        return 'numeric';
    }

    /**
     * Digits validation rule - Validates that the field contains exactly a specified number of digits
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'phone' => $this->digits(10),
     * ];
     * ```
     *
     * @param int $digits The exact number of digits required
     * @return string
     */
    public function digits(int $digits): string
    {
        return "digits:{$digits}";
    }

    /**
     * Digits between validation rule - Validates that the field contains a number of digits within a range
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'phone' => $this->digitsBetween(10, 15),
     * ];
     * ```
     *
     * @param int $min The minimum number of digits
     * @param int $max The maximum number of digits
     * @return string
     */
    public function digitsBetween(int $min, int $max): string
    {
        return "digits_between:{$min},{$max}";
    }

    /**
     * IP address validation rule - Validates that the field contains a valid IP address
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'ip_address' => $this->ip(),
     * ];
     * ```
     *
     * @return string
     */
    public function ip(): string
    {
        return 'ip';
    }

    /**
     * IPv4 address validation rule - Validates that the field contains a valid IPv4 address
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'ip_address' => $this->ipv4(),
     * ];
     * ```
     *
     * @return string
     */
    public function ipv4(): string
    {
        return 'ipv4';
    }

    /**
     * IPv6 address validation rule - Validates that the field contains a valid IPv6 address
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'ip_address' => $this->ipv6(),
     * ];
     * ```
     *
     * @return string
     */
    public function ipv6(): string
    {
        return 'ipv6';
    }

    /**
     * URL validation rule - Validates that the field contains a valid URL
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'website' => $this->url(),
     * ];
     * ```
     *
     * @return string
     */
    public function url(): string
    {
        return 'url';
    }

    /**
     * Active URL validation rule - Validates that the field contains a valid URL that is accessible
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'website' => $this->activeUrl(),
     * ];
     * ```
     *
     * @return string
     */
    public function activeUrl(): string
    {
        return 'active_url';
    }

    /**
     * Image validation rule - Validates that the field contains an image file
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'avatar' => $this->image(),
     * ];
     * ```
     *
     * @return string
     */
    public function image(): string
    {
        return 'image';
    }

    /**
     * Image dimensions validation rule - Validates that the field contains an image with specific dimensions
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'avatar' => $this->dimensions('width=200,height=200'),
     * ];
     * ```
     *
     * @param string $dimensions The dimensions to validate against
     * @return string
     */
    public function dimensions(string $dimensions): string
    {
        return "dimensions:{$dimensions}";
    }

    /**
     * MIME types validation rule - Validates that the field contains a file with specific MIME types
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'document' => $this->mimes('pdf,doc,docx'),
     * ];
     * ```
     *
     * @param string $types The comma-separated list of allowed MIME types
     * @return string
     */
    public function mimes(string $types): string
    {
        return "mimes:{$types}";
    }

    /**
     * File size validation rule - Validates that the field contains a file with specific size limits
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'document' => $this->maxFileSize(2048), // 2MB
     * ];
     * ```
     *
     * @param int $kilobytes The maximum file size in kilobytes
     * @return string
     */
    public function maxFileSize(int $kilobytes): string
    {
        return "max:{$kilobytes}";
    }

    /**
     * File size validation rule - Validates that the field contains a file with specific size limits
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'document' => $this->minFileSize(1024), // 1MB
     * ];
     * ```
     *
     * @param int $kilobytes The minimum file size in kilobytes
     * @return string
     */
    public function minFileSize(int $kilobytes): string
    {
        return "min:{$kilobytes}";
    }

    /**
     * Boolean validation rule - Validates that the field is a boolean value (true/false)
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'is_published' => $this->bool(),
     * ];
     * ```
     *
     * @return string
     */
    public function bool(): string
    {
        return 'boolean';
    }

    /**
     * JSON validation rule - Validates that the field contains valid JSON
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'data' => $this->json(),
     * ];
     * ```
     *
     * @return string
     */
    public function json(): string
    {
        return 'json';
    }

    /**
     * Present validation rule - Validates that the field is present in the input (even if null)
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'optional_field' => $this->present(),
     * ];
     * ```
     *
     * @return string
     */
    public function present(): string
    {
        return 'present';
    }

    /**
     * Required without all validation rule - Makes the field required when all specified fields are not present
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'phone' => $this->requiredWithoutAll(['email', 'address']),
     * ];
     * ```
     *
     * @param array $fields The fields to check for absence
     * @return string
     */
    public function requiredWithoutAll(array $fields): string
    {
        return 'required_without_all:' . implode(',', $fields);
    }

    /**
     * Required with all validation rule - Makes the field required when all specified fields are present
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'phone' => $this->requiredWithAll(['email', 'address']),
     * ];
     * ```
     *
     * @param array $fields The fields to check for presence
     * @return string
     */
    public function requiredWithAll(array $fields): string
    {
        return 'required_with_all:' . implode(',', $fields);
    }

    /**
     * Required without any validation rule - Makes the field required when none of the specified fields are present
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'phone' => $this->requiredWithoutAny(['email', 'address']),
     * ];
     * ```
     *
     * @param array $fields The fields to check for absence
     * @return string
     */
    public function requiredWithoutAny(array $fields): string
    {
        return 'required_without_any:' . implode(',', $fields);
    }

    /**
     * Required with any validation rule - Makes the field required when any of the specified fields are present
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'phone' => $this->requiredWithAny(['email', 'address']),
     * ];
     * ```
     *
     * @param array $fields The fields to check for presence
     * @return string
     */
    public function requiredWithAny(array $fields): string
    {
        return 'required_with_any:' . implode(',', $fields);
    }

    /**
     * Required if condition validation rule - Makes the field required when a specific condition is met
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'phone' => $this->requiredIf('is_active', true),
     * ];
     * ```
     *
     * @param string $attribute The attribute to check
     * @param mixed $value The value to compare against
     * @return string
     */
    public function requiredIfCondition(string $attribute, mixed $value): string
    {
        return "required_if:{$attribute},{$value}";
    }

    /**
     * Required unless condition validation rule - Makes the field required unless a specific condition is met
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'phone' => $this->requiredUnless('is_active', false),
     * ];
     * ```
     *
     * @param string $attribute The attribute to check
     * @param mixed $value The value to compare against
     * @return string
     */
    public function requiredUnlessCondition(string $attribute, mixed $value): string
    {
        return "required_unless:{$attribute},{$value}";
    }

    /**
     * Required when not condition validation rule - Makes the field required when a specific condition is NOT met
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'phone' => $this->requiredWhenNot('is_active', true),
     * ];
     * ```
     *
     * @param string $attribute The attribute to check
     * @param mixed $value The value to compare against
     * @return string
     */
    public function requiredWhenNotCondition(string $attribute, mixed $value): string
    {
        return "required_when_not:{$attribute},{$value}";
    }

    /**
     * Required when condition validation rule - Makes the field required when a specific condition is met
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'phone' => $this->requiredWhen('is_active', true),
     * ];
     * ```
     *
     * @param string $attribute The attribute to check
     * @param mixed $value The value to compare against
     * @return string
     */
    public function requiredWhenCondition(string $attribute, mixed $value): string
    {
        return "required_when:{$attribute},{$value}";
    }

    /**
     * Required when not condition validation rule - Makes the field required when a specific condition is NOT met
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'phone' => $this->requiredWhenNot('is_active', true),
     * ];
     * ```
     *
     * @param string $attribute The attribute to check
     * @param mixed $value The value to compare against
     * @return string
     */
    public function requiredWhenNotCondition2(string $attribute, mixed $value): string
    {
        return "required_when_not:{$attribute},{$value}";
    }

    /**
     * Required when any condition validation rule - Makes the field required when any of the specified conditions are met
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'phone' => $this->requiredWhenAny(['is_active', 'is_verified'], true),
     * ];
     * ```
     *
     * @param array $attributes The attributes to check
     * @param mixed $value The value to compare against
     * @return string
     */
    public function requiredWhenAny(array $attributes, mixed $value): string
    {
        return "required_when_any:" . implode(',', $attributes) . ",{$value}";
    }

    /**
     * Required when all condition validation rule - Makes the field required when all of the specified conditions are met
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'phone' => $this->requiredWhenAll(['is_active', 'is_verified'], true),
     * ];
     * ```
     *
     * @param array $attributes The attributes to check
     * @param mixed $value The value to compare against
     * @return string
     */
    public function requiredWhenAll(array $attributes, mixed $value): string
    {
        return "required_when_all:" . implode(',', $attributes) . ",{$value}";
    }

    /**
     * Required when some condition validation rule - Makes the field required when some of the specified conditions are met
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'phone' => $this->requiredWhenSome(['is_active', 'is_verified'], true),
     * ];
     * ```
     *
     * @param array $attributes The attributes to check
     * @param mixed $value The value to compare against
     * @return string
     */
    public function requiredWhenSome(array $attributes, mixed $value): string
    {
        return "required_when_some:" . implode(',', $attributes) . ",{$value}";
    }

    /**
     * Required when not some condition validation rule - Makes the field required when some of the specified conditions are NOT met
     *
     * Example:
     * ```php
     * $dto->rules = [
     *     'phone' => $this->requiredWhenNotSome(['is_active', 'is_verified'], true),
     * ];
     * ```
     *
     * @param array $attributes The attributes to check
     * @param mixed $value The value to compare against
     * @return string
     */
    public function requiredWhenNotSome(array $attributes, mixed $value): string
    {
        return "required_when_not_some:" . implode(',', $attributes) . ",{$value}";
    }

    /**
     * Append a value to an array dependency property
     *
     * @param string $name The name of the dependency array property
     * @param self|array $value The value to append
     * @return void
     */
    public function appendTo(string $name, self|array $value): void
    {
        if (!isset($this->dependencies[$name])) {
            throw new \Exception("Dependency property {$name} does not exist in DTO.");
        }
        if ($value instanceof self) {
            if ($value->hasAttribute('id')) {
                $this->dependencies[$name][$value->id] = $value;
            } else {
                $this->dependencies[$name][] = $value;
            }
        } else {
            if (is_array($value)) {
                $this->dependencies[$name] = $value;
            } else {
                throw new \Exception("Dependency property {$name} is not an array or DTO.");
            }
        }
    }

    /**
     * Get all values of an array dependency property
     *
     * @param string $name The name of the dependency array property
     * @return array
     */
    public function getArrayDependency(string $name): array
    {
        if (!isset($this->dependencies[$name])) {
            throw new \Exception("Dependency property {$name} does not exist in DTO.");
        }

        if (!is_array($this->dependencies[$name])) {
            throw new \Exception("Dependency property {$name} is not an array.");
        }

        return $this->dependencies[$name];
    }

    /**
     * Get validation errors as string
     * Error structure: ErrorService objects with data and message
     *
     * @return string
     */
    public function getValidationErrorsAsString(): string
    {
        $errorMessages = [];

        if (isset($this->errors) && is_array($this->errors)) {
            foreach ($this->errors as $errorService) {
                if ($errorService instanceof \Modules\Basic\BaseKit\ErrorService) {
                    $data = $errorService->getData();
                    $message = $errorService->getMessage();

                    if (!empty($data) && is_array($data)) {
                        foreach ($data as $attribute => $attributeErrors) {
                            if (is_array($attributeErrors)) {
                                foreach ($attributeErrors as $attributeError) {
                                    $errorMessages[] = $attribute . ': ' . $attributeError;
                                }
                            } else {
                                $errorMessages[] = $attribute . ': ' . $attributeErrors;
                            }
                        }
                    } elseif (!empty($message)) {
                        $errorMessages[] = $message;
                    } elseif (!empty($data) && is_string($data)) {
                        $errorMessages[] = $data;
                    }
                }
            }
        }

        return implode(', ', $errorMessages);
    }
}
