<?php

namespace Units\Settings\Common\database\seeders;

use Illuminate\Database\Seeder;

class SettingsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            FinanceRequestSettingsSeeder::class,
        ]);
    }
}
