<?php

namespace Units\StateCity\Common\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Units\StateCity\Common\Models\City;
use Units\StateCity\Common\Models\Province;

class ProvinceCitySeeder extends Seeder
{
    public function run()
    {
        // خواندن داده‌های استان‌ها
        $provincesPath = base_path('Modules/Units/StateCity/Common/data/province.json');
        $provincesJson = File::get($provincesPath);
        $provincesData = json_decode($provincesJson, true);
        foreach ($provincesData as $province) {
            Province::updateOrCreate(
                ['id' => $province['id']], // شرط پیدا کردن رکورد
                ['name' => $province['title']] // داده‌های برای آپدیت یا ایجاد
            );
        }

        $citiesData = json_decode(File::get(base_path('Modules/Units/StateCity/Common/data/cities.json')), true);

        foreach ($citiesData as $city) {
            City::updateOrCreate(
                ['id' => $city['id']], // شرط پیدا کردن رکورد
                [
                    'province_id' => $city['province_id'],
                    'name' => $city['title']
                ]
            );
        }

    }
}

