
@php
    use Filament\Support\Enums\MaxWidth;

@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('modules/filamentadmin/css/custom.css')  }}">
    <link href="{{ asset('vendor/bladewind/css/animate.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/bladewind/css/bladewind-ui.min.css') }}" rel="stylesheet" />
@endpush
@push('scripts')
    <script src="{{ asset('vendor/bladewind/js/helpers.js') }}"></script>
@endpush

<x-filament-panels::layout.base :livewire="$livewire">
    <div class="flex-row p-6 gap-8 mx-auto px-4 items-center">



        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Left Section: Form -->
            <div class="@container">
                <div class="relative overflow-hidden p-6 @max-sm::::hidden">
                    <img src="{{asset('assets/admin/auth/images/auth-banner.png')}}"
                         alt="Drink Image" class="w-full h-full object-cover">
                </div>
            </div>
            <div class="p-8 flex flex-col justify-center items-center">
                <x-bladewind::card>
                    <div class="text-center mb-6">
                        <!-- Logo -->
                        <img src="{{asset('image/logo.png')}}" alt="Logo" class="mx-auto mb-4">
                        <!-- Welcome Text -->
                        <h1 class="text-xl font-bold  text-gray-800">به سامانه جامع تامین مالی خوش آمدید</h1>
                    </div>
                    {{ $slot }}
                </x-bladewind::card>
            </div>

            <!-- Right Section: Image -->

        </div>
    </div>

</x-filament-panels::layout.base>




{{--<x-filament-panels::layout.base :livewire="$livewire">--}}
{{--    @props([--}}
{{--        'after' => null,--}}
{{--        'heading' => null,--}}
{{--        'subheading' => null,--}}
{{--    ])--}}
{{--    <div class="flex flex-col">--}}
{{--        <div class="items-end">--}}
{{--            <div class="fi-simple-layout flex min-h-screen flex-col items-center">--}}
{{--                @if (($hasTopbar ?? true) && filament()->auth()->check())--}}
{{--                    <div--}}
{{--                        class="absolute end-0 top-0 flex h-16 items-center gap-x-4 pe-4 md:pe-6 lg:pe-8"--}}
{{--                    >--}}
{{--                        @if (filament()->hasDatabaseNotifications())--}}
{{--                            @livewire(Filament\Livewire\DatabaseNotifications::class, [--}}
{{--                            'lazy' => filament()->hasLazyLoadedDatabaseNotifications()--}}
{{--                            ])--}}
{{--                        @endif--}}

{{--                        <x-filament-panels::user-menu/>--}}
{{--                    </div>--}}
{{--                @endif--}}

{{--                <div--}}
{{--                    class="fi-simple-main-ctn flex w-full flex-grow items-center justify-center"--}}
{{--                >--}}
{{--                    <main--}}
{{--                        @class([--}}
{{--                            'fi-simple-main my-16 w-full bg-white px-6 py-12 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:rounded-xl sm:px-12',--}}
{{--                            match ($maxWidth ??= (filament()->getSimplePageMaxContentWidth() ?? MaxWidth::Large)) {--}}
{{--                                MaxWidth::ExtraSmall, 'xs' => 'max-w-xs',--}}
{{--                                MaxWidth::Small, 'sm' => 'max-w-sm',--}}
{{--                                MaxWidth::Medium, 'md' => 'max-w-md',--}}
{{--                                MaxWidth::Large, 'lg' => 'max-w-lg',--}}
{{--                                MaxWidth::ExtraLarge, 'xl' => 'max-w-xl',--}}
{{--                                MaxWidth::TwoExtraLarge, '2xl' => 'max-w-2xl',--}}
{{--                                MaxWidth::ThreeExtraLarge, '3xl' => 'max-w-3xl',--}}
{{--                                MaxWidth::FourExtraLarge, '4xl' => 'max-w-4xl',--}}
{{--                                MaxWidth::FiveExtraLarge, '5xl' => 'max-w-5xl',--}}
{{--                                MaxWidth::SixExtraLarge, '6xl' => 'max-w-6xl',--}}
{{--                                MaxWidth::SevenExtraLarge, '7xl' => 'max-w-7xl',--}}
{{--                                MaxWidth::Full, 'full' => 'max-w-full',--}}
{{--                                MaxWidth::MinContent, 'min' => 'max-w-min',--}}
{{--                                MaxWidth::MaxContent, 'max' => 'max-w-max',--}}
{{--                                MaxWidth::FitContent, 'fit' => 'max-w-fit',--}}
{{--                                MaxWidth::Prose, 'prose' => 'max-w-prose',--}}
{{--                                MaxWidth::ScreenSmall, 'screen-sm' => 'max-w-screen-sm',--}}
{{--                                MaxWidth::ScreenMedium, 'screen-md' => 'max-w-screen-md',--}}
{{--                                MaxWidth::ScreenLarge, 'screen-lg' => 'max-w-screen-lg',--}}
{{--                                MaxWidth::ScreenExtraLarge, 'screen-xl' => 'max-w-screen-xl',--}}
{{--                                MaxWidth::ScreenTwoExtraLarge, 'screen-2xl' => 'max-w-screen-2xl',--}}
{{--                                default => $maxWidth,--}}
{{--                            },--}}
{{--                        ])--}}
{{--                    >--}}
{{--                        {{ $slot }}--}}
{{--                    </main>--}}
{{--                </div>--}}

{{--                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::FOOTER, scopes: $livewire->getRenderHookScopes()) }}--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="items-start">--}}
{{--            <h3>kdjhkjhkja</h3>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</x-filament-panels::layout.base>--}}
