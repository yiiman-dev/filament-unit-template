<?php


if (!function_exists('manage_setting')) {
    function manage_setting(string|array $key = '*', mixed $default = null): mixed
    {
        if (is_array($key)) {
            $settings = [];

            foreach ($key as $k => $v) {
                data_set($settings, $k, \Units\Settings\Manage\Models\ManageSettings::set($k, $v));
            }

            return $settings;
        }

        return \Units\Settings\Manage\Models\ManageSettings::get($key, $default);
    }
}


if (!function_exists('admin_setting')) {
    function admin_setting(string|array $key = '*', mixed $default = null): mixed
    {
        if (is_array($key)) {
            $settings = [];

            foreach ($key as $k => $v) {
                data_set($settings, $k, \Units\Settings\Manage\Models\AdminSettings::set($k, $v));
            }

            return $settings;
        }

        return \Units\Settings\Manage\Models\AdminSettings::get($key, $default);
    }
}
