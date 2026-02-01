<?php

namespace Units\Settings\Common\database\seeders;

use Illuminate\Database\Seeder;
use Units\Settings\Manage\Models\ManageSettings;

class FinanceRequestSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing finance request settings
        ManageSettings::where('key', 'like', 'finance_request.%')->delete();

        // Create finance request settings using the factory
        $financeSettings = [
            [
                'key' => 'finance_request.amount.min',
                'value' => 100, // 100 billion Rials (minimum amount)
            ],
            [
                'key' => 'finance_request.amount.max',
                'value' => 1000, // 1000 billion Rials (maximum amount)
            ],
            [
                'key' => 'finance_request.repayment_period.min',
                'value' => 1, // 1 months minimum repayment period
            ],
            [
                'key' => 'finance_request.repayment_period.max',
                'value' => 36, // 36 months maximum repayment period
            ],
            [
                'key' => 'finance_request.breathing_period.min',
                'value' => 1, // 1 month minimum breathing period
            ],
            [
                'key' => 'finance_request.breathing_period.max',
                'value' => 36, // 36 months maximum breathing period
            ],
        ];

        foreach ($financeSettings as $setting) {
            ManageSettings::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }

        $this->command->info('Finance Request Settings have been seeded.');
    }
}
