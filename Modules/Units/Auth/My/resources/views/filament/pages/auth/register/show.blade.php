@if (filament()->hasRegistration())
    <x-slot name="subheading">
        {{ __('filament-panels::pages/auth/login.actions.register.before') }}

        {{ $this->registerAction }}
    </x-slot>
@endif

{{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}


@if($data)

    @if($data['corporate_type']==\Units\Corporates\Placed\Common\Enums\LegalModeEnum::NATURAL->value)
        <div class="space-y-4 text-center">
            <div class="text-lg font-semibold text-gray-900 dark:text-white">
                حساب کاربری شخصیت حقیقی به مدیریت
                <span
                    class="text-primary-600 dark:text-primary-400">{{ $data['ceo_first_name'].' '.$data['ceo_last_name'] }}</span><br
                    با کد ملی
                <span class="text-primary-600 dark:text-primary-400">{{ $data['ceo_national_code'] }}</span><br>
                و شماره همراه
                <span dir="ltr" class="text-primary-600 dark:text-primary-400">{{ $data['ceo_mobile'] }}</span><br>
                با موفقیت در صف تایید قرار گرفت.
            </div>

            <div class="text-gray-600 dark:text-gray-400">
                در صورت فعال شدن بنگاه شما٬ پیامک فعال سازی ارسال خواهد شد.
            </div>

            <div class="text-gray-600 dark:text-gray-400">
                ورود به سامانه صرفا با شماره همراه فوق امکانپذیر می باشد.
            </div>

            <div class="text-gray-600 dark:text-gray-400">
                در صورت نیاز می توانید با شماره ذیل تماس حاصل فرمائید:
                <br><span class="text-primary-600 dark:text-primary-400"><a
                        href="tel:{{manage_setting('global.brand.phone1')}}">{{manage_setting('global.brand.phone1_title')}}</a></span><br>
                <span class="text-primary-600 dark:text-primary-400"><a
                        href="tel:{{manage_setting('global.brand.phone2')}}">{{manage_setting('global.brand.phone2_title')}}</a></span><br>
            </div>
        </div>
    @else
        <div class="space-y-4 text-center">
            <div class="text-lg font-semibold text-gray-900 dark:text-white">
                حساب کاربری شرکت
                <span class="text-primary-600 dark:text-primary-400">{{ $data['name'] }}</span><br>
                به شناسه ملی
                <span class="text-primary-600 dark:text-primary-400">{{ $data['national_id'] }}</span><br>
                به مدیریت عاملی
                <span
                    class="text-primary-600 dark:text-primary-400">{{ $data['ceo_first_name'].' '.$data['ceo_last_name'] }}</span><br>
                با کد ملی
                <span class="text-primary-600 dark:text-primary-400">{{ $data['ceo_national_code'] }}</span><br>
                و شماره همراه
                <span class="text-primary-600 dark:text-primary-400">{{ $data['ceo_mobile'] }}</span><br>
                با موفقیت ایجاد شد.
            </div>

            <div class="text-gray-600 dark:text-gray-400">
                پیامک احراز هویت ارسال خواهد شد.
            </div>

            <div class="text-gray-600 dark:text-gray-400">
                ورود به سامانه صرفا با شماره همراه فوق امکانپذیر می باشد.
            </div>

            <div class="text-gray-600 dark:text-gray-400">
                در صورت نیاز می توانید با شماره های ذیل تماس حاصل فرمائید:
                <br><span class="text-primary-600 dark:text-primary-400"><a
                        href="tel:{{manage_setting('global.brand.phone1')}}">{{manage_setting('global.brand.phone1_title')}}</a></span><br>
                <span class="text-primary-600 dark:text-primary-400"><a
                        href="tel:{{manage_setting('global.brand.phone2')}}">{{manage_setting('global.brand.phone2_title')}}</a></span><br>
            </div>
        </div>
    @endif
@endif

{{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}

