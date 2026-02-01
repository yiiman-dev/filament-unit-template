
@php
    use Filament\Support\Enums\MaxWidth;

@endphp

@push('styles')
    {{-- <link rel="stylesheet" href="{{ asset('modules/filamentadmin/css/custom.css')  }}"> --}}
    <link href="{{ asset('css/global.css') }}" rel="stylesheet"/>
@endpush
@push('scripts')
@endpush

<x-filament-panels::layout.base :livewire="$livewire">
    <div class=" flex-row p-6 gap-8 mx-auto px-4 items-center" style="height:99vh;font-family: Shabnam">
        <div style="height: 95vh" class=" grid grid-cols-1 md:grid-cols-2 gap-4 bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Left Section: Form -->
            <div class="@container sm:hidden md:block">
                <div class="flex items-center relative overflow-hidden p-6 h-full" style="background-position: center;background-size: cover ;background-image: url({{asset('assets/my/auth/images/auth-banner.png')}})">

                </div>
            </div>
            <div class="p-8 flex flex-col justify-center items-center h-[90vh]">
                <x-bladewind::card >
                    <div class="text-center mb-6 h-full">
                        <!-- Logo -->
                        <img src="{{asset('image/logo.png')}}" alt="Logo" class="mx-auto mb-4">
                        <!-- Welcome Text -->
                        <h1 class="text-xl font-bold  text-gray-800">
                            به پلتفرم تامین مالی آرین خوش آمدید
                            <span style="margin-top: 10px;display: block"></span>
                            ورود به سامانه متقاضی
                        </h1>
                    </div>
                    {{ $slot }}
                </x-bladewind::card>
            </div>

            <!-- Right Section: Image -->

        </div>
    </div>

</x-filament-panels::layout.base>
