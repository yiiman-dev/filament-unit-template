<div dir="rtl" style="direction: rtl;">
    @if(isset($record) && $record)
        <livewire:chat-component :record="$record" />
    @else
        <div style="text-align: center; color: #66; padding: 16px;">
            برای مشاهده چت، لطفاً یک رکورد را ذخیره کنید
        </div>
    @endif
</div>
