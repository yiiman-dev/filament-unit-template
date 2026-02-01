<?php

namespace Units\Shield\Common;

use Spatie\Permission\PermissionRegistrar;

class ShieldHelper
{
    public static function setConfigToManage()
    {
        static::setConfig('manage');
    }

    public static function setConfigToMy()
    {
        static::setConfig('my');
    }

    public static function setConfigToAdmin()
    {
        static::setConfig('admin');
    }


    public static function setConfig($panelID)
    {
        $config = app()->make('config');
        $config->set(
            'filament-shield',
            require base_path('Modules/Units/Shield/' . str($panelID)->pascal()->toString() . '/config/filament-shield.php')
        );
        $config->set(
            'permission',
            require base_path('Modules/Units/Shield/' . str($panelID)->pascal()->toString() . '/config/permission.php')
        );
        $shield_permission = config('permission');
        $permission = app(PermissionRegistrar::class);
        $permission->setPermissionClass($shield_permission['models']['permission']);
        $permission->setRoleClass($shield_permission['models']['role']);
        $permission->cacheKey = $shield_permission['cache']['key'];
        $permission->pivotRole = $shield_permission['column_names']['role_pivot_key'];
        $permission->pivotPermission = $shield_permission['column_names']['permission_pivot_key'];
        $permission->forgetCachedPermissions();
    }

    public static function getConfig($panel, $key)
    {
        // Load the config file data
        $data = include __DIR__.'/../'.str($panel)->pascal()->toString().'/config/'.str($key)->before('.')->toString().'.php';

        // Parse the dot notation key into parts
        $keyParts = str($key)->after('.')->explode('.');

        // Traverse the nested array structure
        $result = $data;
        foreach ($keyParts as $part) {
            if (is_array($result) && array_key_exists($part, $result)) {
                $result = $result[$part];
            } else {
                return null; // Key not found
            }
        }

        return $result;
    }

    /**
     * سیستم را طوری کانفیگ میکند تا کانفیگ های سیستم پرمیشن و نقش های پنل متقاضی را بشناسد
     * @return void
     */
    public static function switchToMyPanel($corporate_national_id){
        app(PermissionRegistrar::class)->setPermissionClass(
            ShieldHelper::getConfig('my', 'permission.models.permission')
        );
        app(PermissionRegistrar::class)->setRoleClass(
            ShieldHelper::getConfig('my', 'permission.models.role')
        );
        app(PermissionRegistrar::class)->teams=true;
        app(PermissionRegistrar::class)->teamsKey=self::getConfig('my', 'permission.column_names.team_foreign_key');
        setPermissionsTeamId($corporate_national_id);
    }
}
