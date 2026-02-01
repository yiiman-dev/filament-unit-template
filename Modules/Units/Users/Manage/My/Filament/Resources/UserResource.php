<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/8/25, 7:57 PM
 */

namespace Units\Users\Manage\My\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Basic\BaseKit\Filament\Components\Forms\DateTimePicker;
use Modules\Basic\BaseKit\Filament\Components\Forms\Textarea;
use Modules\Basic\Helpers\Helper;
use Morilog\Jalali\Jalalian;
use Predis\Command\Argument\Search\SchemaFields\TextField;
use Units\Auth\My\Models\UserMetadata;
use Units\Auth\My\Models\UserModel;
use Units\SMS\Common\Services\BaseSmsService;
use Units\Users\Manage\My\Enums\UserStatusEnum;
use Units\Users\Manage\My\Enums\ValidateStatusEnum;
use Units\Users\Manage\My\Filament\Resources\UserResource\Pages;
use Units\Users\Manage\My\Filament\Schematic\FormPartial\BankInfoSchema;
use Units\Users\Manage\My\Filament\Schematic\FormPartial\UserValidationFieldsetSchema;
use Units\Users\Manage\My\Models\User;
use Units\Users\Manage\My\Services\UserService;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'کاربران پنل متقاضی';
    protected static ?string $modelLabel = 'کاربر پنل بنگاه';

    protected static ?string $navigationGroup = 'مدیریت کاربران';
    protected static ?string $pluralLabel = 'کاربران پنل بنگاه';

    protected static ?int $navigationSort = 1;

//    public static function getModelLabel(): string
//    {
//        return 'کاربر';
//    }
//
//    public static function getPluralModelLabel(): string
//    {
//        return 'کاربران';
//    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات کاربر')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('first_name')
                                    ->formatStateUsing(function (?UserModel $record) {
                                        if (empty($record)) {
                                            return null;
                                        }
                                        $profile = UserMetadata::getProfile($record->national_code);
                                        if (!empty($profile['first_name'])) {
                                            return $profile['first_name'];
                                        }
                                        return null;
                                    })
                                    ->label('نام'),
                                Forms\Components\TextInput::make('last_name')
                                    ->formatStateUsing(function (?UserModel $record) {
                                        if (empty($record)) {
                                            return null;
                                        }
                                        $profile = UserMetadata::getProfile($record->national_code);
                                        if (!empty($profile['last_name'])) {
                                            return $profile['last_name'];
                                        }
                                        return null;
                                    })
                                    ->label('نام خانوادگی'),
                                DateTimePicker::make('birth_day')
                                    ->jalali()
                                    ->formatStateUsing(function (?UserModel $record) {
                                        if (empty($record)) {
                                            return null;
                                        }
                                        $profile = UserMetadata::getProfile($record->national_code);
                                        if (!empty($profile['birth_day'])) {
                                            return $profile['birth_day'];
                                        }
                                        return null;
                                    })
                                    ->label('تاریخ تولد'),
                                Forms\Components\TextInput::make('national_code')
                                    ->label('کد ملی')
                                    ->required()
                                    ->maxLength(10)
                                    ->unique(table: User::class, column: 'national_code', ignoreRecord: true)
                                    ->validationAttribute('کد ملی'),

                                Forms\Components\TextInput::make('phone_number')
                                    ->label('شماره موبایل')
                                    ->required()
                                    ->maxLength(14)
                                    ->tel()
                                    ->validationAttribute('شماره موبایل'),

                                Forms\Components\Select::make('status')
                                    ->label('وضعیت')
                                    ->required()
                                    ->options([
                                        UserStatusEnum::ACTIVE->value => 'فعال',
                                        UserStatusEnum::INACTIVE->value => 'غیرفعال',
                                    ])
                                    ->default(UserStatusEnum::ACTIVE->value),

                                Forms\Components\Select::make('validate_status')
                                    ->label('وضعیت اعتبارسنجی')
                                    ->required()
                                    ->options([
                                        ValidateStatusEnum::VALIDATED->value => 'تایید شده',
                                        ValidateStatusEnum::NOT_VALIDATED->value => 'تایید نشده',
                                    ])
                                    ->default(ValidateStatusEnum::NOT_VALIDATED->value),
                            ]),
                        Forms\Components\Grid::make(1)
                            ->schema([
                                Textarea::make('bio')
                                    ->formatStateUsing(function (?UserModel $record) {
                                        if (empty($record)) {
                                            return null;
                                        }
                                        $profile = UserMetadata::getProfile($record->national_code);
                                        if (!empty($profile['bio'])) {
                                            return $profile['bio'];
                                        }
                                        return null;
                                    })
                                    ->helperText('')
                                    ->label('درباره کاربر'),
                                Forms\Components\TextInput::make('address')
                                    ->formatStateUsing(function (?UserModel $record) {
                                        if (empty($record)) {
                                            return null;
                                        }
                                        $profile = UserMetadata::getProfile($record->national_code);
                                        if (!empty($profile['address'])) {
                                            return $profile['address'];
                                        }
                                        return null;
                                    })
                                    ->label('آدرس'),
                            ]),
                        ...BankInfoSchema::makeSchema()->returnCommonSchema(),
                        ...UserValidationFieldsetSchema::makeSchema()->returnCommonSchema()
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at','desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('شناسه')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('id')
                    ->label('نام کاربر')
                    ->formatStateUsing(function (User $record) {
                        return $record->first_name . ' ' . $record->last_name;
                    })
                    ->sortable()
                    ->copyable()
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('national_code')
                    ->label('کد ملی')
                    ->sortable()
                    ->copyable()
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('شماره موبایل')
                    ->sortable()
                    ->extraAttributes(['style' => 'direction:ltr'])
                    ->formatStateUsing(function ($state, $record) {
                        return Helper::denormalize_phone_number($state);
                    })
                    ->copyable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('status')
                    ->label('وضعیت')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(fn(User $record): bool => $record->status === UserStatusEnum::ACTIVE->value)
                    ->sortable(),

                Tables\Columns\IconColumn::make('validate_status')
                    ->label('وضعیت اعتبارسنجی')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->getStateUsing(
                        fn(User $record): bool => $record->validate_status === ValidateStatusEnum::VALIDATED->value
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime()
                    ->formatStateUsing(function ($state) {
                        return Jalalian::fromDateTime($state)->format('%Y/%m/%d %H:%M');
                    })
                    ->toggleable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('تاریخ بروزرسانی')
                    ->dateTime()
                    ->formatStateUsing(function ($state) {
                        return Jalalian::fromDateTime($state)->format('%Y/%m/%d');
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_online')
                    ->label('Online')
                    ->trueIcon('heroicon-s-light-bulb')
                    ->falseIcon('heroicon-o-light-bulb')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->default(fn(?UserModel $record) => !empty($record) && cache()->has($record->id . '_online'))

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        UserStatusEnum::ACTIVE->value => 'فعال',
                        UserStatusEnum::INACTIVE->value => 'غیرفعال',
                    ]),

                Tables\Filters\SelectFilter::make('validate_status')
                    ->label('وضعیت اعتبارسنجی')
                    ->options([
                        ValidateStatusEnum::VALIDATED->value => 'تایید شده',
                        ValidateStatusEnum::NOT_VALIDATED->value => 'تایید نشده',
                    ]),

                Tables\Filters\TrashedFilter::make()
                    ->label('فقط حذف شده‌ها'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('مشاهده'),

                Tables\Actions\EditAction::make()
                    ->label('ویرایش'),

                Tables\Actions\Action::make('validate')
                    ->label('اعتبارسنجی')
                    //->icon('heroicon-o-badge-check')
                    ->color('warning')
                    ->visible(fn(User $record) => $record->validate_status === ValidateStatusEnum::NOT_VALIDATED->value)
                    ->form([
                        Forms\Components\Checkbox::make('send_sms')
                            ->label('ارسال پیامک به کاربر')
                            ->default(true),
                    ])
                    ->modalHeading('اعتبارسنجی کاربر')
                    ->modalDescription('آیا مایل به اعتبارسنجی این کاربر هستید؟')
                    ->modalSubmitActionLabel('بله، اعتبارسنجی شود')
                    ->extraModalFooterActions([
                        \Filament\Actions\Action::make('cancel')
                            ->label('انصراف')
                            ->color('gray'),
                    ])
                    ->action(function (User $record, array $data): void {
                        // Simulate processing time
                        sleep(2);

                        // Update user validation status
                        $record->validate_status = ValidateStatusEnum::VALIDATED->value;
                        $record->validate_request_at = Carbon::now();
                        $record->save();

                        // Send SMS if requested
                        if ($data['send_sms'] ?? false) {
                            $message = "کاربر گرامی، اعتبارسنجی حساب شما با موفقیت انجام شد.";
                            $smsService = new BaseSmsService();
                            $smsService->voidSend($record->phone_number, $message);
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('موفقیت')
                            ->body('کاربر با موفقیت اعتبارسنجی شد')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('activate')
                    ->label('فعال‌سازی')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(User $record) => $record->status === UserStatusEnum::INACTIVE->value)
                    ->form([
                        Forms\Components\Checkbox::make('send_sms')
                            ->label('ارسال پیامک به کاربر')
                            ->default(true),
                    ])
                    ->modalHeading('فعال‌سازی کاربر')
                    ->modalDescription('آیا مایل به فعال‌سازی این کاربر هستید؟')
                    ->modalSubmitActionLabel('بله، فعال شود')
                    ->extraModalFooterActions([
                        \Filament\Actions\Action::make('cancel')
                            ->label('انصراف')
                            ->color('gray'),
                    ])
                    ->action(function (User $record, array $data): void {
                        $userService = new UserService();
                        $userService->actActivateUser($record->id);
                        if (cache()->has($record->id . '_logout')) {
                            cache()->delete($record->id . '_logout');
                        }
                        if ($userService->hasErrors()) {
                            foreach ($userService->getErrorMessages() as $message) {
                                \Filament\Notifications\Notification::make()
                                    ->title('خطا')
                                    ->body($message)
                                    ->danger()
                                    ->send();
                            }
                        } else {
                            // Send SMS if requested
                            if ($data['send_sms'] ?? false) {
                                $message = "کاربر گرامی، حساب کاربری شما فعال شد.";
                                $smsService = new BaseSmsService();
                                $smsService->voidSend($record->phone_number, $message);
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('موفقیت')
                                ->body('کاربر با موفقیت فعال شد')
                                ->success()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('deactivate')
                    ->label('غیرفعال‌سازی')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(User $record) => $record->status === UserStatusEnum::ACTIVE->value)
                    ->form([
                        Forms\Components\Checkbox::make('send_sms')
                            ->label('ارسال پیامک به کاربر')
                            ->default(true),
                    ])
                    ->modalHeading('غیرفعال‌سازی کاربر')
                    ->modalDescription('آیا مایل به غیرفعال‌سازی این کاربر هستید؟')
                    ->modalSubmitActionLabel('بله، غیرفعال شود')
                    ->extraModalFooterActions([
                        \Filament\Actions\Action::make('cancel')
                            ->label('انصراف')
                            ->color('gray'),
                    ])
                    ->action(function (User $record, array $data): void {
                        $userService = new UserService();
                        $userService->actDeactivateUser($record->id);
                        cache()->set($record->id . '_logout', 'true');
                        if ($userService->hasErrors()) {
                            foreach ($userService->getErrorMessages() as $message) {
                                \Filament\Notifications\Notification::make()
                                    ->title('خطا')
                                    ->body($message)
                                    ->danger()
                                    ->send();
                            }
                        } else {
                            // Send SMS if requested
                            if ($data['send_sms'] ?? false) {
                                $message = "کاربر گرامی، حساب کاربری شما غیرفعال شد.";
                                $smsService = new BaseSmsService();
                                $smsService->voidSend($record->phone_number, $message);
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('موفقیت')
                                ->body('کاربر با موفقیت غیرفعال شد')
                                ->success()
                                ->send();
                        }
                    }),

                Tables\Actions\DeleteAction::make()
                    ->label('حذف')
                    ->using(function (User $record, array $data): void {
                        $userService = new UserService();
                        $userService->actDeleteUser(
                            $record->id,
                            $data['reason'] ?? 'دلیل ارائه نشده',
                            auth()->user()->name ?? 'سیستم'
                        );

                        if ($userService->hasErrors()) {
                            foreach ($userService->getErrorMessages() as $message) {
                                \Filament\Notifications\Notification::make()
                                    ->title('خطا')
                                    ->body($message)
                                    ->danger()
                                    ->send();
                            }
                        }

                        // Send SMS if requested
                        if ($data['send_sms'] ?? false) {
                            $message = "کاربر گرامی، حساب کاربری شما حذف شد.";
                            $smsService = new BaseSmsService();
                            $smsService->voidSend($record->phone_number, $message);
                        }
                    })
                    ->modalHeading('حذف کاربر')
                    ->modalDescription(
                        'آیا مطمئن هستید که می‌خواهید این کاربر را حذف کنید؟ این عمل کاربر را به صورت soft delete حذف می‌کند.'
                    )
                    ->modalSubmitActionLabel('بله، حذف شود')
                    ->extraModalFooterActions([
                        \Filament\Actions\Action::make('cancel')
                            ->label('انصراف')
                            ->color('gray'),
                    ])
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('دلیل حذف')
                            ->placeholder('لطفا دلیل حذف این کاربر را وارد کنید')
                            ->required(),

                        Forms\Components\Checkbox::make('send_sms')
                            ->label('ارسال پیامک به کاربر')
                            ->default(false),
                    ]),

                Tables\Actions\RestoreAction::make()
                    ->visible(fn($record)=>!empty($record->deleted_at))
                    ->tooltip('این کاربر از سیستم حذف شده است٬ شما میتوانید با استفاده از بازیابی این رکورد را بازگردانی کنید٬ مشروط بر اینکه رکورد مشابهی در پایگاه داده موجود نباشد')
                    ->label('بازیابی'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف دسته‌جمعی')
                        ->using(function (Collection $records, array $data): void {
                            $service = new UserService();

                            foreach ($records as $record) {
                                $service->actDeleteUser(
                                    $record->id,
                                    $data['reason'] ?? 'حذف دسته‌جمعی',
                                    auth()->user()->name ?? 'سیستم'
                                );

                                // Send SMS if requested
                                if ($data['send_sms'] ?? false) {
                                    $message = "کاربر گرامی، حساب کاربری شما حذف شد.";
                                    $smsService = new BaseSmsService();
                                    $smsService->voidSend($record->phone_number, $message);
                                }
                            }
                        })
                        ->form([
                            Forms\Components\Textarea::make('reason')
                                ->label('دلیل حذف')
                                ->placeholder('لطفا دلیل حذف این کاربران را وارد کنید')
                                ->required(),

                            Forms\Components\Checkbox::make('send_sms')
                                ->label('ارسال پیامک به کاربران')
                                ->default(false),
                        ]),

                    Tables\Actions\RestoreBulkAction::make()
                        ->label('بازیابی دسته‌جمعی'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
