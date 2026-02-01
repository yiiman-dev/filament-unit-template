# HasSMS Trait

## Description
This trait is used for sending SMS in Filament and uses `BaseSmsService` for SMS sending.

## Location
```
Modules/Basic/app/BaseKit/Filament/HasSMS.php
```

## Usage

```php
use Units\SMS\Common\Concerns\HasSMS;

class MyFilamentPage extends Page
{
    use HasSMS;
    
    public function mount()
    {
        $this->initialHasSms();
    }
    
    public function sendNotification()
    {
        $this->SendSmsToCurrentUser('Your message has been received');
    }
}
```

## Methods

### `initialHasSms()`
Initialize SMS service

```php
public function initialHasSms()
{
    $this->sms_service = new BaseSmsService();
}
```

### `SendSmsToCurrentUser($text)`
Send SMS to currently logged-in user in the current panel

```php
public function SendSmsToCurrentUser($text): void
{
    $this->sms_service->sendSms(
        Helper::normalize_phone_number(filament()->auth()->user()->phone_number), 
        $text
    );
}
```

### `SendSmsToCurrentCorporateCEO($text)`
Send SMS to corporate CEO

```php
public function SendSmsToCurrentCorporateCEO($text): void
{
    $ceo = $this->getCorporateCEOModel();
    if ($ceo && $ceo->phone_number) {
        $this->sms_service->sendSms(Helper::normalize_phone_number($ceo->phone_number), $text);
    }
}
```

## Dependencies

- `Modules\Basic\Helpers\Helper`
- `Modules\Basic\Services\BaseSmsService`
- `InteractWithCorporate` trait

## Complete Example

```php
<?php

use Filament\Pages\Page;
use Units\SMS\Common\Concerns\HasSMS;

class NotificationPage extends Page
{
    use HasSMS;
    
    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static string $view = 'filament.pages.notification';
    
    public function mount()
    {
        $this->initialHasSms();
    }
    
    public function sendWelcomeMessage()
    {
        $this->SendSmsToCurrentUser('Welcome! Your account has been activated.');
        $this->alert_success('SMS sent');
    }
    
    public function notifyCEO()
    {
        $this->SendSmsToCurrentCorporateCEO('New request submitted');
        $this->alert_info('CEO has been notified');
    }
}
```

## Important Notes

1. **Initialization**: Always call `initialHasSms()` in `mount()`
2. **Phone Number**: Phone number is automatically normalized
3. **CEO**: For sending to CEO, national code is read from session
4. **Error**: In case of SMS sending error, the error is logged

## Testing

```php
class HasSMSTest extends TestCase
{
    public function test_send_sms_to_current_user()
    {
        $page = new NotificationPage();
        $page->initialHasSms();
        
        // Mock BaseSmsService
        $page->SendSmsToCurrentUser('Test message');
        
        // Assert SMS was sent
    }
}
```
