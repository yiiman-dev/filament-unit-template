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
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Get;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconPosition;
use LaraZeus\Quantity\Components\Quantity;
use Modules\Basic\BaseKit\Filament\Components\Forms\TextInput;
use Modules\Basic\BaseKit\Filament\Form\Components\MobileInput;
use Modules\Basic\BaseKit\Filament\Form\Components\MoneyInput;
use Morilog\Jalali\Jalalian;
use Units\Corporates\Users\Common\Models\CorporateUsersModel;

use function Laravel\Prompts\text;

trait InteractWithInfoList
{


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
            $component->visible($this->isVisible($attribute));
        } catch (\Exception $e) {
        }
        try {
            $component->label($this->getAttributeLabel($attribute));
        } catch (\Exception $e) {
        }
        return $component->visible();
    }

    // Refactored amountInput method to accept only numbers, format with commas, and display real-time Rial conversion.
    public function amountInput($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute)
                ->suffix('میلیارد ریال')
                ->formatStateUsing(function ($state) {
                    if (!empty($state)) {
                        return number_format((float)$state);
                    }
                    return '0';
                })
                ->helperText(function ($state) { // Step 5: Add real-time Rial calculation to helper text
                    $cleanedState = str_replace(',', '', $state);
                    if (is_numeric($cleanedState)) {
                        $valueInRial = (float)$cleanedState * 1000000000; // 1 Billion Rial = 10,000,000,000 Rial
                        return 'معادل: ' . number_format($valueInRial) . ' ریال';
                    }
                    return 'معادل: ۰ ریال';
                })
            ,
            $attribute
        );
    }

    /**
     * فیلد ورود و بررسی شماره کارت بانکی
     *
     * @param $attribute
     * @return TextEntry
     */
    public function paymentCardTextInput($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute)
                ->formatStateUsing(function ($state) {
                    if (!empty($state)) {
                        // Format card number with spaces
                        return preg_replace('/(\d{4})/', '$1 ', trim($state));
                    }
                    return '';
                })
            ,
            $attribute
        );
    }

    /**
     * فیلد ورود و بررسی شماره شبای بانکی
     * @param $attribute
     * @return TextEntry
     */
    public function shebaTextInput($attribute): TextEntry
    {
        // Step 1: Add live(), maxLength, minLength, and helperText
        return $this->textInput($attribute)
            ->suffix('IR')
            ->extraAlpineAttributes(['style' => 'direction:ltr'])
            ->live()
            ->maxLength(26)
            ->minLength(26)
            ->helperText('شناسه شبا باید ۲۴ کاراکتر باشد. مثال: 062960000000100324200001')
            ->rules([
                'size:26', // Ensure exactly 26 characters
                // Step 2 & 3: Implement custom IBAN validation logic
                function (Get $get): \Closure {
                    return function (string $attribute, mixed $value, \Closure $fail) {
                        $value = strtoupper(str_replace(' ', '', $value)); // Normalize input

                        if (strlen($value) !== 24) {
                            $fail('شناسه شبا باید دقیقاً ۲۴ کاراکتر باشد.');
                            return;
                        }

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
            ->validationMessages([
                'required' => 'وارد کردن شناسه شبا الزامی است.',
                'size' => 'شناسه شبا باید دقیقاً ۲۴ کاراکتر باشد.',
            ]);
    }

    public function textInput($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute),
            $attribute
        );
    }


    public function textNumberInput($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute)
                ->numeric()
                ->formatStateUsing(function ($state) {
                    if (!empty($state)) {
                        return number_format((float)$state);
                    }
                    return '0';
                })
            ,
            $attribute
        );
    }


    public function repeater($attribute, $add_action_label = null): RepeatableEntry
    {
        return RepeatableEntry::make($attribute)
            ->label($this->getAttributeLabel($attribute));
    }

    public function percentageInput($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute)
                ->suffix('%')
                ->formatStateUsing(function ($state) {
                    if (!empty($state)) {
                        return $state;
                    }
                    return '0';
                })
            ,
            $attribute
        );
    }


    public function month_input($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute)
                ->suffix('ماه')
                ->formatStateUsing(function ($state) {
                    if (!empty($state)) {
                        return $state;
                    }
                    return '0';
                })
            ,
            $attribute
        );
    }

    public function selectInput($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute),
            $attribute
        );
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
                'regex' => 'فرمت شماره همراه بین‌الملتی صحیح نیست. مثال: +989123456789',
            ]);
    }

    public function normalPhoneNumberInput($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute)
                ->formatStateUsing(function ($state) {
                    if (!empty($state)) {
                        return '0' . ltrim(trim($state), '0');
                    }
                    return '';
                })
            ,
            $attribute
        );
    }

    public function nationalCodeInput($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute)
                ->formatStateUsing(function ($state) {
                    if (!empty($state)) {
                        return trim($state);
                    }
                    return '';
                })
            ,
            $attribute
        );
    }

    public function richEditorInput($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute),
            $attribute
        );
    }

    public function tinyEditor($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute),
            $attribute
        );
    }

    public function checkBoxInput($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute)
                ->formatStateUsing(function ($state) {
                    return $state ? 'فعال' : 'غیرفعال';
                })
                ->badge()
                ->color(function ($state) {
                    return $state ? 'success' : 'danger';
                })
            ,
            $attribute
        );
    }

    public function checkBoxListInput($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute)
                ->formatStateUsing(function ($state) {
                    if (is_array($state)) {
                        return implode(', ', $state);
                    }
                    return '';
                })
            ,
            $attribute
        );
    }

    public function colorPickerInput($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute)
                ->formatStateUsing(function ($state) {
                    return $state ? $state : 'بدون رنگ';
                })
                ->badge()
                ->color(function ($state) {
                    return $state;
                })
            ,
            $attribute
        );
    }

    public function datePickerInput($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute)
                ->date()
                ->formatStateUsing(fn(CorporateUsersModel $record) =>$record->expire_at?Jalalian::fromCarbon( $record->expire_at)->toFormattedDateString():'بدون تاریخ انقضا')
            // Add additional information about remaining/past days
                ->tooltip(function ($state) {
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
                }),
            $attribute
        );
    }

    public function dateTimePickerInput($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute)
                ->dateTime()
                ->jalaliDateTime()
            ,
            $attribute
        );
    }

    public function fileUploadInput($attribute): \Filament\Infolists\Components\ImageEntry
    {
        return $this->configComponent(
            \Filament\Infolists\Components\ImageEntry::make($attribute),
            $attribute
        );
    }

    public function radioInput($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute),
            $attribute
        );
    }

    public function textAreaInput($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute),
            $attribute
        );
    }

    public function tagsInput($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute)
                ->formatStateUsing(function ($state) {
                    if (is_array($state)) {
                        return implode(', ', $state);
                    }
                    return '';
                })
            ,
            $attribute
        );
    }

    public function timePickerInput($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute)
                ->time()
            ,
            $attribute
        );
    }

    public function toggleInput($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute)
                ->formatStateUsing(function ($state) {
                    return $state ? 'فعال' : 'غیرفعال';
                })
                ->badge()
                ->color(function ($state) {
                    return $state ? 'success' : 'danger';
                })
            ,
            $attribute
        );
    }

    public function toggleButtonsInput($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute)
                ->formatStateUsing(function ($state) {
                    return $state ? 'فعال' : 'غیرفعال';
                })
                ->badge()
                ->color(function ($state) {
                    return $state ? 'success' : 'danger';
                })
            ,
            $attribute
        );
    }


    public function viewField($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute),
            $attribute
        );
    }

    public function actionField($attribute): TextEntry
    {
        return $this->configComponent(
            TextEntry::make($attribute),
            $attribute
        );
    }
}
