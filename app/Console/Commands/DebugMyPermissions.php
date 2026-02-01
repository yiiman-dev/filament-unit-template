<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use Units\Auth\My\Models\UserModel as MyUser;

class DebugMyPermissions extends Command
{
    protected $signature = 'perm:debug-my {--user-id=} {--tenant=}';

    protected $description = 'Debug roles/permissions for My panel user with optional tenant scoping.';

    public function handle(): int
    {
        $userId = (int) ($this->option('user-id') ?? 0);
        $tenant = $this->option('tenant');

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

        // Reset permission cache and ensure models are registered
        $config = config('permission');
        /** @var PermissionRegistrar $registrar */
        $registrar = app(PermissionRegistrar::class);
        $registrar->setPermissionClass($config['models']['permission']);
        $registrar->setRoleClass($config['models']['role']);
        $registrar->cacheKey = $config['cache']['key'];
        $registrar->forgetCachedPermissions();

        if (! empty($tenant)) {
            if (function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId($tenant);
                $this->info("Team/Tenant set to: {$tenant}");
            } else {
                $this->warn('Teams not enabled or helper missing.');
            }
        } else {
            $this->warn('No --tenant provided; checks will ignore team scoping.');
        }

        $this->line('--- User Context ---');
        $this->line('Class: ' . get_class($user));
        $this->line('ID: ' . $user->getKey());
        $this->line('Guard on model: ' . ($user->guard_name ?? 'n/a'));
        $this->line('DB connection (getConnectionName): ' . ($user->getConnectionName() ?? 'default'));

        // Basic table diagnostics
        $rolesTable = config('permission.table_names.roles');
        $permsTable = config('permission.table_names.permissions');
        $mhrTable = config('permission.table_names.model_has_roles');
        $mhpTable = config('permission.table_names.model_has_permissions');

        $this->line('--- Tables (connection: my) ---');
        $this->line("Roles rows: " . DB::connection('my')->table($rolesTable)->count());
        $this->line("Permissions rows: " . DB::connection('my')->table($permsTable)->count());

        // Model type expected
        $expectedModelType = $user->getMorphClass();
        $this->line('Expected model_type for pivots: ' . $expectedModelType);

        // Pivot rows for this user
        $mhr = DB::connection('my')->table($mhrTable)
            ->where('model_id', $user->getKey())
            ->where('model_type', $expectedModelType)
            ->get();
        $mhp = DB::connection('my')->table($mhpTable)
            ->where('model_id', $user->getKey())
            ->where('model_type', $expectedModelType)
            ->get();

        $this->line('model_has_roles rows for user: ' . $mhr->count());
        $this->line('model_has_permissions rows for user: ' . $mhp->count());

        // Attached roles including guard/team (use explicit table prefix to avoid ambiguous columns)
        $roles = DB::connection('my')->table($rolesTable)
            ->join($mhrTable, $rolesTable.'.id', '=', $mhrTable.'.role_id')
            ->where($mhrTable.'.model_id', $user->getKey())
            ->where($mhrTable.'.model_type', $expectedModelType)
            ->select([$rolesTable.'.id', $rolesTable.'.name', $rolesTable.'.guard_name', $rolesTable.'.corporate_national_code'])
            ->get();
        $this->table(['id','name','guard','tenant'], $roles->map(fn($r) => [
            $r->id, $r->name, $r->guard_name, $r->corporate_national_code
        ])->toArray());

        // Distinct guards present
        $distinctRoleGuards = DB::connection('my')->table($rolesTable)->select('guard_name')->distinct()->pluck('guard_name')->implode(', ');
        $this->line('Distinct role guard_name values: ' . $distinctRoleGuards);

        // Effective permissions
        $perms = $user->getAllPermissions()->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'guard' => $p->guard_name,
        ]);
        $this->table(['id','permission','guard'], $perms->toArray());

        $this->info('Done. If no roles/permissions are listed, check guard_name and model_type in pivot rows, and tenant scoping.');
        return self::SUCCESS;
    }
}


