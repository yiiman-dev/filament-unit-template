# واحد چت - Chat Unit

## توضیحات کلی
این واحد یک سیستم چت مدل-آگنوسیک است که امکان ارتباط دوطرفه بین درخواست‌کنندگان (پنل مای) و کارشناسان (پنل مدیریت) را فراهم می‌کند. این واحد می‌تواند به هر مدل ایلومینیت متصل شود و از روابط چند-model استفاده می‌کند.

## ویژگی‌ها
- سیستم چت مدل-آگنوسیک (قابل استفاده با هر مدل)
- پشتیبانی از چند-tenant برای پنل مای
- ارتباط دوطرفه بین درخواست‌کنندگان و کارشناسان
- پیام‌های متنی ساده (بدون فایل، ایموجی یا متن ضخیم)
- نمایشگر وضعیت "در حال تایپ" و "دیده شده"
- بارگذاری دوره‌ای پیام‌ها هر 3 ثانیه
- ویجت‌های فیلمنت (فرم + اینفولیست)
- پشتیبانی از RTL و فارسی

## ساختار پایگاه داده

### جدول chat_threads
- `id`: شناسه منحصر به فرد
- `model_type`: نوع مدل مرتبط (polymorphic)
- `model_id`: شناسه مدل مرتبط (polymorphic)
- `title`: عنوان چت
- `description`: توضیحات چت
- `meta`: اطلاعات متا به صورت JSON
- `created_at`, `updated_at`: زمان‌های رکورد

### جدول chat_messages
- `id`: شناسه منحصر به فرد
- `chat_thread_id`: ارتباط با ترد چت
- `sender_type`: نوع فرستنده (applicant/agent)
- `sender_id`: شناسه فرستنده
- `content`: محتوای پیام
- `is_seen`: نشانه دیده شدن
- `seen_at`: زمان دیده شدن
- `meta`: اطلاعات متا به صورت JSON
- `created_at`, `updated_at`: زمان‌های رکورد

## ادغام با مدل‌های دیگر

### نحوه استفاده در منابع فیلمنت

```php
// در فایل منبع خود
use Units\Chat\Common\Filament\Widgets\ChatFormWidget;
use Units\Chat\Common\Filament\Widgets\ChatInfolistWidget;

// در فرم (به عنوان کامپوننت فرم)
public static function form(Form $form): Form
{
    return $form
        ->schema([
            // سایر فیلدها
            ChatFormWidget::make('chat')
                ->record($this->record), // یا هر مدلی که چت باید به آن متصل شود
        ]);
}

// در صفحه نمایش (View) به عنوان اینفولیست
protected function getHeaderWidgets(): array
{
    return [
        ChatInfolistWidget::make('chat_messages')
            ->record($this->record),
    ];
}

// یا در اینفولیست مستقیم
public function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            // سایر کامپوننت‌ها
            ChatInfolistWidget::make('chat_messages')
                ->record($this->record),
        ]);
}
```

### استفاده مستقیم در سرویس‌ها

```php
use Units\Chat\Common\Services\ChatService;

$chatService = app(ChatService::class);

// شروع چت جدید
$chatService->actStartChat('App\Models\FinanceRequest', $financeRequestId, [
    'title' => 'چت درخواست مالی'
]);

if (!$chatService->hasErrors()) {
    $thread = $chatService->getSuccessResponse()->getData()['thread'];
    
    // ارسال پیام
    $chatService->actSendMessage($thread->id, 'applicant', $userId, 'متن پیام');
    
    // دریافت پیام‌ها
    $chatService->actGetThreadMessages($thread->id);
    $messages = $chatService->getSuccessResponse()->getData()['messages'];
}
```

## ویجت‌های فیلمنت

### ChatFormWidget
ویجت فرم برای ارسال پیام‌های جدید. شامل:
- فیلد ورودی متنی RTL
- دکمه ارسال
- ویژگی‌های زنده (در حال تایپ، بارگذاری دوره‌ای)

### ChatInfolistWidget
ویجت اینفولیست برای نمایش پیام‌ها. شامل:
- نمایش پیام‌های موجود
- نشانگرهای دیده شدن
- زمان ارسال پیام
- پشتیبانی از RTL

## پنل‌ها

### پنل مدیریت (Manage Panel)
- منبع `ChatThreadResource` برای مدیریت تردهای چت
- دسترسی به تمام چت‌های سیستم
- نمایش جزئیات هر ترد چت

### پنل مای (My Panel)
- منبع `ChatThreadResource` برای نمایش چت‌های کاربر
- پشتیبانی از چند-tenant
- فیلتر کردن بر اساس مالکیت کاربر

## ویژگی‌های زنده

### بارگذاری دوره‌ای
- بارگذاری خودکار پیام‌های جدید هر 3 ثانیه
- به‌روزرسانی خودکار نمایشگر

### نشانگر در حال تایپ
- نمایش "در حال نوشتن..." هنگام تایپ
- ارسال سیگنال تایپ به سمت مقابل

### نشانگر دیده شدن
- علامت‌گذاری پیام‌ها به عنوان دیده شده
- نمایش وضعیت دیده شدن

## پیکربندی

### افزودن به پنل‌ها
```php
// در فایل پلاگین پنل مدیریت
use Units\Chat\Manage\ChatManagePlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            ChatManagePlugin::make(),
            // سایر پلاگین‌ها
        ]);
}

// در فایل پلاگین پنل مای
use Units\Chat\My\ChatMyPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            ChatMyPlugin::make(),
            // سایر پلاگین‌ها
        ]);
}
```

## استفاده در لاراول

### اجرای میگریشن‌ها
```bash
php artisan migrate
```

### استفاده در کنترلرها
```php
use Units\Chat\Common\Services\ChatService;

class YourController extends Controller
{
    private ChatService $chatService;
    
    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }
    
    public function startChat($modelType, $modelId)
    {
        $this->chatService->actStartChat($modelType, $modelId);
        
        if (!$this->chatService->hasErrors()) {
            $thread = $this->chatService->getSuccessResponse()->getData()['thread'];
            return response()->json(['thread' => $thread]);
        }
        
        return response()->json(['error' => 'Failed to start chat'], 500);
    }
}
```

## نکات مهم

1. **چند-tenant**: در پنل مای، فیلترهای tenant باید بر اساس نیاز پیاده‌سازی شوند
2. **امنیت**: اطمینان از احراز هویت کاربران قبل از دسترسی به چت
3. **عملکرد**: برای چت‌های فعال، ممکن است نیاز به WebSocket برای عملکرد بهتر باشد
4. **پشتیبانی**: این پیاده‌سازی از polling استفاده می‌کند، نه WebSocket
5. **RTL**: تمام واسط‌ها به صورت RTL و فارسی پشتیبانی می‌شوند

## اشکال‌زدایی

### مشکلات رایج
- اطمینان از وجود رکورد قبل از استفاده از ویجت چت
- بررسی اجازه‌های دسترسی کاربر
- تأیید اتصال به پایگاه داده

### لاگ‌ها
- استفاده از `Log::debug()` برای ردیابی مشکلات
- بررسی لاگ‌های پایگاه داده برای اشکالات SQL
