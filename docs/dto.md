# Laravel DTO System Documentation

This document explains how to define, use, validate, and generate DTO (Data Transfer Object) classes in a Laravel application, including nested DTOs and automatic validation.

## What is a DTO?

A **DTO (Data Transfer Object)** is a simple object used to transfer structured data between layers of your application, especially useful when:

- You want to separate data shape from Eloquent models
- You need to validate incoming data before saving
- You use nested structured data (e.g., address inside a customer)
- You want IDE auto-completion for structured inputs

## Features

- Built-in Laravel-style validation
- Support for nested DTOs
- Auto-fill Eloquent models via DTO
- Safe property access with IDE support via PHPDoc
- Easily extendable for API Resources or Requests

## Directory Structure

```
app/
└── DTOs/
    ├── DTO.php              # Base abstract class
    ├── AddressDTO.php       # Example nested DTO
    └── CustomerDTO.php      # Example main DTO
```

## Usage Example

### 1. Define DTOs

#### AddressDTO.php

```php
/**
 * @property string $street
 * @property string $city
 * @property string $zip
 */
class AddressDTO extends DTO
{
    public function __construct()
    {
        $this->fields = [
            'street' => '',
            'city'   => '',
            'zip'    => '',
        ];
    }

    public function rules(): array
    {
        return [
            'street' => [$this->required(), $this->string(), $this->max(255)],
            'city'   => [$this->required(), $this->string()],
            'zip'    => [$this->required(), $this->string()],
        ];
    }
}
```

### CustomerDTO.php
```php
/**
 * @property string     $first_name
 * @property string     $last_name
 * @property array      $email
 * @property AddressDTO $address
 */
class CustomerDTO extends DTO
{
    public function __construct()
    {
        $this->fields = [
            'first_name' => '',
            'last_name' => '',
            'email'      => [],
            'address'    => new AddressDTO(),
        ];
    }

    public function rules(): array
    {
        return [
            'first_name' => [$this->required(), $this->string()],
            'last_name'  => [$this->required(), $this->string()],
            'email'      => [$this->required(), $this->array()],
            'address'    => [$this->required(), $this->array()],
        ];
    }
}
```

## 2. Use DTO in Controller

```php
$address = new AddressDTO();
$address->street = '123 Main St';
$address->city = 'New York';
$address->zip = '10001';

$customer = new CustomerDTO();
$customer->first_name = 'John';
$customer->last_name = 'Doe';
$customer->email = ['john@example.com'];
$customer->address = $address;

try {
    $success = $customer->save(\App\Models\Customer::class); // Save to Eloquent model
} catch (\Illuminate\Validation\ValidationException $e) {
    return response()->json(['errors' => $e->errors()], 422);
}
```

## How to Generate DTOs Automatically

You can use an Artisan command to generate DTOs from migration files.

Artisan Command (example):
```bash
php artisan make:dto-from-migration \
  --migration=database/migrations/2024_01_01_00000_create_customers_table.php \
  --output=app/DTOs \
  --name=CustomerDTO
```

This command will:

- ✅ Parse the migration
- ✅ Extract column names and types
- ✅ Add PHPDoc comments for IDE auto-completion
- ✅ Apply validation rules (based on column type)
- ✅ Create a ready-to-use DTO class

## Benefits

- Strong typing: Avoid passing raw arrays around your app.
- Code completion: Easily see properties and documentation in IDE.
- Single responsibility: Validation logic stays in DTO.
- Reusable: DTOs can be reused in controllers, services, API, and CLI.

## Good to Know

- Nested DTOs are validated recursively.
- All validation rules match Laravel's built-in validation.
- DTOs are not bound to HTTP requests, making them test-friendly.
- You can extend this base to support API Resources, Requests, etc.

## Future Extensions (Optional Ideas)

- Auto-generate DTOs from models
- Generate API Resource / OpenAPI from DTOs
- DTO-to-FormRequest converters
- Database introspection for existing tables

## Testing DTOs

You can unit test a DTO like this:

```php
public function test_valid_customer_dto()
{
    $dto = new CustomerDTO();
    $dto->first_name = 'Jane';
    $dto->last_name = 'Doe';
    $dto->email = ['jane@example.com'];
    $dto->address = new AddressDTO();
    $dto->address->street = '456 Broadway';
    $dto->address->city = 'NYC';
    $dto->address->zip = '10002';

    $dto->validate(); // throws no exception = valid
    $this->assertTrue(true);
}
```
