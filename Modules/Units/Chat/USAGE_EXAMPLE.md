# مثال استفاده از واحد چت

## نحوه ادغام چت با مدل‌های دلخواه

این مثال نشان می‌دهد که چگونه می‌توانید واحد چت را با هر مدل ایلومینیت ادغام کنید.

### مثال 1: ادغام با مدل FinanceRequest

```php
<?php

namespace Modules\Units\FinanceRequest\Manage\Filament\Resources\FinanceRequestResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Units\Chat\Common\Filament\Widgets\ChatFormWidget;
use Units\Chat\Common\Filament\Widgets\ChatInfolistWidget;
use Modules\Units\FinanceRequest\Manage\Filament\Resources\FinanceRequestResource;

class ViewFinanceRequest extends ViewRecord
{
    protected static string $resource = FinanceRequestResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            ChatInfolistWidget::make('chat_messages')
                ->record($this->record), // اتصال چت به رکورد فعلی
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            ChatFormWidget::make('chat_form')
                ->record($this->record), // اتصال فرم چت به رکورد فعلی
        ];
    }
}
```

### مثال 2: ادغام در فرم سفارش

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
                // فیلدهای سفارش
                Forms\Components\TextInput::make('customer_name')
                    ->required()
                    ->label('نام مشتری'),
                
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->label('مبلغ'),
                
                // اضافه کردن ویجت چت
                ChatFormWidget::make('order_chat')
                    ->record($this->record) // اتصال به رکورد سفارش
                    ->columnSpan('full'),
            ]);
    }
}
```

### مثال 3: استفاده در اینفولیست سفارش

```php
<?php

namespace Modules\Units\Orders\Manage\Filament\Resources\OrderResource;

use Filament\Infolists\Infolist;
use Units\Chat\Common\Filament\Widgets\ChatInfolistWidget;

class ViewOrder
{
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // اطلاعات سفارش
                Infolists\Components\TextEntry::make('customer_name')
                    ->label('نام مشتری'),
                
                Infolists\Components\TextEntry::make('amount')
                    ->label('مبلغ')
                    ->money('IRR'),
                
                // نمایش پیام‌های چت
                ChatInfolistWidget::make('order_chat_messages')
                    ->record($this->record)
                    ->columnSpan('full'),
            ]);
    }
}
```

### مثال 4: استفاده مستقیم در سرویس

```php
<?php

namespace Modules\Units\FinanceRequest\Common\Services;

use Units\Chat\Common\Services\ChatService;

class FinanceRequestChatService
{
    private ChatService $chatService;
    
    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }
    
    /**
     * شروع چت برای درخواست مالی
     */
    public function startChatForFinanceRequest($financeRequest)
    {
        $this->chatService->actStartChat(
            get_class($financeRequest),
            $financeRequest->id,
            [
                'title' => "چت درخواست مالی #{$financeRequest->id}",
                'description' => "چت برای {$financeRequest->customer_name}",
            ]
        );
        
        if ($this->chatService->hasErrors()) {
            throw new \Exception('خطا در شروع چت');
        }
        
        return $this->chatService->getSuccessResponse()->getData()['thread'];
    }
    
    /**
     * ارسال پیام به چت درخواست مالی
     */
    public function sendMessageToFinanceRequestChat($threadId, $senderType, $senderId, $message)
    {
        $this->chatService->actSendMessage($threadId, $senderType, $senderId, $message);
        
        if ($this->chatService->hasErrors()) {
            throw new \Exception('خطا در ارسال پیام');
        }
        
        return $this->chatService->getSuccessResponse()->getData()['message'];
    }
    
    /**
     * دریافت پیام‌های چت
     */
    public function getChatMessages($threadId)
    {
        $this->chatService->actGetThreadMessages($threadId);
        
        if ($this->chatService->hasErrors()) {
            throw new \Exception('خطا در دریافت پیام‌ها');
        }
        
        return $this->chatService->getSuccessResponse()->getData()['messages'];
    }
}
```

### مثال 5: استفاده در کنترلر

```php
<?php

namespace Modules\Units\FinanceRequest\Http\Controllers;

use Illuminate\Http\Request;
use Units\Chat\Common\Services\ChatService;

class ChatController extends Controller
{
    private ChatService $chatService;
    
    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }
    
    /**
     * ارسال پیام چت از طریق API
     */
    public function sendMessage(Request $request, $modelType, $modelId)
    {
        // اعتبارسنجی درخواست
        $request->validate([
            'content' => 'required|string|max:1000',
            'sender_type' => 'required|in:applicant,agent',
            'sender_id' => 'required|integer',
        ]);
        
        // شروع یا یافتن چت برای مدل
        $this->chatService->actStartChat($modelType, $modelId, [
            'title' => "چت برای {$modelType}-{$modelId}",
        ]);
        
        if ($this->chatService->hasErrors()) {
            return response()->json(['error' => 'خطا در شروع چت'], 500);
        }
        
        $thread = $this->chatService->getSuccessResponse()->getData()['thread'];
        
        // ارسال پیام
        $this->chatService->actSendMessage(
            $thread->id,
            $request->sender_type,
            $request->sender_id,
            $request->content
        );
        
        if ($this->chatService->hasErrors()) {
            return response()->json(['error' => 'خطا در ارسال پیام'], 500);
        }
        
        $message = $this->chatService->getSuccessResponse()->getData()['message'];
        
        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }
    
    /**
     * دریافت پیام‌های چت
     */
    public function getMessages($threadId)
    {
        $this->chatService->actGetThreadMessages($threadId);
        
        if ($this->chatService->hasErrors()) {
            return response()->json(['error' => 'خطا در دریافت پیام‌ها'], 500);
        }
        
        $messages = $this->chatService->getSuccessResponse()->getData()['messages'];
        
        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }
}
```

## نکات مهم

1. **مدل‌های UUID**: واحد چت از هر دو نوع آیدی عددی و UUID پشتیبانی می‌کند
2. **چند-tenant**: در پنل مای، فیلترهای tenant باید بر اساس نیاز پیاده‌سازی شوند
3. **امنیت**: همیشه احراز هویت کاربران را قبل از دسترسی به چت بررسی کنید
4. **عملکرد**: برای چت‌های فعال، ممکن است نیاز به WebSocket برای عملکرد بهتر باشد

## رفع اشکال

### خطای "Record not found"
- اطمینان از وجود رکورد قبل از استفاده از ویجت چت
- بررسی اتصال به پایگاه داده

### خطای "Access denied"
- بررسی اجازه‌های دسترسی کاربر
- تأیید نقش‌های کاربری

### خطای "Database connection"
- بررسی تنظیمات پایگاه داده
- تأیید اجرای میگریشن‌ها
