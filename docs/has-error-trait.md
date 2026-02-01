# HasError Trait

## Description
This trait is used for error management in services and classes, providing the ability to store, check, and retrieve errors.

## Location
```
Modules/Basic/app/Concerns/HasError.php
```

## Usage

```php
use Modules\Basic\Concerns\HasError;

class MyService
{
    use HasError;
    
    public function process()
    {
        if ($error) {
            $this->addError(['field' => 'name'], 'Name is required');
        }
        
        if ($this->hasErrors()) {
            return false;
        }
    }
}
```

## Methods

### `addError($data, $message)`
Add error to error list

```php
public function addError(array|string $data = [], string $message = ''): void
{
    $this->errors[] = new ErrorService($data, $message);
    $info = getReferrerInfo();
    \Log::error(is_string($data) ? $data : $message, [
        'class' => self::class,
        'line' => $info['line'],
        'file' => $info['file'],
    ]);
}
```

### `hasErrors()`
Check for errors

```php
public function hasErrors(): bool
{
    return !empty($this->errors);
}
```

### `hasNotError()`
Check for no errors

```php
public function hasNotError(): bool
{
    return !$this->hasErrors();
}
```

### `getErrors()`
Get all errors

```php
public function getErrors(): array
{
    return $this->errors;
}
```

### `getErrorCollection()`
Get errors as Collection

```php
public function getErrorCollection(): Collection
{
    return Collection::make($this->errors);
}
```

### `getErrorMessages()`
Get error messages

```php
public function getErrorMessages(): array
{
    return array_map(fn($error) => $error->getMessage(), $this->errors);
}
```

### `handleModelErrors($model)`
Handle model errors

```php
public function handleModelErrors($model): void
{
    if ($errors = $model->getErrors()) {
        foreach ($errors as $field => $messages) {
            $this->addError(['field' => $field], implode(', ', $messages));
        }
    }
}
```

## Dependencies

- `Illuminate\Support\Collection`
- `Modules\Basic\BaseKit\ErrorService`

## Complete Example

```php
<?php

use Modules\Basic\Concerns\HasError;

class UserService
{
    use HasError;
    
    public function createUser($data)
    {
        // Data validation
        if (empty($data['name'])) {
            $this->addError(['field' => 'name'], 'Name is required');
        }
        
        if (empty($data['email'])) {
            $this->addError(['field' => 'email'], 'Email is required');
        }
        
        if (empty($data['phone'])) {
            $this->addError(['field' => 'phone'], 'Phone number is required');
        }
        
        // Check for errors
        if ($this->hasErrors()) {
            return false;
        }
        
        try {
            // Create user
            $user = User::create($data);
            return $user;
        } catch (\Exception $e) {
            $this->addError([], 'Error creating user: ' . $e->getMessage());
            return false;
        }
    }
    
    public function updateUser($id, $data)
    {
        $user = User::find($id);
        
        if (!$user) {
            $this->addError(['field' => 'id'], 'User not found');
            return false;
        }
        
        try {
            $user->update($data);
            return $user;
        } catch (\Exception $e) {
            $this->addError([], 'Error updating user: ' . $e->getMessage());
            return false;
        }
    }
    
    public function getErrorSummary()
    {
        if ($this->hasErrors()) {
            return [
                'has_errors' => true,
                'errors' => $this->getErrors(),
                'messages' => $this->getErrorMessages(),
                'count' => count($this->getErrors())
            ];
        }
        
        return ['has_errors' => false];
    }
}
```

## Usage in Filament

```php
<?php

use Filament\Pages\Page;
use Modules\Basic\Concerns\HasError;
use Modules\Basic\BaseKit\Filament\HasNotification;

class UserPage extends Page
{
    use HasError, HasNotification;
    
    public function saveUser()
    {
        $service = new UserService();
        $result = $service->createUser($this->form->getState());
        
        if ($service->hasErrors()) {
            foreach ($service->getErrorMessages() as $message) {
                $this->alert_error($message);
            }
            return;
        }
        
        $this->alert_success('User created successfully');
    }
}
}
```

## Important Notes

1. **Automatic Logging**: All errors are automatically logged
2. **Additional Information**: File and error information is stored in the log
3. **Data Types**: Errors can be arrays or strings
4. **Model Management**: Model errors are automatically managed

## Testing

```php
class HasErrorTest extends TestCase
{
    public function test_add_error()
    {
        $service = new UserService();
        
        $service->addError(['field' => 'name'], 'Name is required');
        
        $this->assertTrue($service->hasErrors());
        $this->assertCount(1, $service->getErrors());
    }
    
    public function test_get_error_messages()
    {
        $service = new UserService();
        
        $service->addError(['field' => 'name'], 'Name is required');
        $service->addError(['field' => 'email'], 'Email is required');
        
        $messages = $service->getErrorMessages();
        
        $this->assertContains('Name is required', $messages);
        $this->assertContains('Email is required', $messages);
    }
}
```

## Best Practices

1. **Error Checking**: Always check for errors before continuing operations
2. **Clear Messages**: Use clear and understandable error messages
3. **Combine with Other Traits**: Combine this trait with `HasNotification`
4. **Exception Management**: Use try-catch for exception handling
