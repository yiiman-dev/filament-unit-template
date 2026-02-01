# HasNotification Trait

## Description
This trait is used for displaying notifications in Filament and uses the Filament Notification API.

## Location
```
Modules/Basic/app/BaseKit/Filament/HasNotification.php
```

## Usage

```php
use Modules\Basic\BaseKit\Filament\HasNotification;

class MyFilamentPage extends Page
{
    use HasNotification;
    
    public function showNotifications()
    {
        $this->alert_success('Operation completed successfully');
        $this->alert_error('Error in operation');
    }
}
```

## Methods

### `alert_error($message, $title = '')`
Display error notification with red color

```php
public function alert_error(string $message, string $title = ''): void
{
    Notification::make('error_' . uniqid())
        ->danger()
        ->title($title)
        ->body($message)
        ->send();
}
```

### `alert_success($message, $title = '')`
Display success notification with green color

```php
public function alert_success(string $message, string $title = ''): void
{
    Notification::make('success_' . uniqid())
        ->success()
        ->title($title)
        ->body($message)
        ->send();
}
```

### `alert_info($message, $title = '')`
Display info notification with blue color

```php
public function alert_info(string $message, string $title = ''): void
{
    Notification::make('info_' . uniqid())
        ->info()
        ->title($title)
        ->body($message)
        ->send();
}
```

### `alert_warning($message, $title = '')`
Display warning notification with yellow color

```php
public function alert_warning(string $message, string $title = ''): void
{
    Notification::make('warning_' . uniqid())
        ->warning()
        ->title($title)
        ->body($message)
        ->send();

```

## Dependencies

- `Filament\Notifications\Notification`

## Complete Example

```php
<?php

use Filament\Pages\Page;
use Modules\Basic\BaseKit\Filament\HasNotification;

class DashboardPage extends Page
{
    use HasNotification;
    
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';
    
    public function saveData()
    {
        try {
            // Save operation
            $this->alert_success('Data saved successfully', 'Success operation');
        } catch (\Exception $e) {
            $this->alert_error('Error saving data: ' . $e->getMessage(), 'Error');
        }
    }
    
    public function showInfo()
    {
        $this->alert_info('Important information for you', 'Notice');
    }
    
    public function showWarning()
    {
        $this->alert_warning('This operation is irreversible', 'Warning');
    }
}
```

## Notification Types

| Type | Color | Purpose |
|-----|-----|--------|
| `alert_success` | Green | Success operation |
| `alert_error` | Red | Error |
| `alert_info` | Blue | Information |
| `alert_warning` | Yellow | Warning |

## Important Notes

1. **Unique ID**: Each notification has a unique ID generated with `uniqid()`
2. **Optional Title**: The `$title` parameter is optional
3. **Language**: Messages can be in Persian
4. **Auto-hide**: Notifications automatically disappear

## Testing

```php
class HasNotificationTest extends TestCase
{
    public function test_alert_success()
    {
        $page = new DashboardPage();
        
        // Mock Notification
        $page->alert_success('Test message', 'Test title');
        
        // Assert notification was sent
    }
    
    public function test_alert_error()
    {
        $page = new DashboardPage();
        
        $page->alert_error('Error message');
        
        // Assert error notification was sent
    }
}
```

## Best Practices

1. **Clear Messages**: Use clear and understandable messages
2. **Appropriate Title**: Use titles for important notifications
3. **Proper Language**: Use Persian for messages
4. **Combine with Other Traits**: Combine this trait with `HasError`
