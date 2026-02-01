<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/27/25, 5:53 PM
 */

namespace Units\Users\Admin\Admin\Filament\Resources;

use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Modules\Basic\BaseKit\Filament\Actions\Butttons\CodeConfirmTableAction;
use Modules\Basic\BaseKit\Filament\Components\Table\BadgeColumn;
use Modules\Basic\BaseKit\Filament\Form\Components\MobileInput;
use Modules\Basic\BaseKit\Filament\Form\Components\PasswordInput;
use Modules\Basic\BaseKit\Filament\HasNotification;
use Units\Auth\Admin\Models\UserModel;
use Units\Shield\Admin\Models\Role;
use Units\SMS\Common\Services\BaseSmsService;
use Units\Users\Admin\Admin\Filament\Resources\AdminUsersResource\Pages\CreateAdminUsers;
use Units\Users\Admin\Admin\Filament\Resources\AdminUsersResource\Pages\EditAdminUsers;
use Units\Users\Admin\Admin\Filament\Resources\AdminUsersResource\Pages\ListAdminUsers;
use Units\Users\Admin\Admin\Filament\Resources\AdminUsersResource\Pages\ViewAdminUser;
use Units\Users\Admin\Admin\Services\UserService;

class AdminUsersResource extends Resource
{
    use HasNotification;

    protected static ?string $model = UserModel::class;

    protected static ?string $modelLabel = 'مدیریت کاربر ادمین';

    protected static ?string $pluralModelLabel = 'مدیریت کاربران ادمین';

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('username')
                    ->extraAlpineAttributes(['tabindex' => 1])
                    ->label('نام کاربری')
                    ->unique(UserModel::class, ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'کاربر تکراری است',
                    ])
                    ->extraAlpineAttributes([
                        'style' => 'text-align:left;direction:ltr',
                    ])
                    ->unique(UserModel::class, ignoreRecord: true),
                MobileInput::make('phone_number')
                    ->extraAlpineAttributes(['tabindex' => 2])
                    ->unique(UserModel::class, ignoreRecord: true)
                    ->required(),
                Select::make('status')
                    ->options(
                        [
                            UserService::STATUS_ACTIVE => 'کاربر فعال',
                            UserService::STATUS_DE_ACTIVE => 'غیر فعال (عدم امکان ورود به پنل)',
                        ]
                    )
                    ->disabled(function ($record) {
                        if (! empty($record)) {
                            return true;
                        }
                    }),
                TextInput::make('created_by')
                    ->label('ایجاد شده توسط')
                    ->extraAlpineAttributes([
                        'style' => 'text-align:left;direction:ltr',
                    ])
                    ->disabled(function ($record) {
                        if (! empty($record)) {
                            return true;
                        }
                    })
                    ->helperText(function ($record) {
                        if ($record['created_by'] == $logged_in_user = Filament::auth()->user()->phone_number) {
                            return 'شما این کاربر را ایجاد کردید';
                        }
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('username')
                    ->searchable()
                    ->copyable()
                    ->label('نام کاربری'),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('شماره همراه')
                    ->searchable()
                    ->copyable()
                    ->alignRight()
                    ->extraCellAttributes(['style' => 'direction:ltr']),
                BadgeColumn::make('status')
                    ->label('وضعیت')
                    ->formatStateUsing(fn ($state) => empty($state) ? 'غیر فعال' : 'فعال'),
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
                // Activate Action
                Action::make('activate')
                    ->label('فعال سازی')
                    ->icon('heroicon-o-check-circle')
                    ->color(Color::Green)
                    ->visible(fn ($record) => $record->status === UserService::STATUS_DE_ACTIVE)
                    ->requiresConfirmation()
                    ->form([
                        Checkbox::make('send_sms')
                            ->label('آیا پیامک فعال سازی برای کاربر ارسال شود؟'),
                    ])
                    ->modalHeading('فعال سازی کاربر ادمین')
                    ->modalDescription(function ($record, array $data) {
                        return 'آیا برای فعال سازی مجدد کاربر ادمین با شماره همراه '.$record['phone_number'].' اطمینان دارید؟';
                    })
                    ->disabled(function ($record) {
                        if ($record['phone_number'] == Filament::auth()->user()->phone_number) {
                            return true;
                        }
                        $user_service = resolve(UserService::class);

                        return $user_service->isParent(Filament::auth()->user()->phone_number, $record['phone_number']);
                    })
                    ->action(function ($record, array $data) {
                        $user_service = resolve(UserService::class);
                        $user_service->actActivate(normalized_mobile: $record['phone_number']);
                        if ($user_service->hasErrors()) {
                            Notification::make('error_'.uniqid())
                                ->danger()
                                ->title('')
                                ->body($user_service->getErrorMessages()[0])
                                ->send();

                            return null;
                        }
                        if ((bool) $data['send_sms']) {
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
                    ->disabled(function ($record) {
                        if ($record['phone_number'] == Filament::auth()->user()->phone_number) {
                            return true;
                        }
                        $user_service = resolve(UserService::class);

                        return $user_service->isParent(Filament::auth()->user()->phone_number, $record['phone_number']);
                    })
                    ->visible(fn ($record) => $record->status === UserService::STATUS_ACTIVE)
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
                        return 'غیر فعال سازی حساب کاربری ادمین : '.$record['phone_number'];
                    })
                    ->modalDescription('شما در حال غیرفعال سازی حساب کاربری یک کاربر ادمین هستید. پس از این اقدام٬ کاربر مذکور دیگر نمیتواند به پنل ادمین وارد شود. شما میتوانید کاربران غیر فعال را مجددا فعال سازی کنید.')
                    ->action(function ($record, array $data) {
                        // Prevent deactivate yourself!
                        if (Filament::auth()->user()->phone_number == $record['phone_number']) {
                            Notification::make('error_'.uniqid())
                                ->danger()
                                ->title('')
                                ->body('شما نمیتوانید حساب کاربری خودتان را عیر فعال کنید!!')
                                ->send();

                            return null;
                        }

                        // Prevent Deactivate parent users!!
                        $user_service = resolve(UserService::class);
                        if ($user_service->isParent(Filament::auth()->user()->phone_number, $record['phone_number'])) {
                            Notification::make('error_'.uniqid())
                                ->danger()
                                ->title('')
                                ->body('شما نمیتوانید حساب کاربر ارشد خودتان را غیز فعال کنید!!')
                                ->send();

                            return;
                        }

                        $user_service = resolve(UserService::class);
                        $user_service->actDeactivate($record['phone_number'], $data['reason']);
                        if ($user_service->hasErrors()) {
                            Notification::make('error_'.uniqid())
                                ->danger()
                                ->title('')
                                ->body($user_service->getErrorMessages()[0])
                                ->send();

                            return null;
                        }
                        if ((bool) $data['send_sms']) {
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
                            Notification::make('error_'.uniqid())
                                ->danger()
                                ->title('')
                                ->body('حین تغییر رمز عبور این کاربر مشکلی بوجود آمد :'.$user_service->getErrorMessages()[0])
                                ->send();

                            return null;
                        }

                        // ارسال پیامک در صورت نیاز
                        if ($data['send_sms']) {
                            $sms_service->voidSend(
                                normalized_mobile: $record['phone_number'],
                                message: 'کاربر گرامی رمز عبور شما در پنل ادمین سامانه تامین مالی زنجیره آرین تغییر کرد.'."\n"
                                .'اطلاعات ورود جدید: '
                                .'User name: '.$record['username']."\n"
                                .'Password: '.$data['new_password']."\n"
                                .' تغییر یافته توسط: '.Filament::auth()->user()->phone_number
                            );
                        }
                    })
                    ->hidden(
                        function ($record) {
                            if ($record['phone_number'] == Filament::auth()->user()->phone_number) {
                                return true;
                            }
                            $user_service = resolve(UserService::class);

                            return $user_service->isParent(Filament::auth()->user()->phone_number, $record['phone_number']);
                        }
                    )
                    ->color('primary'),
                // Role Assignment Action
                CodeConfirmTableAction::make('assignRoles', function (array $data, $record) {
                    $user = UserModel::where('phone_number', $record['phone_number'])->first();
                    if (! $user) {
                        Notification::make('error_'.uniqid())
                            ->danger()
                            ->title('خطا')
                            ->body('کاربر مورد نظر یافت نشد.')
                            ->send();

                        return;
                    }

                    // Get current roles
                    $currentRoles = $user->roles->pluck('name')->toArray();

                    // Get selected roles from form
                    $selectedRoles = $data['roles'] ?? [];

                    // Calculate roles to add and remove
                    $rolesToAdd = array_diff($selectedRoles, $currentRoles);
                    $rolesToRemove = array_diff($currentRoles, $selectedRoles);

                    // Check if user will have at least one role after changes
                    $finalRoles = array_diff(array_merge($currentRoles, $rolesToAdd), $rolesToRemove);
                    if (empty($finalRoles)) {
                        Notification::make('error_'.uniqid())
                            ->danger()
                            ->title('خطا')
                            ->body('کاربر باید حداقل یک نقش داشته باشد.')
                            ->send();

                        return;
                    }

                    // Apply role changes
                    try {
                        // Remove roles
                        if (! empty($rolesToRemove)) {
                            $user->removeRole($rolesToRemove);
                        }

                        // Add roles
                        if (! empty($rolesToAdd)) {
                            $user->assignRole($rolesToAdd);
                        }

                        // Send SMS if requested
                        if ($data['send_sms'] ?? false) {
                            $smsService = resolve(BaseSmsService::class);
                            $roleNames = collect($finalRoles)->map(function ($role) {
                                return ucfirst(str_replace('_', ' ', $role));
                            })->join('، ');

                            $message = "کاربر گرامی، نقش‌های دسترسی شما در پنل ادمین به‌روزرسانی شد.\nنقش‌های جدید: ".$roleNames;
                            $smsService->voidSend(
                                normalized_mobile: $record['phone_number'],
                                message: $message
                            );
                        }

                        Notification::make('success_'.uniqid())
                            ->success()
                            ->title('موفق')
                            ->body('نقش‌های کاربر با موفقیت به‌روزرسانی شد.')
                            ->send();

                    } catch (\Exception $e) {
                        Notification::make('error_'.uniqid())
                            ->danger()
                            ->title('خطا')
                            ->body('خطا در به‌روزرسانی نقش‌ها: '.$e->getMessage())
                            ->send();
                    }
                }, [
                    CheckboxList::make('roles')
                        ->label('انتخاب نقش‌ها')
                        ->options(function () {
                            return Role::where('guard_name', 'admin')
                                ->pluck('name', 'name')
                                ->mapWithKeys(function ($name) {
                                    return [$name => ucfirst(str_replace('_', ' ', $name))];
                                })
                                ->toArray();
                        })
                        ->default(function ($record) {
                            $user = UserModel::where('phone_number', $record['phone_number'])->first();

                            return $user ? $user->roles->pluck('name')->toArray() : [];
                        })
                        ->required()
                        ->minItems(1)
                        ->validationMessages([
                            'min_items' => 'کاربر باید حداقل یک نقش داشته باشد.',
                        ])
                        ->columns(2),
                    Checkbox::make('send_sms')
                        ->label('ارسال پیامک اطلاع‌رسانی به کاربر')
                        ->default(false),
                ])
                    ->label('مدیریت نقش‌ها')
                    ->icon('heroicon-o-shield-check')
                    ->color('warning')
                    ->disabled(function ($record) {
                        if ($record['phone_number'] == Filament::auth()->user()->phone_number) {
                            return true;
                        }
                        $user_service = resolve(UserService::class);

                        return $user_service->isParent(Filament::auth()->user()->phone_number, $record['phone_number']);
                    }),

            ]);
        //            ->bulkActions([
        //                Tables\Actions\BulkActionGroup::make([
        //                    Tables\Actions\DeleteBulkAction::make(),
        //                ]),
        //            ]);
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
            'index' => ListAdminUsers::route('/'),
            'create' => CreateAdminUsers::route('/create'),
            'edit' => EditAdminUsers::route('/{record}/edit'),
            'view' => ViewAdminUser::class::route('/{record}/view'),
        ];
    }
}
