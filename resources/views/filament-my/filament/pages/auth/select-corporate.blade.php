@php
    use Filament\Support\Facades\FilamentView;

    $color = 'primary';
@endphp

<x-filament-panels::page.simple>
    @if (FilamentView::hasSpaMode())
        <div class="absolute end-4 top-4">
            <x-filament-panels::theme-switcher />
        </div>
    @endif

    <div class="relative flex flex-col items-center justify-center">
        <div class="absolute end-0 top-0">
            <div class="flex justify-end">
                <x-filament-panels::theme-switcher />
            </div>
        </div>

        <div class="flex w-full flex-col items-center justify-center">
            <div class="my-2 text-center">
                <x-filament-panels::logo />
            </div>

            <h2 class="text-2xl font-bold tracking-tight text-center">
                {{ $layoutData['title'] ?? 'انتخاب شرکت' }}
            </h2>

            <p class="mt-2 text-center">
                لطفاً شرکت مورد نظر خود را برای ورود به پنل شرکت انتخاب نمایید.
            </p>

            <div class="mt-8 w-full md:w-3/4">
                <div class="space-y-6">
                    <form
                        wire:submit="select"
                        class="flex w-full flex-col items-center justify-center rounded-xl p-8"
                    >
                        {{ $this->form }}

                        <div class="mt-8 w-full space-y-4">
                            @foreach($this->getFormActions() as $action)
                                {{ $action }}
                            @endforeach
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <x-filament-panels::footer />
</x-filament-panels::page.simple> 