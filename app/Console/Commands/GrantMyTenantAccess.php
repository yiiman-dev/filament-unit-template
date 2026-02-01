<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\PermissionRegistrar;
use Units\Auth\My\Models\UserModel as MyUser;
use Units\Shield\My\Models\Permission as MyPermission;
use Units\Shield\My\Models\Role as MyRole;

class GrantMyTenantAccess extends Command
{
       protected $signature = 'perm:grant-my {--user-id=} {--tenant=*} {--all-tenants} {--from-role-id=}';

    protected $description = 'Grant full access (role + all permissions) to a My user for given tenant(s).';

    public function handle(): int
    {
        // Force permission config to My panel definitions like MyShieldPlugin does
        $config = app()->make('config');
        $config->set('permission', require base_path('Modules/Units/Shield/My/config/permission.php'));

        $userId = (int) ($this->option('user-id') ?? 0);
        $tenantIds = (array) $this->option('tenant');
        $all = (bool) $this->option('all-tenants');

        if ($userId <= 0) {
            $this->error('Provide --user-id=<id>');
            return self::FAILURE;
        }

        /** @var MyUser|null $user */
        $user = MyUser::query()->find($userId);
        if (! $user) {
            $this->error("User not found: {$userId}");
            return self::FAILURE;
        }

        // Ensure Shield models are registered & cache cleared
        /** @var PermissionRegistrar $registrar */
        $registrar = app(PermissionRegistrar::class);
        $registrar->setPermissionClass(config('permission.models.permission'));
        $registrar->setRoleClass(config('permission.models.role'));
        $registrar->cacheKey = config('permission.cache.key');
        $registrar->forgetCachedPermissions();

        if ($all) {
            $tenantIds = $user->getTenantIds();
            if (empty($tenantIds)) {
                $this->warn('User has no tenants.');
            }
        }

        if (empty($tenantIds)) {
            $this->error('Provide at least one --tenant or use --all-tenants');
            return self::FAILURE;
        }

        foreach ($tenantIds as $tenantId) {
            if (function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId($tenantId);
            }

            $fromRoleId = $this->option('from-role-id');
            if ($fromRoleId) {
                // Mirror permissions from an existing DB role id within schema my
                /** @var MyRole|null $sourceRole */
                $sourceRole = MyRole::query()->where('id', $fromRoleId)->first();
                if (! $sourceRole) {
                    $this->error("Source role not found: {$fromRoleId}");
                    return self::FAILURE;
                }
                /** @var MyRole $role */
                $role = MyRole::query()->firstOrCreate([
                    'name' => $sourceRole->name,
                    'corporate_national_code' => $tenantId,
                    'guard_name' => 'my',
                ]);
                $role->syncPermissions($sourceRole->permissions()->pluck('id'));
                $user->assignRole($role);
            } else {
                // Ensure a role exists for this tenant with guard 'my' and full permissions
                /** @var MyRole $role */
                $role = MyRole::query()->firstOrCreate(
                    [
                        'name' => 'super_admin',
                        'corporate_national_code' => $tenantId,
                        'guard_name' => 'my',
                    ],
                    []
                );
                $permissions = MyPermission::query()->where('guard_name', 'my')->get();
                $role->syncPermissions($permissions);
                $user->assignRole($role);
            }

            $this->info("Granted full access for tenant: {$tenantId}");
        }

        return self::SUCCESS;
    }
}


