<?php

namespace Units\Settings\Common\database\seeders;

use Illuminate\Database\Seeder;
use Units\Settings\Manage\Models\ManageSettings;

class FinancialSettingsSeeder extends Seeder
{
    public function run()
    {
        ManageSettings::where('key', 'like', 'financial.%')->delete();
        $financialSettings = [
            [
                'key' => 'financial.vat_percentage',
                'value' => 10, // 10%
            ]
        ];


        foreach ($financialSettings as $setting) {
            ManageSettings::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}
