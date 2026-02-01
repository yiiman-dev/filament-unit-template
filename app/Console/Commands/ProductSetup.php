<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Units\Auth\My\Models\UserModel;

class ProductSetup extends Command
{
    public $signature = 'product:setup';

    /**
     * این دستور مجوز های شیلد را برای هر یک از پنل ها به بانک داده تزریق میکند تا کاربران دسترسی به صفحات را داشته باشند
     */
    public function handle(): int
    {
        $this->generateAdminPermissions();
        $this->generateManagePermissions();
        $this->generateMyPermissions();

        // Grant full access to all My users for their tenants
        UserModel::query()->each(function (UserModel $user) {
            $user->grantFullAccessForAllTenants();
        });

        return 0;
    }

    public function generateManagePermissions()
    {
        // --ignore-existing-policies
        Artisan::call('shield:generate --all --panel=manage --ignore-existing-policies ');
    }

    public function generateAdminPermissions()
    {
        Artisan::call('shield:generate --all --panel=admin --ignore-existing-policies ');
    }

    public function generateMyPermissions()
    {
        Artisan::call('shield:generate --all --panel=my --ignore-existing-policies --relationships');
    }
}
