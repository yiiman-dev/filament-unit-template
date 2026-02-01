<?php


use Illuminate\Database\Seeder;
use Units\Corporates\FieldOfActivity\Common\database\seeders\FieldOfActivitySeeder;
use Units\DocumentConditionTemplate\Common\database\seeders\DocumentConditionTemplateSeeder;
use Units\DocumentTypes\Common\database\seeders\DocumentTypeSeeder;
use Units\Financier\FinancierType\Common\seeders\FinancierTypeSeeder;
use Units\StateCity\Common\database\seeders\ProvinceCitySeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
//        $this->call(FilamentMyDatabaseSeeder::class);
//        $this->call(FilamentManageDatabaseSeeder::class);
//        $this->call(FilamentAdminDatabaseSeeder::class);
        $this->call(FieldOfActivitySeeder::class);
        $this->call(FinancierTypeSeeder::class);
        $this->call(ProvinceCitySeeder::class);
        $this->call(\Units\Financier\FinancingMode\Common\database\seeders\FinancingModeSeeder::class);
        $this->call(\Units\Settings\Common\database\seeders\SettingsDatabaseSeeder::class);
        $this->call(\Units\MemorandumTemplates\Common\database\seeder\MemorandumTemplateSeeder::class);
        $this->call(DocumentTypeSeeder::class);
        $this->call(DocumentConditionTemplateSeeder::class);
//        $this->call(\Units\Invoice\Common\database\seeders\InvoiceSeeder::class);
    }
}
