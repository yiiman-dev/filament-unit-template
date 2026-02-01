<div dir="rtl" style="direction: rtl;">
    @if(isset($this->record) && $this->record)
        @php

            // Ensure the record is not a closure before passing to Livewire
            $resolvedRecord = $this->record;
            if ($resolvedRecord instanceof \Closure) {
                $resolvedRecord = $resolvedRecord();
            }
            // Additional check to ensure we have a proper Model instance
            if (!($resolvedRecord instanceof \Illuminate\Database\Eloquent\Model)) {
                $resolvedRecord = null;
            }

        @endphp
        @if($resolvedRecord)
            @php
                // Register dynamic component for this instance to avoid conflicts
                $componentId = 'dynamic-chat-' . uniqid();
                \Livewire\Livewire::component($componentId, \Units\Chat\Common\Livewire\DynamicChatComponent::class);
            @endphp
            <livewire:dynamic-component
                :is="$componentId"
                :record="$resolvedRecord"
                :persona="$getPersona()"
                :term="$getSenderType()"
                :tenant_national_code="$getTenantNationalCode()"
                :button_label="$getButtonLabel()"
                :key="$componentId"
            />
        @else
            <div style="text-align: center; color: #66; padding: 16px;">
                رکورد معتبری برای چت یافت نشد
            </div>
        @endif
    @else
        <div style="text-align: center; color: #66; padding: 16px;">
            برای استفاده از چت، لطفاً یک رکورد را ذخیره کنید
        </div>
    @endif
</div>
