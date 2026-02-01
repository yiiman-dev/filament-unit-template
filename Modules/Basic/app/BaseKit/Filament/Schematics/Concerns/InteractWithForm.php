<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          8/4/25, 4:28 PM
 */

namespace Modules\Basic\BaseKit\Filament\Schematics\Concerns;

use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use LaraZeus\Quantity\Components\Quantity;
use Modules\Basic\BaseKit\Filament\Components\Forms\TextInput;
use Modules\Basic\BaseKit\Filament\HasNotification;
use Modules\Basic\Helpers\Helper;
use Mokhosh\FilamentRating\Components\Rating;

trait InteractWithForm
{

    use HasNotification;

    private function configComponent(mixed $component, $attribute): mixed
    {
        /**
         * @var TextInput $component
         */
        try {
            $component->hint($this->getAttributeHint($attribute));
        } catch (\Exception $e) {
        }
        try {
            $component->helperText($this->getAttributeHelperText($attribute));
        } catch (\Exception $e) {
        }
        try {
            $component->placeholder($this->getAttributePlaceholder($attribute));
        } catch (\Exception $e) {
        }
        try {
            $component->disabled($this->isDisabled($attribute));
            if ($this->canSaveOnDisable($attribute)) {
                if ($this->canSaveOnDisable($attribute)) {
                    $component->dehydrated();
                }
            }
        } catch (\Exception $e) {
        }
        try {
            $component->visible($this->isVisible($attribute));
        } catch (\Exception $e) {
        }
        try {
            $component->default($this->getAttributeDefault($attribute));
        } catch (\Exception $e) {
        }
        try {
            $component->label($this->getAttributeLabel($attribute));
        } catch (\Exception $e) {
        }

        return $component;
    }

    // Refactored amountInput method to accept only numbers, format with commas, and display real-time Rial conversion.
    public function amountInput($attribute): TextInput
    {
        return
            $this->configComponent(TextInput::make($attribute), $attribute)
                ->suffix('ماه')
                ->numeric()
                ->step(1)
                ->default(function ($state) {
                    if (!empty($state)) {
                        return $state;
                    }

                    return 0;
                })
                ->suffix('میلیارد ریال')
                ->live()
//                ->mask(RawJs::make('$money($input)'))
//                ->step(0.01)
//                ->numeric()
//                ->stripCharacters(',')
//                ->afterStateHydrated(function (TextInput $component, $state) { // Step 4: Format on hydration
//                    if ($state) {
//                        $component->state(number_format((int)$state));
//                    }
//                })
//
//                ->afterStateUpdated(function (TextInput $component, $state) { // Step 4: Format on update
//                    $cleanedState = str_replace(',', '', $state);
//                    if (is_numeric($cleanedState)) {
//                        $component->state(number_format((int)$cleanedState));
//                    } else {
//                        $component->state(''); // Clear if not numeric after cleaning
//                    }
//                })
                ->dehydrateStateUsing(fn($state) => str_replace(',', '', $state)) // Remove commas before saving
                ->helperText(function ($state) { // Step 5: Add real-time Rial calculation to helper text
                    $cleanedState = str_replace(',', '', $state);
                    if (is_numeric($cleanedState)) {
                        $valueInRial = (float)$cleanedState * 1000000000; // 1 Billion Rial = 10,000,000,000 Rial

                        return 'معادل: ' . number_format($valueInRial) . ' ریال';
                    }

                    return 'معادل: ۰ ریال';
                });
    }

    public function amountRialInput($attribute): TextInput
    {
        return
            $this->configComponent(TextInput::make($attribute), $attribute)
                ->numeric()
                ->step(1)
                ->default(function ($state) {
                    if (!empty($state)) {
                        return $state;
                    }

                    return 0;
                })
                ->suffix('ریال')
                ->live()
//                ->mask(RawJs::make('$money($input)'))
//                ->step(0.01)
//                ->numeric()
//                ->stripCharacters(',')
//                ->afterStateHydrated(function (TextInput $component, $state) { // Step 4: Format on hydration
//                    if ($state) {
//                        $component->state(number_format((int)$state));
//                    }
//                })
//
//                ->afterStateUpdated(function (TextInput $component, $state) { // Step 4: Format on update
//                    $cleanedState = str_replace(',', '', $state);
//                    if (is_numeric($cleanedState)) {
//                        $component->state(number_format((int)$cleanedState));
//                    } else {
//                        $component->state(''); // Clear if not numeric after cleaning
//                    }
//                })
                ->dehydrateStateUsing(fn($state) => str_replace(',', '', $state)) // Remove commas before saving
                ->helperText(function ($state) { // Step 5: Add real-time Rial calculation to helper text
                    $cleanedState = str_replace(',', '', $state);
                    if (is_numeric($cleanedState)) {
                        $valueInRial = (float)$cleanedState; // 1 Billion Rial = 10,000,000,000 Rial

                        return 'معادل: ' . number_format($valueInRial) . ' ریال';
                    }

                    return 'معادل: ۰ ریال';
                });
    }

    /**
     * فیلد ورود و بررسی شماره کارت بانکی
     */
    public function paymentCardTextInput($attribute): TextInput
    {
        // Helper function for bank card validation (Luhn algorithm)
        $bankCardCheck = function ($card) {
            $card = (string)preg_replace('/\D/', '', $card);
            $strlen = strlen($card);

            // For Iranian cards, length must be 16
            if ($strlen !== 16) {
                return false;
            }

            // Check first digit for common card types (4, 5, 6 for Iranian banks)
            // The provided algorithm also checks for 2 and 9, which might be for international cards.
            // For Iranian bank cards, 4, 5, 6 are most common.
            if (!in_array($card[0], [2, 4, 5, 6, 9])) {
                return false;
            }

            $res = [];
            for ($i = 0; $i < $strlen; $i++) {
                $res[$i] = (int)$card[$i];
                if (($strlen % 2) === ($i % 2)) { // Even positions from right (or odd from left, 1-indexed)
                    $res[$i] *= 2;
                    if ($res[$i] > 9) {
                        $res[$i] -= 9;
                    }
                }
            }

            return (array_sum($res) % 10) === 0;
        };

        return $this->textInput($attribute)
            ->extraAlpineAttributes(['style' => 'direction:ltr'])
            ->live() // Step 1: Make the input lives for real-time feedback
            ->mask('9999 9999 9999 9999') // Step 1: Mask for 4-digit groups
            ->length(19) // Step 2: 16 digits + 3 spaces = 19 characters
            ->helperText('شماره کارت بانکی ۱۶ رقمی را وارد کنید.')
            ->label('شماره کارت بانکی')
            ->rules([
                'string',
                'size:19', // Validate the masked length
                // Step 3: Custom validation using the Luhn algorithm
                function (Get $get) use ($bankCardCheck): \Closure {
                    return function (string $attribute, mixed $value, \Closure $fail) use ($bankCardCheck) {
                        $cleanedCard = str_replace(' ', '', $value); // Remove spaces for validation
                        if (!$bankCardCheck($cleanedCard)) {
                            $fail('شماره کارت بانکی وارد شده معتبر نیست.');
                        }
                    };
                },
            ])
            ->validationMessages([ // Step 4: Persian validation messages
                'required' => 'وارد کردن شماره کارت بانکی الزامی است.',
                'string' => 'فرمت شماره کارت بانکی صحیح نیست.',
                'size' => 'شماره کارت بانکی باید ۱۶ رقمی باشد.',
            ])
            ->dehydrateStateUsing(fn($state) => str_replace(' ', '', $state)); // Step 5: Remove spaces before saving
    }

    /**
     * فیلد ورود و بررسی شماره شبای بانکی
     */
    public function shebaTextInput($attribute): TextInput
    {
        // Step 1: Add live(), maxLength, minLength, and helperText
        return $this->textInput($attribute)
            ->suffix('IR')
            ->extraAlpineAttributes(['style' => 'direction:ltr'])
            ->live()
            ->maxLength(24)
            ->minLength(24)
            ->helperText('شناسه شبا باید ۲۴ کاراکتر باشد. مثال: 062960000000100324200001')
            ->rules([
                'size:24', // Ensure exactly 26 characters
                // Step 2 & 3: Implement custom IBAN validation logic
                function (Get $get): \Closure {
                    return function (string $attribute, mixed $value, \Closure $fail) {
                        $value = strtoupper(str_replace(' ', '', $value)); // Normalize input

                        if (strlen($value) !== 24) {
                            $fail('شناسه شبا باید دقیقاً ۲۴ کاراکتر باشد.');

                            return;
                        }
                        $value='IR'.$value;
                        // Character to number mapping as per IBAN standard (A=10, B=11, ..., Z=35)
                        $charToNum = function ($char) {
                            if (is_numeric($char)) {
                                return $char;
                            }

                            return ord($char) - ord('A') + 10;
                        };

                        // 1. Move the first four characters to the end
                        $rearranged = substr($value, 4) . substr($value, 0, 4);

                        // 2. Replace letters with numbers
                        $numericString = '';
                        for ($i = 0; $i < strlen($rearranged); $i++) {
                            $numericString .= $charToNum($rearranged[$i]);
                        }

                        // 3. Calculate modulo 97
                        // PHP's native modulo operator doesn't handle arbitrarily large numbers well.
                        // We need a custom function for large number modulo.
                        $mod97 = function ($numberString) {
                            $remainder = 0;
                            for ($i = 0; $i < strlen($numberString); $i++) {
                                $remainder = ($remainder * 10 + (int)$numberString[$i]) % 97;
                            }

                            return $remainder;
                        };

                        if ($mod97($numericString) !== 1) {
                            $fail('شناسه شبا وارد شده معتبر نیست.');
                        }
                    };
                },
            ])
            ->label('شماره کاربر')
            ->validationMessages([
                'required' => 'وارد کردن شناسه شبا الزامی است.',
                'size' => 'شناسه شبا باید دقیقاً ۲۴ کاراکتر باشد.',
            ]);
    }

    public function textInput($attribute): TextInput
    {
        return $this->configComponent(TextInput::make($attribute), $attribute);
    }

    public function placeHolder($attribute): Placeholder
    {
        return Placeholder::make($attribute);
    }

    public function textNumberInput($attribute): Quantity
    {
        return $this->configComponent(Quantity::make($attribute), $attribute)
            ->default(function ($state) {
                if (!empty($state)) {
                    return $state;
                }

                return 0;
            })
            ->numeric();
    }

    public function repeater($attribute, $add_action_label = null): Repeater
    {
        return Repeater::make($attribute)
            ->label($this->getAttributeLabel($attribute))
            ->addAction(function ($action) use ($add_action_label) {
                /**
                 * @var $action Action
                 */
                $action
                    ->color(Color::Pink)
                    ->size(ActionSize::Medium)
                    ->icon('heroicon-s-plus-circle')
                    ->iconPosition(IconPosition::After);
                if ($add_action_label) {
                    $action->label($add_action_label);
                }

                return $action;
            })
            ->addActionAlignment(Alignment::End);
    }

    public function percentageInput($attribute): TextInput
    {
        return
            $this->configComponent(TextInput::make($attribute), $attribute)
                ->suffix('%')
                ->step(0.02)
                ->default(function ($state) {
                    if (!empty($state)) {
                        return $state;
                    }

                    return 0;
                })
                ->formatStateUsing(fn($state)=>!empty($state)?$state:0)
                ->validationMessages([
                    'min' => 'مقدار درصد باید حداقل ۰ باشد',
                    'max' => 'حداکثر مقدار درصد عدد ۱۰۰ است',
                ])
                ->maxValue(100)
                ->minValue(0)
                ->numeric();
    }

    public function month_input($attribute): Quantity
    {
        return $this->configComponent(Quantity::make($attribute), $attribute)
            ->suffix('ماه')
            ->default(function ($state) {
                if (!empty($state)) {
                    return $state;
                }

                return 0;
            })
            ->numeric();
    }

    public function selectInput($attribute): Select
    {
        return $this->configComponent(Select::make($attribute), $attribute);
    }

    public function internationalPhoneNumberInput($attribute)
    {
        // Step 1: Add mask for international format
        // Step 2: Add regex validation for the specified international format
        // Step 3: Add custom validation message for the regex rule
        return $this->textInput($attribute)
            ->helperText('قالب شماره همراه : +989129876543')
            ->mask('+989999999999') // Mask for +989 followed by 9 digits
            ->rules(['regex:/^\+989[0-9]{9}$/']) // Regex for +989 followed by 9 digits
            ->validationMessages([
                'regex' => 'فرمت شماره همراه بین‌المللی صحیح نیست. مثال: +989123456789',
            ]);
    }

    public function normalPhoneNumberInput($attribute)
    {
        // Step 1: Add mask to guide user input
        // Step 2: Add regex validation for the specified format
        // Step 3: Add custom validation message for the regex rule
        return $this->textInput($attribute)
            ->helperText('قالب شماره همراه : 09129876543')
            ->formatStateUsing(function ($state) {
                return Helper::denormalize_phone_number($state);
            })
            ->mask('99999999999') // Mask for 11 digits
            ->rules(['regex:/^09[0-9]{9}$/']) // Regex for 09 followed by 9 digits
            ->validationMessages([
                'regex' => 'فرمت شماره همراه صحیح نیست. مثال: 09123456789',
            ]);
    }

    public function nationalCodeInput($attribute)
    {
        // Step 1: Add custom validation rule for Iranian national code
        // Step 2: Add custom validation messages for the national code rule
        return $this->textInput($attribute)
            ->live()
            ->mask('9999999999')
            ->rules([
                'digits:10',
                fn(Get $get): \Closure => function ($attribute, $value, $fail) {
                    if (!preg_match('/^\d{10}$/', $value)) {
                        $fail('کد ملی باید ۱۰ رقمی باشد.');

                        return;
                    }

                    // Check for all identical digits
                    if (preg_match('/^(\d)\1{9}$/', $value)) {
                        $fail('کد ملی وارد شده معتبر نیست.');

                        return;
                    }

                    $c = (int)$value[9];
                    $n = 0;
                    for ($i = 0; $i < 9; $i++) {
                        $n += (int)$value[$i] * (10 - $i);
                    }
                    $r = $n % 11;

                    if (($r == 0 && $r == $c) || ($r == 1 && $c == 1) || ($r > 1 && $c == (11 - $r))) {
                        // Valid national code
                    } else {
                        $fail('کد ملی وارد شده معتبر نیست.');
                    }
                },
            ])
            ->validationMessages([
                'required' => 'وارد کردن کد ملی الزامی است.',
                'digits' => 'کد ملی باید ۱۰ رقمی باشد.',
            ]);
    }

    public function richEditorInput($attribute): RichEditor
    {
        return $this->configComponent(RichEditor::make($attribute), $attribute);
    }

    public function tinyEditor($attribute): TinyEditor
    {
        return $this->configComponent(TinyEditor::make($attribute)->setCustomConfigs(['provider'=>'vendor'])->language(
            'en'), $attribute)
            ->rtl(); // Set RTL or use ->direction('auto|rtl|ltr');
    }

    public function checkBoxInput($attribute): Checkbox
    {
        return $this->configComponent(Checkbox::make($attribute), $attribute);
    }

    public function checkBoxListInput($attribute): CheckboxList
    {
        return $this->configComponent(CheckboxList::make($attribute), $attribute);
    }

    public function colorPickerInput($attribute): ColorPicker
    {
        return $this->configComponent(ColorPicker::make($attribute), $attribute);
    }

    public function datePickerInput($attribute): DatePicker
    {
        return $this->configComponent(DatePicker::make($attribute)->suffixIcon('heroicon-s-calendar'), $attribute)
            ->live()
            ->helperText(function ($state) {
                if (!empty($state)) {
                    $date = new \DateTime($state);
                    $today = new \DateTime();
                    $interval = $today->diff($date);

                    if ($date < $today) {
                        // Past date
                        return  $interval->days . ' روز قبل';
                    } else {
                        // Future date
                        return  $interval->days . ' روز بعد';
                    }
                }
                return '';
            })
            ->jalali();
    }

    public function dateTimePickerInput($attribute): DateTimePicker
    {
        return $this->configComponent(DateTimePicker::make($attribute), $attribute)
            ->live()
            ->helperText(function ($state) {
                if (!empty($state)) {
                    $date = new \DateTime($state);
                    $today = new \DateTime();
                    $interval = $today->diff($date);

                    if ($date < $today) {
                        // Past date
                        return 'تاریخ گذشته - ' . $interval->days . ' روز قبل';
                    } else {
                        // Future date
                        return 'تاریخ آینده - ' . $interval->days . ' روز مانده';
                    }
                }
                return '';
            })
            ->jalali();
    }

    public function fileUploadInput($attribute, string $directory): FileUpload
    {
        $fileUpload = $this->configComponent(FileUpload::make($attribute), $attribute);
        /**
         *
         * @var FileUpload $fileUpload
         */
        return $fileUpload
            ->disk(app()->isProduction() ? 's3' : 'local')
            ->directory($directory)
            ->moveFiles()
            ->multiple(false)
            ->visibility('private');

    }

    public function radioInput($attribute): Radio
    {
        return $this->configComponent(Radio::make($attribute), $attribute);
    }

    public function textAreaInput($attribute): Textarea
    {
        return $this->configComponent(Textarea::make($attribute), $attribute);
    }

    public function tagsInput($attribute): TagsInput
    {
        return $this->configComponent(TagsInput::make($attribute), $attribute);
    }

    public function timePickerInput($attribute): TimePicker
    {
        return $this->configComponent(TimePicker::make($attribute), $attribute);
    }

    public function toggleInput($attribute): Toggle
    {
        return $this->configComponent(Toggle::make($attribute), $attribute);
    }

    public function toggleButtonsInput($attribute): ToggleButtons
    {
        return $this->configComponent(ToggleButtons::make($attribute), $attribute);
    }

    public function viewField($attribute): \Modules\Basic\BaseKit\Filament\Components\Forms\ViewField
    {
        return $this->configComponent(
            \Modules\Basic\BaseKit\Filament\Components\Forms\ViewField::make($attribute),
            $attribute
        );
    }

    public function actionField($attribute): Action
    {
        return $this->configComponent(Action::make($attribute), $attribute);
    }

    /**
     *  این اکشن فقط پراپرتی هایی که به آن داده شده است را ولیدیت و ذخیره میکند
     *
     * اگر در حین ذخیره اطلاعات مشکلی پیش بیاید خطا را در سیستم لاگ منکث میکند
     *
     * @param array $attributes
     * @return Action
     */
    public function saveAction(array $attributes): Action
    {
        return $this->actionField('save')
            ->label('ذخیره اطلاعات')
            ->outlined()
            ->visible()
            ->color('gray')
            ->action(function ($record, $livewire) use ($attributes) {
                /**
                 *
                 * @var CreateRecord|EditRecord $livewire
                 * @var Model $record
                 */
                if ($livewire->validate()) {
                    $livewire->save();
                }
//                if($livewire->validate(
//                    collect( $livewire->form->getValidationRules())->only($attributes)->toArray(),
//                    collect( $livewire->form->getValidationMessages())->only($attributes)->toArray(),
//                    collect($livewire->form->getValidationAttributes())->only($attributes)->toArray()
//                ))
//                {
//                    try {
//                        $data=$livewire->mutateFormDataBeforeSave($livewire->data);
//                        $record->update(collect($data)->only($attributes)->toArray());
//                        $record->addHistory($livewire->data);
//                    }catch (\Exception $e){
//                        $this->alert_error('خطایی در ثبت اطلاعات رخ داد');
//                        Log::error('خطا در ثبت اطلاعات میانی فرم: '.$e->getMessage(),$e->getTrace());
//                    }
//                }
            });
    }

    /**
     * اکشن تایید با استفاده از عدد در مودال
     * @param string|null $attribute
     * @param Closure|string|null $action
     * @param array|Closure $form_components
     * @return Action
     */
    public function confirmCodeAction(
        ?string $attribute = null,
        Closure|string|null $action = null,
        array|Closure $form_components = []
    ): Action {
        $generated_code = rand(1111, 9999);
        $action = Action::make($attribute)
            ->color('success')
            ->modalHeading('ورود کد تایید')
            ->size(ActionSize::Large->value)
            ->defaultSize(ActionSize::Large->value)
            ->modalWidth(MaxWidth::Large->value)
            ->modalDescription(
                new HtmlString(
                    "<h2 style='direction: rtl'>لطفا جهت تایید عملیات٬ کد <strong style='font-weight: 900;font-size: x-large'>$generated_code</strong> را درون کادر ذیل وارد کنید.</h2>"
                )
            )
            ->modalSubmitActionLabel('تایید')
            ->modalCancelActionLabel('انصراف')
            ->form([
                \Filament\Forms\Components\TextInput::make('verification_code')
                    ->label('کد ۴ رقمی')
                    ->required()
                    ->length(7) // طول دقیق 4 رقم
                    ->mask('*-*-*-*')
                    ->extraAlpineAttributes([
                        'style' => 'font-size: large;text-align:center;direction:ltr;'
                    ])
                    ->columns(1)
                    ->default(fn()=>app()->hasDebugModeEnabled()?$generated_code:'')
                    ->maxWidth('50%'),
                TextInput::make('hide')
                    ->extraAlpineAttributes(['style' => 'width:0px;height:0px;opacity:0'])
                    ->extraFieldWrapperAttributes(['style' => 'width:0px;height:0px;opacity:0'])
                    ->hiddenLabel()
                    ->default($generated_code),
                ...$form_components
            ])
            ->action(function (array $data,$record,$get,$set,$livewire) use ($action) {
                // $data['verification_code'] شامل کد وارد شده توسط کاربر است

                // اینجا منطق اعتبارسنجی کد را قرار دهید
                $code = str_replace(['-', '*'], '', $data['verification_code']);

                if ((string)$code === (string)$data['hide']) { // مثال اعتبارسنجی ساده
                    // عملیات تایید موفق
//                    Notification::make('success_'.uniqid())
//                        ->success()
//                        ->title('success')
//                        ->body('کد تایید صحیح است.')
//                        ->send();
                    if (!empty($action)) {
                        $action($data,$record,$get,$set,$livewire);
                    }
                } else {
                    // در صورت خطا می‌توانید خطا ارسال کنید یا پیام دهید

                    Notification::make('danger_' . uniqid())
                        ->danger()
                        ->title('danger')
                        ->body('کد تایید اشتباه است.')
                        ->send();
                    // اگر بخواهید Modal بسته نشود می‌توانید استثنا پرتاب کنید:
                    // throw new \Exception('کد تایید اشتباه است.');
                }
            });
        return $this->configComponent($action, $attribute);
    }


    /**
     * اکشن رد با استفاده از عدد تاییدیه در مودال و دریافت دلیل مخالفت از کاربر
     *
     * Reason of reject will pass on ``$data['reject_reason']`` to action method
     *
     * You should use that like :
     *
     * ```
     *
     * $this->rejectAction('reject')
     *  ->action(fn($data)=>echo $data['reject_reason'])
     * ```
     * @param string|null $attribute
     * @return Action
     */
    public function rejectAction(?string $attribute = null): Action
    {
        $generated_code = rand(1111, 9999);
        $action = Action::make($attribute)
            ->modalHeading('ورود کد تایید')
            ->color('danger')
            ->modalDescription(
                new HtmlString(
                    "<h2 style='direction: rtl'>لطفا جهت تایید <div style='
width: fit-content;
    color: red;
    position: relative;
    display: contents;
    '> مخالفت</div>٬ کد <strong style='font-weight: 900;font-size: x-large'>$generated_code</strong> را درون کادر ذیل وارد کنید.</h2>"
                )
            )
            ->size(ActionSize::Large->value)
            ->defaultSize(ActionSize::Large->value)
            ->modalWidth(MaxWidth::Large->value)
            ->modalSubmitActionLabel('تایید')
            ->modalCancelActionLabel('انصراف')
            ->form([
                \Filament\Forms\Components\TextInput::make('verification_code')
                    ->label('کد ۴ رقمی')
                    ->required()
                    ->length(7) // طول دقیق 4 رقم
                    ->mask('*-*-*-*')
                    ->default(fn()=>app()->hasDebugModeEnabled()?$generated_code:'')
                    ->extraAlpineAttributes([
                        'style' => 'font-size: large;text-align:center;direction:ltr;'
                    ])
                    ->columns(1)
                    ->maxWidth('50%'),
                TextInput::make('hide')
                    ->extraAlpineAttributes(['style' => 'width:0px;height:0px;opacity:0'])
                    ->extraFieldWrapperAttributes(['style' => 'width:0px;height:0px;opacity:0'])
                    ->hiddenLabel()
                    ->default($generated_code),
                $this->tinyEditor('reject_reason')
                    ->label('علت مخالفت')
                    ->visible()
                    ->required()
                    ->hint('لطفا علت مخالفت خود را شرح دهید')
            ])
            ->action(function (array $data) {
                $code = str_replace(['-', '*'], '', $data['verification_code']);
                if ((string)$code != (string)$data['hide']) {
                    Notification::make('danger_' . uniqid())
                        ->danger()
                        ->title('danger')
                        ->body('کد تایید اشتباه است.')
                        ->send();
                }
            });
        return $this->configComponent($action, $attribute);
    }


    /**
     * اکشن تایید با استفاده از عدد در مودال
     * @param string|null $attribute
     * @return Action
     */
    public function approveAction(?string $attribute = null): Action
    {
        $generated_code = rand(1111, 9999);
        $action = Action::make($attribute)
            ->modalHeading('ورود کد تایید')
            ->modalDescription(
                new HtmlString(
                    "<h2 style='direction: rtl'>لطفا جهت تایید <div style='color: green;display: contents;width: fit-content'>موافقت </div>٬ کد <strong style='font-weight: 900;font-size: x-large'>$generated_code</strong> را درون کادر ذیل وارد کنید.</h2>"
                )
            )
            ->size(ActionSize::Large->value)
            ->defaultSize(ActionSize::Large->value)
            ->modalWidth(MaxWidth::Large->value)
            ->modalSubmitActionLabel('تایید')
            ->modalCancelActionLabel('انصراف')
            ->beforeFormFilled(fn($livewire)=>$livewire->form->validate())
            ->form([
                \Filament\Forms\Components\TextInput::make('verification_code')
                    ->label('کد ۴ رقمی')
                    ->required()
                    ->length(7) // طول دقیق 4 رقم
                    ->mask('*-*-*-*')
                    ->extraAlpineAttributes([
                        'style' => 'font-size: large;text-align:center;direction:ltr;'
                    ])
                    ->default(fn()=>app()->hasDebugModeEnabled()?$generated_code:'')
                    ->columns(1)
                    ->maxWidth('50%'),
                TextInput::make('hide')
                    ->extraAlpineAttributes(['style' => 'width:0px;height:0px;opacity:0'])
                    ->extraFieldWrapperAttributes(['style' => 'width:0px;height:0px;opacity:0'])
                    ->hiddenLabel()
                    ->default($generated_code),
            ])
            ->action(function (array $data) {
                $code = str_replace(['-', '*'], '', $data['verification_code']);
                if ((string)$code != (string)$data['hide']) {
                    Notification::make('danger_' . uniqid())
                        ->danger()
                        ->title('danger')
                        ->body('کد تایید اشتباه است.')
                        ->send();
                }
            });
        return $this->configComponent($action, $attribute);
    }


    public function statusSelectField($attribute, $statuses): Select
    {
        return $this->selectInput($attribute)
            ->options($statuses)
            ->default(Collection::make($statuses)->first());
    }

    public function ratingInput($attribute):Rating{
        return $this->configComponent(Rating::make($attribute), $attribute);
    }

    public function mountInput($attribute){
        return $this->selectInput($attribute)
            ->visible()
            ->options([
                1 => '1',
                2 => '2',
                3 => '3',
                4 => '4',
                5 => '5',
                6 => '6',
                7 => '7',
                8 => '8',
                9 => '9',
                10 => '10',
                11 => '11',
                12 => '12',
                13 => '13',
                14 => '14',
                15 => '15',
                16 => '16',
                17 => '17',
                18 => '18',
                19 => '19',
                20 => '20',
                21 => '21',
                22 => '22',
                23 => '23',
                25 => '25',
                26 => '26',
                27 => '27',
                28 => '28',
                29 => '29',
                30 => '30',
                31 => '31',
                32 => '32',
                33 => '33',
                34 => '34',
                35 => '35',
                36 => '36',
            ])
            ->suffix('ماه');
    }

    public function postalCode($attribute){
        return $this->textInput($attribute)
            ->visible()
            ->maxLength(10)
            ->mask('9999999999');
    }
}
