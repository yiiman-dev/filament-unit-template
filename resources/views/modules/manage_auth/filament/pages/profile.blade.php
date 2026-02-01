<x-filament-panels::page>
    <div class="space-y-6">
        {{-- اطلاعات پروفایل --}}
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">اطلاعات پروفایل</h2>
                <div class="mt-4">
                    {{ $this->form }}
                </div>
                <div class="mt-4">
                    <x-filament::button
                        wire:click="save"
                        color="primary"
                    >
                        ذخیره تغییرات
                    </x-filament::button>
                </div>
            </div>
        </div>

        {{-- تاریخچه فعالیت‌ها --}}

    </div>
</x-filament-panels::page>

