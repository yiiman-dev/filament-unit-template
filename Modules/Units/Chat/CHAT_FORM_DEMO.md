# دمو استفاده از چت در فرم بدون تداخل با سابمیت فرم

## مقدمه
این دمو نشان می‌دهد که چگونه می‌توان از کامپوننت چت در فرم‌های مختلف استفاده کرد بدون اینکه سابمیت فرم اصلی تحت تأثیر قرار گیرد.

## مثال 1: استفاده در فرم ثبت درخواست مالی

```php
<?php

namespace Modules\Units\FinanceRequest\Manage\Filament\Resources\FinanceRequestResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Units\Chat\Common\Filament\Widgets\ChatFormWidget;
use Modules\Units\FinanceRequest\Manage\Filament\Resources\FinanceRequestResource;

class EditFinanceRequest extends EditRecord
{
    protected static string $resource = FinanceRequestResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // فیلدهای فرم اصلی
                Forms\Components\TextInput::make('customer_name')
                    ->label('نام مشتری')
                    ->required(),
                
                Forms\Components\TextInput::make('amount')
                    ->label('مبلغ')
                    ->numeric()
                    ->required(),
                
                Forms\Components\Textarea::make('description')
                    ->label('توضیحات')
                    ->rows(3),
                
                // کامپوننت چت - بدون تداخل با فرم اصلی
                ChatFormWidget::make('chat')
                    ->record($this->record)
                    ->columnSpan('full'),
            ]);
    }
    
    // دکمه سابمیت فرم اصلی همچنان کار می‌کند
    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('save')
                ->label('ذخیره درخواست')
                ->submit('save'), // این سابمیت فقط فرم اصلی را انجام می‌دهد
        ];
    }
}
```

## مثال 2: استفاده در صفحه نمایش با چت

```php
<?php

namespace Modules\Units\FinanceRequest\Manage\Filament\Resources\FinanceRequestResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Units\Chat\Common\Filament\Widgets\ChatInfolistWidget;
use Units\Chat\Common\Filament\Widgets\ChatFormWidget;
use Modules\Units\FinanceRequest\Manage\Filament\Resources\FinanceRequestResource;

class ViewFinanceRequest extends ViewRecord
{
    protected static string $resource = FinanceRequestResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            // چت در هدر ویجت‌ها
            ChatInfolistWidget::make('chat_messages')
                ->record($this->record),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            // فرم چت در فوتر ویجت‌ها
            ChatFormWidget::make('chat_form')
                ->record($this->record),
        ];
    }
}
```

## مثال 3: استفاده در فرم سفارش با چت

```php
<?php

namespace Modules\Units\Orders\Manage\Filament\Resources\OrderResource;

use Filament\Forms\Form;
use Units\Chat\Common\Filament\Widgets\ChatFormWidget;

class EditOrder
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // اطلاعات سفارش
                Forms\Components\Section::make('اطلاعات سفارش')
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->label('نام مشتری')
                            ->required(),
                        
                        Forms\Components\TextInput::make('order_amount')
                            ->label('مبلغ سفارش')
                            ->numeric()
                            ->required(),
                    ]),
                
                // بخش چت
                Forms\Components\Section::make('چت با مشتری')
                    ->schema([
                        ChatFormWidget::make('order_chat')
                            ->record($this->record)
                            ->columnSpan('full'),
                    ])
                    ->collapsible(),
                
                // دکمه‌های فرم
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('submit_order')
                        ->label('ثبت سفارش')
                        ->color('primary')
                        ->submit(), // این فقط فرم اصلی را سابمیت می‌کند
                    
                    Forms\Components\Actions\Action::make('save_draft')
                        ->label('ذخیره پیش‌نویس')
                        ->color('secondary')
                        ->submit(), // این هم فقط فرم اصلی را سابمیت می‌کند
                ])->columnSpan('full'),
            ]);
    }
}
```

## نحوه کارکرد بدون تداخل

### 1. فیلد چت با `prevent`:
```html
<!-- در فایل: Modules/Units/Chat/Common/resources/views/livewire/chat-component.blade.php -->
<input
    wire:keydown.enter.prevent="sendMessage"
    x-on:keydown.enter.prevent="$wire.sendMessage()"
/>
```

این کد باعث می‌شود که:
- `prevent` از سابمیت فرم والد جلوگیری کند
- `sendMessage()` فقط پیام چت را ارسال کند

### 2. دکمه ارسال چت با `type="button"`:
```html
<button
    type="button"
    wire:click="sendMessage"
    onclick="event.preventDefault();"
>
    ارسال
</button>
```

این کد باعث می‌شود که:
- `type="button"` از سابمیت فرم جلوگیری کند
- `onclick="event.preventDefault();"` اضافی برای اطمینان بیشتر

### 3. اعتبارسنجی در کامپوننت:
```php
public function sendMessage(): void
{
    if (empty(trim($this->newMessage)) || !$this->threadId) {
        return;
    }
    
    // فقط منطق ارسال پیام چت
    // هیچ تداخلی با فرم والد ندارد
}
```

## تست رفتار کاربر

### سناریو 1: کاربر در چت Enter می‌زند
```
کاربر در فیلد چت تایپ می‌کند: "سلام، سوالی داشتم"
کاربر Enter را فشار می‌دهد
نتیجه: 
✓ فقط پیام چت ارسال می‌شود
✓ فرم اصلی سابمیت نمی‌شود
✓ کاربر می‌تواند ادامه کار کند
```

### سناریو 2: کاربر فرم اصلی را سابمیت می‌کند
```
کاربر روی دکمه "ثبت سفارش" کلیک می‌کند
نتیجه:
✓ فرم اصلی سابمیت می‌شود
✓ چت به عنوان یک کامپوننت مستقل کار می‌کند
✓ هیچ تداخلی بین دو عمل وجود ندارد
```

### سناریو 3: کاربر پیام خالی ارسال می‌کند
```
کاربر فیلد چت را خالی رها کرده و Enter می‌زند
نتیجه:
✓ هیچ پیامی ارسال نمی‌شود
✓ فرم اصلی تأثیر نمی‌پذیرد
✓ کاربر می‌تواند ادامه دهد
```

## مزایای این پیاده‌سازی

### 1. عدم تداخل
- چت به عنوان یک کامپوننت مستقل کار می‌کند
- سابمیت فرم اصلی تحت تأثیر قرار نمی‌گیرد
- کاربران می‌توانند همزمان از هر دو قابلیت استفاده کنند

### 2. تجربه کاربری بهتر
- کاربران می‌توانند پیام‌های چت را با Enter ارسال کنند
- نیازی به توجه ویژه به جلوگیری از سابمیت فرم نیست
- رابط کاربری طبیعی و روان

### 3. انعطاف‌پذیری
- قابل استفاده در هر فرم فیلمنت
- سازگار با انواع مدل‌ها
- بدون نیاز به تغییر در فرم‌های موجود

## نحوه استفاده در پروژه‌های جدید

### 1. اضافه کردن به فرم:
```php
use Units\Chat\Common\Filament\Widgets\ChatFormWidget;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            // فیلدهای اصلی فرم
            // ...
            
            // اضافه کردن چت
            ChatFormWidget::make('chat')
                ->record($this->record)
                ->columnSpan('full'),
        ]);
}
```

### 2. اضافه کردن به صفحه نمایش:
```php
use Units\Chat\Common\Filament\Widgets\ChatInfolistWidget;
use Units\Chat\Common\Filament\Widgets\ChatFormWidget;

protected function getHeaderWidgets(): array
{
    return [
        ChatInfolistWidget::make('chat_messages')
            ->record($this->record),
    ];
}

protected function getFooterWidgets(): array
{
    return [
        ChatFormWidget::make('chat_form')
            ->record($this->record),
    ];
}
```

## نتیجه‌گیری

این پیاده‌سازی اطمینان حاصل می‌کند که:
- چت به عنوان یک کامپوننت مستقل و غیر مزاحم کار می‌کند
- سابمیت فرم اصلی تحت تأثیر قرار نمی‌گیرد
- کاربران می‌توانند از هر دو قابلیت به طور همزمان استفاده کنند
- تجربه کاربری بهتر و طبیعی‌تری فراهم می‌شود

این رفع مشکل باعث می‌شود که چت به یک ابزار کاربردی واقعی در محیط‌های فرم تبدیل شود.
