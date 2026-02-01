# BaseDTO Documentation

## Overview

The `BaseDTO` class is a foundational Data Transfer Object (DTO) implementation that provides a robust framework for data validation, transformation, and model persistence in Laravel applications. It serves as the base class for all DTOs in the system, offering standardized patterns for data handling across different units.

## Key Features

### 1. Data Validation
- **Automatic Validation**: Built-in validation using Laravel's Validator facade
- **Nested DTO Support**: Validates nested DTO objects recursively
- **Custom Rules**: Abstract `rules()` method for defining validation rules
- **Error Collection**: Centralized error handling with detailed error messages

### 2. Model Integration
- **Eloquent Model Support**: Direct integration with Laravel Eloquent models
- **Dynamic Property Assignment**: Automatic property mapping from models to DTOs
- **Persistence Layer**: Save DTO data directly to database models
- **Collection Support**: Handle Laravel Collections and arrays

### 3. Type Safety
- **Property Type Checking**: Runtime validation of property existence
- **Dynamic Property Access**: Magic methods for getter/setter operations
- **Dependency Injection**: Support for nested DTO dependencies
- **UUID Generation**: Built-in UUID generation for primary keys

## Class Structure

### Core Properties

```php
protected array $fields = [];           // DTO field definitions
protected Model $model;                 // Associated Eloquent model
protected array $dependencies = [];     // Nested DTO dependencies
```

### Key Methods

#### Static Methods

**`make()`**
- Creates a new instance of the DTO
- Factory pattern for instantiation

**`loadFromModel(array|Model|Collection|null $model)`**
- Loads data from various sources into the DTO
- Supports Eloquent models, collections, arrays, and null values
- Automatically converts data structures to object format

#### Instance Methods

**`hasAttribute($attribute)`**
- Checks if a property exists in the DTO
- Validates against the `$fields` array

**`generateUUID($attribute = 'id')`**
- Generates a UUID for the specified attribute
- Defaults to 'id' if no attribute specified

**`toArray()`**
- Converts DTO to array format
- Handles nested DTO conversion recursively
- Used for model persistence

**`validate()`**
- Performs validation on all DTO fields
- Validates nested DTOs recursively
- Returns boolean success status
- Collects errors in the error system

**`save(string $modelClass)`**
- Persists DTO data to database
- Creates new model instance
- Maps DTO properties to model attributes
- Returns boolean success status

## Usage Patterns

### 1. Basic DTO Creation

```php
class UserDTO extends BaseDTO
{
    protected array $fields = [
        'name' => '',
        'email' => '',
        'status' => 1
    ];

    public function rules(): array
    {
        return [
            'name' => $this->required() . '|' . $this->string() . '|' . $this->max(255),
            'email' => $this->required() . '|' . $this->email(),
            'status' => $this->integer()
        ];
    }
}

// Usage
$userDTO = UserDTO::make();
$userDTO->name = 'John Doe';
$userDTO->email = 'john@example.com';
```

### 2. Loading from Model

```php
$user = User::find(1);
$userDTO = UserDTO::loadFromModel($user);
// DTO now contains all model data
```

### 3. Validation and Persistence

```php
if ($userDTO->validate()) {
    $userDTO->save(User::class);
    $savedUser = $userDTO->getRecord();
}
```

### 4. Nested DTO Support

```php
class AddressDTO extends BaseDTO
{
    protected array $fields = [
        'street' => '',
        'city' => '',
        'postal_code' => ''
    ];

    public function rules(): array
    {
        return [
            'street' => $this->required() . '|' . $this->string(),
            'city' => $this->required() . '|' . $this->string(),
            'postal_code' => $this->required() . '|' . $this->string()
        ];
    }
}

class UserWithAddressDTO extends BaseDTO
{
    protected array $fields = [
        'name' => '',
        'email' => ''
    ];

    protected function requiredDTOs(): array
    {
        return [
            'address' => AddressDTO::class
        ];
    }

    public function rules(): array
    {
        return [
            'name' => $this->required() . '|' . $this->string(),
            'email' => $this->required() . '|' . $this->email()
        ];
    }
}
```

## Validation Rules

The BaseDTO provides convenient methods for common Laravel validation rules:

### Basic Rules
- `required()` - Required field validation
- `string()` - String type validation
- `integer()` - Integer type validation
- `email()` - Email format validation
- `boolean()` - Boolean type validation
- `array()` - Array type validation
- `nullable()` - Allows null values

### Constraint Rules
- `max(int $value)` - Maximum length/value constraint
- `uuid()` - UUID format validation
- `unsignedBigInteger()` - Unsigned big integer validation

### Custom Rules
You can extend the BaseDTO to add custom validation rules:

```php
public function customRule(): string
{
    return 'custom_rule';
}
```

## Error Handling

The BaseDTO integrates with the `HasError` trait for comprehensive error management:

### Error Collection
- Validation errors are automatically collected
- Nested DTO errors are propagated to parent
- Detailed error messages with field context

### Error Checking
```php
if ($dto->hasErrors()) {
    $errors = $dto->getErrors();
    // Handle errors
}
```

## Best Practices

### 1. DTO Design
- Define all fields in the `$fields` array
- Use descriptive field names
- Provide default values for optional fields
- Document complex validation rules

### 2. Validation Rules
- Use the provided rule methods for consistency
- Chain rules with pipe separator
- Test validation thoroughly
- Handle edge cases in custom rules

### 3. Model Integration
- Ensure model class exists before saving
- Handle save failures gracefully
- Use transactions for complex operations
- Validate data before persistence

### 4. Performance Considerations
- Avoid deep nesting of DTOs
- Use lazy loading for large datasets
- Cache validation results when appropriate
- Optimize database queries

## Integration with FinanceRequest Unit

The BaseDTO is extensively used in the FinanceRequest unit for:

### 1. Request Data Validation
- Validating financial request submissions
- Ensuring data integrity before processing
- Handling complex nested financial data structures

### 2. Model Persistence
- Saving validated financial requests to database
- Maintaining data consistency across operations
- Supporting audit trails and data history

### 3. API Response Handling
- Structuring API responses consistently
- Validating incoming API data
- Transforming data between layers

## Related Documentation

- [DTO Pattern Overview](dto.md)
- [Design Patterns](design-patterns.md)
- [API Documentation](api.md)
- [Schematic Patterns](schematic-pattern.md)

## Error Codes

| Code | Description | Resolution |
|------|-------------|------------|
| DTO_001 | Property does not exist in DTO | Check field definition in `$fields` array |
| DTO_002 | Model class not found | Verify model class exists and is imported |
| DTO_003 | Validation failed | Review validation rules and input data |
| DTO_004 | Nested DTO validation failed | Check nested DTO implementation |

## Examples

### Complete FinanceRequest DTO Example

```php
class FinanceRequestDTO extends BaseDTO
{
    protected array $fields = [
        'id' => null,
        'user_id' => null,
        'amount' => 0,
        'currency' => 'IRR',
        'status' => 'pending',
        'description' => '',
        'request_date' => null,
        'approved_date' => null
    ];

    protected function requiredDTOs(): array
    {
        return [
            'user' => UserDTO::class,
            'documents' => DocumentCollectionDTO::class
        ];
    }

    public function rules(): array
    {
        return [
            'user_id' => $this->required() . '|' . $this->integer(),
            'amount' => $this->required() . '|' . $this->integer() . '|min:1000',
            'currency' => $this->required() . '|' . $this->string() . '|in:IRR,USD,EUR',
            'status' => $this->required() . '|' . $this->string() . '|in:pending,approved,rejected',
            'description' => $this->nullable() . '|' . $this->string() . '|' . $this->max(1000),
            'request_date' => $this->required() . '|date',
            'approved_date' => $this->nullable() . '|date|after:request_date'
        ];
    }

    public function generateRequestId(): void
    {
        $this->id = 'FR-' . date('Ymd') . '-' . Str::random(8);
    }
}
```

This documentation provides a comprehensive guide to using the BaseDTO class effectively in your Laravel Filament application, with specific focus on its integration with the FinanceRequest unit.
