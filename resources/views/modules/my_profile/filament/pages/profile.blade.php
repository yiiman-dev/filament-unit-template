<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 mb-5">
                    {{ $this->form }}
                <div class="mt-5">
                    <x-filament::button wire:click="save" color="primary">
                        ذخیره تغییرات
                    </x-filament::button>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
