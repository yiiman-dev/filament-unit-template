<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\PermissionRegistrar;
use Units\Auth\My\Models\UserModel as MyUser;
use Units\Shield\My\Models\Role as MyRole;

class RevokeMyTenantAccess extends Command
{
    protected $signature = 'perm:revoke-my {--user-id=} {--tenant=}';

    protected $description = 'Revoke super_admin role for a My user in a specific tenant.';

    public function handle(): int
    {
        $config = app()->make('config');
        $config->set('permission', require base_path('Modules/Units/Shield/My/config/permission.php'));

        $userId = (int) ($this->option('user-id') ?? 0);
        $tenantId = (string) ($this->option('tenant') ?? '');

        if ($userId <= 0 || $tenantId === '') {
            $this->error('Provide --user-id and --tenant');
            return self::FAILURE;
        }

        /** @var MyUser|null $user */
        $user = MyUser::query()->find($userId);
        if (! $user) {
            $this->error("User not found: {$userId}");
            return self::FAILURE;
        }

        /** @var PermissionRegistrar $registrar */
        $registrar = app(PermissionRegistrar::class);
        $registrar->setPermissionClass(config('permission.models.permission'));
        $registrar->setRoleClass(config('permission.models.role'));
        $registrar->cacheKey = config('permission.cache.key');
        $registrar->forgetCachedPermissions();

        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($tenantId);
        }

        // Find the tenant-scoped super_admin role
        $role = MyRole::query()->where([
            'name' => 'super_admin',
            'guard_name' => 'my',
            'corporate_national_code' => $tenantId,
        ])->first();

        if (! $role) {
            $this->warn('No super_admin role found for this tenant. Nothing to revoke.');
            return self::SUCCESS;
        }

        // Detach the role from the user
        $user->removeRole($role);
        $this->info("Revoked super_admin from user {$userId} for tenant {$tenantId}.");

        return self::SUCCESS;
    }
}


