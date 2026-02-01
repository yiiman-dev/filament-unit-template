<?php

/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          8/4/25, 2:31 AM
 */

namespace Units\Shield\Common\Console\Commands;

use BezhanSalleh\FilamentShield\FilamentShield;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Config\Repository;
use Illuminate\Console\Command;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class SuperAdminCommand extends Command
{
    public $signature = 'shield:super-admin
        {--user= : ID of user to be made super admin}
        {--user-column= : The column in the user table used to search for the user option value.}
        {--panel= : Panel ID to get the configuration from.}
        {--tenant= : Team/Tenant ID to assign role to user.}
    ';

    public $description = 'Creates Filament Super Admin - Custom on Shield unit';

    protected Authenticatable $superAdmin;

    /** @var ?\Illuminate\Database\Eloquent\Model */
    protected $superAdminRole = null;

    protected function getAuthGuard(): Guard
    {
        $panel = $this->option('panel');
        if ($panel) {
            Filament::setCurrentPanel(Filament::getPanel($panel));
        }

        $auth = Filament::getPanel($panel)?->auth();

        return $auth;
    }

    protected function getUserProvider(): UserProvider
    {
        return $this->getAuthGuard()->getProvider();
    }

    protected function getUserModel(): string
    {
        /** @var EloquentUserProvider $provider */
        $provider = $this->getUserProvider();

        $model = $provider->getModel();

        return $model;
    }

    public function handle(): int
    {
        // Load config files
        // $app= require base_path('bootstrap/app.php');
        /**
         * @var Repository $config
         */
        $config = app()->make('config');
        $panel = 'admin';
        if (! empty($this->option('panel'))) {
            $panel = $this->option('panel');
            Filament::getCurrentPanel()?->authGuard($panel);
        }

        $config->set(
            'filament-shield',
            require base_path('Modules/Units/Shield/'.str($panel)->pascal().'/config/filament-shield.php')
        );
        $config->set(
            'permission',
            require base_path('Modules/Units/Shield/'.str($panel)->pascal().'/config/permission.php')
        );

        $shield_config = config('filament-shield');
        $shield_permission = config('permission');

        $shield_permission = app(PermissionRegistrar::class)->setPermissionClass(
            config('permission.models.permission')
        );
        $shield_permission = app(PermissionRegistrar::class)->setRoleClass(config('permission.models.role'));
        $usersCount = static::getUserModel()::count();
        $tenantId = $this->option('tenant');

        if ($this->option('user')) {
            if (empty($this->option('user-column'))) {
                $this->superAdmin = static::getUserModel()::findOrFail($this->option('user'));
            } else {
                $this->superAdmin = static::getUserModel()::where(
                    $this->option('user-column'),
                    $this->option('user')
                )->firstOrFail();
            }
        } elseif ($usersCount === 1) {
            $this->superAdmin = static::getUserModel()::first();
        } elseif ($usersCount > 1) {
            $this->table(
                ['ID', 'Name', 'Email', 'Roles'],
                static::getUserModel()::with('roles')->get()->map(function (Authenticatable $user) {
                    return [
                        'id' => $user->getKey(),
                        'name' => $user->getAttribute('name'),
                        'email' => $user->getAttribute('email'),
                        /** @phpstan-ignore-next-line */
                        'roles' => implode(',', $user->roles->pluck('name')->toArray()),
                    ];
                })
            );

            $superAdminId = text(
                label: 'Please provide the `UserID` to be set as `super_admin`',
                required: true
            );

            $this->superAdmin = static::getUserModel()::findOrFail($superAdminId);
        } else {
            $this->superAdmin = $this->createSuperAdmin();
        }

        if (Utils::isTenancyEnabled()) {
            if (blank($tenantId)) {
                $this->components->error(
                    'Please provide the team/tenant id via `--tenant` option to assign the super admin to a team/tenant.'
                );

                return self::FAILURE;
            }

            setPermissionsTeamId($tenantId);
            $this->superAdminRole = FilamentShield::createRole(tenantId: $tenantId);
            $this->superAdminRole->syncPermissions(Utils::getPermissionModel()::pluck('id'));
        } else {
            $this->superAdminRole = FilamentShield::createRole();
        }

        $this->superAdmin
            ->unsetRelation('roles')
            ->unsetRelation('permissions');

        $this->superAdmin
            ->assignRole($this->superAdminRole);

        $this->components->info("Success! {$this->superAdmin->email}");

        return self::SUCCESS;
    }

    protected function createSuperAdmin(): Authenticatable
    {
        return static::getUserModel()::create([
            'name' => text(label: 'Name', required: true),
            'email' => text(
                label: 'Email address',
                required: true,
                validate: fn (string $email): ?string => match (true) {
                    ! filter_var($email, FILTER_VALIDATE_EMAIL) => 'The email address must be valid.',
                    static::getUserModel()::where('email', $email)->exists(
                    ) => 'A user with this email address already exists',
                    default => null,
                },
            ),
            'password' => Hash::make(
                password(
                    label: 'Password',
                    required: true,
                    validate: fn (string $value) => match (true) {
                        strlen($value) < 8 => 'The password must be at least 8 characters.',
                        default => null
                    }
                )
            ),
        ]);
    }
}
