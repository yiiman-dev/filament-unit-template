<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/27/25, 6:11 PM
 */

namespace Units\Users\Admin\Manage\Filament\Resources;

use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Modules\Basic\BaseKit\Filament\Form\Components\PasswordInput;
use Units\SMS\Common\Services\BaseSmsService;
use Units\Users\Admin\Manage\Filament\Resources\UserResource\Pages\CreateUser;
use Units\Users\Admin\Manage\Filament\Resources\UserResource\Pages\EditUser;
use Units\Users\Admin\Manage\Filament\Resources\UserResource\Pages\ListUsers;
use Units\Users\Admin\Manage\Models\User;
use Units\Users\Admin\Manage\Services\UserService;

/**
 * Resource فیلامنت برای مدل User
 * این Resource از مدل User که از APIModel ارث‌بری می‌کند استفاده می‌کند
 *
 */
class UserResource extends Resource
{
    protected static ?string $slug = '/manage-users';
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'مدیریت کاربران تامین مالی';

    protected static ?string $modelLabel = 'کاربر';

    protected static ?string $pluralModelLabel = 'کاربران';


    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('username')
                    ->label('نام کاربری'),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('شماره همراه')
                    ->alignRight()
                    ->extraCellAttributes(['style' => 'direction:ltr'])
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->disabled(function ($record) {
                        $user_service = resolve(UserService::class);
                        return $user_service->isParent(Filament::auth()->user()->phone_number, $record['phone_number']);
                    }),
                Tables\Actions\ViewAction::make(),
                //Activate Action
                Action::make('activate')
                    ->label('فعال سازی')
                    ->icon('heroicon-o-check-circle')
                    ->color(Color::Green)
                    ->visible(fn($record) => isset($record->status) && $record->status === UserService::STATUS_DE_ACTIVE)
                    ->requiresConfirmation()
                    ->form([
                        Checkbox::make('send_sms')
                            ->label('آیا پیامک فعال سازی برای کاربر ارسال شود؟'),
                    ])
                    ->modalHeading('فعال سازی کاربر ادمین')
                    ->modalDescription(function ($record, array $data) {
                        return 'آیا برای فعال سازی مجدد کاربر ادمین با شماره همراه ' . $record['phone_number'] . ' اطمینان دارید؟';
                    })

                    ->action(function ($record, array $data) {
                        $user_service = resolve(UserService::class);
                        $user_service->actActivate(normalized_mobile: $record['phone_number']);
                        if ($user_service->hasErrors()) {
                            Notification::make('error_' . uniqid())
                                ->danger()
                                ->title('')
                                ->body($user_service->getSuccessResponse()[0])
                                ->send();
                            return null;
                        }
                        if ((bool)$data['send_sms']) {
                            $sms_service = resolve(BaseSmsService::class);
                            $sms_service->voidSend(
                                normalized_mobile: $record['phone_number'],
                                message: 'کاربر گرامی٬ حساب کاربری مدیریت شما در سامانه ی تامین مالی زنجیره ای آرین بازگشایی شد.'
                            );
                        }
                    }),
                // Deactivate Action
                Action::make('deactivate')
                    ->label('غیر فعال سازی')
                    ->icon('heroicon-o-x-circle')
                    ->color(Color::Red)

                    ->visible(fn($record) => $record->status === UserService::STATUS_ACTIVE)
                    ->form([
                        TextInput::make('reason')
                            ->label('دلیل غیرفعال سازی کاربر')
                            ->required()
                            ->placeholder('لطفا دلیل غیرفعال سازی کاربر را درج فرمایید'),
                        Checkbox::make('send_sms')
                            ->label('دلیل غیر فعال سازی برای کاربر پیامک شود'),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading(function ($record, array $data) {
                        return 'غیر فعال سازی حساب کاربری ادمین : ' . $record['phone_number'];
                    })
                    ->modalDescription('شما در حال غیرفعال سازی حساب کاربری یک کاربر ادمین هستید. پس از این اقدام٬ کاربر مذکور دیگر نمیتواند به پنل ادمین وارد شود. شما میتوانید کاربران غیر فعال را مجددا فعال سازی کنید.')
                    ->action(function ($record, array $data) {
                        //Prevent deactivate yourself!
                        if (Filament::auth()->user()->phone_number == $record['phone_number']) {
                            Notification::make('error_' . uniqid())
                                ->danger()
                                ->title('')
                                ->body('شما نمیتوانید حساب کاربری خودتان را عیر فعال کنید!!')
                                ->send();
                            return null;
                        }


                        //Prevent Deactivate parent users!!
                        $user_service = resolve(UserService::class);
                        if ($user_service->isParent(Filament::auth()->user()->phone_number, $record['phone_number'])) {
                            Notification::make('error_' . uniqid())
                                ->danger()
                                ->title('')
                                ->body('شما نمیتوانید حساب کاربر ارشد خودتان را غیز فعال کنید!!')
                                ->send();
                            return;
                        }


                        $user_service = resolve(UserService::class);
                        $user_service->actDeactivate($record['phone_number'], $data['reason']);
                        if ($user_service->hasErrors()) {
                            Notification::make('error_' . uniqid())
                                ->danger()
                                ->title('')
                                ->body($user_service->getSuccessResponse()[0])
                                ->send();
                            return null;
                        }
                        if ((bool)$data['send_sms']) {
                            $sms_service = resolve(BaseSmsService::class);
                            $sms_service->voidSend(
                                normalized_mobile: $record['phone_number'],
                                message: $data['reason']
                            );
                        }
                    }),
                Action::make('setNewPassword')
                    ->label('تعیین رمز عبور جدید')
                    ->icon('heroicon-o-key')
                    ->modalHeading('تعیین رمز عبور جدید برای کاربر')
                    ->modalDescription('لطفاً رمز عبور جدید را وارد کنید. در صورت تمایل می‌توانید اطلاع‌رسانی پیامکی نیز انجام دهید.')
                    ->form([
                        PasswordInput::make('new_password')
                            ->label('رمز عبور جدید')
                            ->copyable()
                            ->regeneratePassword()
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->helperText('رمز عبور باید حداقل ۸ کاراکتر باشد.'),
                        Checkbox::make('send_sms')
                            ->default(1)
                            ->label('ارسال رمز عبور از طریق پیامک به کاربر'),
                    ])
                    ->action(function (array $data, $record) {
                        $user_service = app(UserService::class);
                        $sms_service = app(BaseSmsService::class);
                        $user_service->actChangePassword($record['phone_number'], $data['new_password']);
                        if ($user_service->hasErrors()) {
                            Notification::make('error_' . uniqid())
                                ->danger()
                                ->title('')
                                ->body('حین تغییر رمز عبور این کاربر مشکلی بوجود آمد :' . $user_service->getErrorMessages()[0])
                                ->send();
                            return null;
                        }

                        // ارسال پیامک در صورت نیاز
                        if ($data['send_sms']) {
                            $sms_service->voidSend(
                                normalized_mobile: $record['phone_number'],
                                message: 'کاربر گرامی رمز عبور شما در پنل ادمین سامانه تامین مالی زنجیره آرین تغییر کرد.' . "\n"
                                . "اطلاعات ورود جدید: "
                                . "User name: " . $record['username'] . "\n"
                                . "Password: " . $data['new_password']."\n"
                                ." تغییر یافته توسط: ".Filament::auth()->user()->phone_number
                            );
                        }
                    })
                    ->color('primary')

            ]);
//            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
//            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
