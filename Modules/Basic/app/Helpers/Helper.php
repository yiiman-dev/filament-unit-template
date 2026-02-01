<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/12/25, 12:26 PM
 */

namespace Modules\Basic\Helpers;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Modules\Basic\BaseKit\Filament\HasNotification;
use Modules\Basic\BaseKit\Filament\InteractWithCorporate;
use Units\Corporates\Placed\Common\Models\CorporateModel;
use Units\Corporates\Users\Common\Models\CorporateUsersModel;

class Helper
{
    use InteractWithCorporate;
    use HasNotification;

    const MOBILE_REGEX = '/^\+[1-9][0-9]{6,14}$/';


    public static function make()
    {
        return (new static);
    }

    /**
     * هر نوع شماره همراهی به آن داده شود٬ به اول آن کد کشور را می افزاید و خروجی را بازگردانی میکند
     * 09353466620 => +989353466620
     * @param string $phone_number
     * @param string $country_code
     * @param string $starter
     * @return string|null
     */
    public static function normalize_phone_number(
        string|null $phone_number,
        string $country_code = '+98',
        string $starter = '9'
    ): string|null {
        if (empty($phone_number)) {
            return $phone_number;
        }


        switch (strlen($phone_number)) {
            case 10:
                if ($phone_number[0] == $starter && is_numeric($phone_number)) {
                    return $country_code . $phone_number;
                }
                break;
            case 11:
                if (substr($phone_number, 0, 2) == '0' . $starter && is_numeric($phone_number)) {
                    return $country_code . substr($phone_number, 1);
                }
                break;
            case 12:
                if ('+' . substr($phone_number, 0, 3) == $country_code . $starter && is_numeric($phone_number)) {
                    return $country_code . substr($phone_number, 2);
                }
                break;
            case 13:
                if (substr($phone_number, 0, 4) == $country_code . $starter && is_numeric(substr($phone_number, 1))) {
                    return $phone_number;
                }
                break;
            case 14:
                if (substr($phone_number, 0, 5) == '00' . str_replace('+', '', $country_code) . $starter && is_numeric(
                        $phone_number
                    )) {
                    return '+' . substr($phone_number, 2);
                }
                break;
        }

        return $phone_number;
    }


    /**
     * Get the absolute path for a unit
     *
     * @param string $name Unit name
     * @param string $path Additional path to append
     * @return string Absolute path
     */
    public static function unit_path(string $name, string $path = ''): string
    {
        $basePath = app()->basePath('Modules/Units/' . $name);
        return $path ? $basePath . DIRECTORY_SEPARATOR . $path : $basePath;
    }


    public static function denormalize_phone_number(
        $phone_number = null,
        string $country_code = '+98',
        string $starter = '9'
    ) {
        if (empty($phone_number)) {
            return $phone_number;
        }
        // Remove spaces and dashes for safety
        $phone_number = str_replace([' ', '-'], '', $phone_number);

        // If the number starts with the country code, replace it with '0'
        if (strpos($phone_number, $country_code) === 0) {
            // Example: +989353466620 => 09353466620
            return '0' . substr($phone_number, strlen($country_code));
        }

        // If it starts with '00' + country code (international format)
        $country_code_numeric = str_replace('+', '', $country_code);
        if (strpos($phone_number, '00' . $country_code_numeric) === 0) {
            // Example: 00989353466620 => 09353466620
            return '0' . substr($phone_number, strlen('00' . $country_code_numeric));
        }

        // If it already starts with '0' + starter and is 11 digits, return as is
        if (substr($phone_number, 0, 2) == '0' . $starter && strlen($phone_number) == 11) {
            return $phone_number;
        }

        // If it starts with just the starter (for 10-digit numbers), add '0'
        if (substr($phone_number, 0, 1) == $starter && strlen($phone_number) == 10) {
            return '0' . $phone_number;
        }

        // If it starts with '+' and is not the expected country code, replace '+' with '0'
        if (substr($phone_number, 0, 1) == '+' && strlen($phone_number) > 1) {
            return '0' . substr($phone_number, 1);
        }

        // If it starts with '00' and is not the expected country code, replace '00' with '0'
        if (substr($phone_number, 0, 2) == '00' && strlen($phone_number) > 2) {
            return '0' . substr($phone_number, 2);
        }

        // Otherwise, return as is (or null if you want strictness)
        return $phone_number;
    }

    /**
     * Register views for a specific panel and its units
     *
     * @param string $panel The panel name (e.g. 'Admin', 'admin', 'ADMIN')
     * @param bool $forceRefresh Force refresh the cache
     * @return array Array of registered view paths
     */
    public static function registerUnitViews(string $panel, bool $forceRefresh = false): array
    {
        $cacheKey = 'unit_views_' . strtolower($panel);

        if (!$forceRefresh && cache()->has($cacheKey)) {
            return cache()->get($cacheKey);
        }

        $basePath = app()->basePath('Modules/Units');
        $panel = strtolower($panel);
        $viewPaths = [];

        // Find all unit directories
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($basePath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isDir() && self::isUnitDirectory($fileinfo)) {
                $unitPath = $fileinfo->getPathname();
                $unitName = basename($unitPath);

                // Check if this unit belongs to the specified panel
                if (self::isUnitInPanel($unitPath, $panel)) {
                    $viewPath = $unitPath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views';
                    if (is_dir($viewPath)) {
                        $viewPaths[] = [
                            'path' => $viewPath,
                            'name' => strtolower($unitName),
                            'hash' => self::generateViewHash($viewPath)
                        ];
                    }
                }
            }
        }

        // Cache the results
        cache()->put($cacheKey, $viewPaths, now()->addHours(24));

        return $viewPaths;
    }

    /**
     * Check if a unit belongs to a specific panel
     *
     * @param string $unitPath The unit's base path
     * @param string $panel The panel name in lowercase
     * @return bool True if the unit belongs to the panel
     */
    private static function isUnitInPanel(string $unitPath, string $panel): bool
    {
        $panelPath = $unitPath . DIRECTORY_SEPARATOR . 'Panels' . DIRECTORY_SEPARATOR . ucfirst($panel);
        return is_dir($panelPath);
    }

    /**
     * Generate a hash for a view directory to detect changes
     *
     * @param string $viewPath The view directory path
     * @return string MD5 hash of all view files
     */
    private static function generateViewHash(string $viewPath): string
    {
        if (!is_dir($viewPath)) {
            return '';
        }

        $hash = '';
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($viewPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $hash .= md5_file($file->getPathname());
            }
        }

        return md5($hash);
    }

    /**
     * Check if views have changed and need to be refreshed
     *
     * @param string $panel The panel name
     * @return bool True if views need to be refreshed
     */
    public static function shouldRefreshViews(string $panel): bool
    {
        $cacheKey = 'unit_views_' . strtolower($panel);
        if (!cache()->has($cacheKey)) {
            return true;
        }

        $cachedPaths = cache()->get($cacheKey);
        foreach ($cachedPaths as $path) {
            $currentHash = self::generateViewHash($path['path']);
            if ($currentHash !== $path['hash']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all registered view paths for a panel
     *
     * @param string $panel The panel name
     * @return array Array of view paths
     */
    public static function getUnitViewPaths(string $panel): array
    {
        $paths = self::registerUnitViews($panel);
        return array_column($paths, 'path');
    }

    /**
     * Register views for a panel and load them into the view system
     *
     * @param string $panel The panel name
     * @return void
     */
    public static function loadUnitViews(string $panel): void
    {
        if (self::shouldRefreshViews($panel)) {
            self::registerUnitViews($panel, true);
        }

        $paths = self::getUnitViewPaths($panel);
        foreach ($paths as $path) {
            if (is_dir($path)) {
                app('view')->addLocation($path);
            }
        }
    }

    /**
     * Check if a directory is a unit directory
     *
     * @param \SplFileInfo $fileinfo The directory to check
     * @return bool True if the directory is a unit directory
     */
    private static function isUnitDirectory(\SplFileInfo $fileinfo): bool
    {
        if (!$fileinfo->isDir()) {
            return false;
        }

        $path = $fileinfo->getPathname();
        $name = $fileinfo->getFilename();

        // Check if directory name starts with a capital letter (PascalCase)
        if (!preg_match('/^[A-Z]/', $name)) {
            return false;
        }

        // Check if it has a resources/views directory
        if (!is_dir($path . '/resources/views')) {
            return false;
        }

//        // Check if it has a Panels directory
//        if (!is_dir($path . '/Panels')) {
//            return false;
//        }

        return true;
    }

    public static function migrationConnection($connection)
    {
        if (env('APP_ENV') == 'testing') {
            return 'test_' . $connection;
        } else {
            return $connection;
        }
    }

    /**
     * دریافت بنگاهی که کاربر در پنل مای در آن لاگین کرده است
     * @return \corporate_national_code|\Illuminate\Container\TClass|mixed|object|null
     */
    public static function current_user_corporate_national_code()
    {
        return static::make()->getCorporateNationalCode();
    }

    /**
     * @return CorporateModel
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     */
    public static function getMyPanelCurrentCorporate(): CorporateModel
    {
        return Filament::getTenant();
    }

    /**
     * @return CorporateUsersModel
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function getMyPanelCurrentCorporateUsers(): CorporateUsersModel
    {
        return request()->session()->get('corporate_users');
    }

    /**
     * This will change current corporate on session
     * @return void
     */
    public static function setCurrentCorporate($corporate_national_code)
    {
        session()->put('active_corporate_national_code', $corporate_national_code);
    }

    public static function getCurrentUserMobile()
    {
        return auth()->user()->phone_number;
    }

    /**
     * Calculate the amount that represents a given percentage of a total amount
     *
     * @param float $percentage The percentage to calculate (e.g., 20 for 20%)
     * @param float $amount The total amount to calculate the percentage of
     * @return float The calculated amount representing the percentage
     */
    public function calculatePercentage($percentage, $amount)
    {
        // Validate inputs
        if (!is_numeric($percentage) || !is_numeric($amount)) {
            throw new \InvalidArgumentException('Both percentage and amount must be numeric values');
        }

        if ($percentage < 0 || $amount < 0) {
            throw new \InvalidArgumentException('Percentage and amount must be non-negative values');
        }

        // Calculate and return the percentage amount
        return ($percentage / 100) * $amount;
    }

    /**
     * Pad an ID with leading zeros to reach specified length
     *
     * @param mixed $id The ID to pad
     * @param int $length The target length (default 10)
     * @return string The padded ID
     */
    static function pad_id_to_length($id, int $length = 10): string
    {
        $id = (string)$id;
        return str_pad($id, $length, '0', STR_PAD_LEFT);
    }


    /**
     * Normalize an ID to 10 digits with leading zeros
     *
     * @param mixed $national_code The ID to normalize
     * @return string The normalized ID
     */
    static function normalize_user_national_code(string $national_code): string
    {
        return static::pad_id_to_length($national_code, 10);
    }

    static function isMyPanel():bool{
        return filament()->getCurrentPanel()->getId()==='my';
    }

    static function isManagePanel():bool
    {
        return filament()->getCurrentPanel()->getId()==='manage';
    }

    static function isAdminPanel():bool
    {
        return filament()->getCurrentPanel()->getId()==='admin';
    }



    public static function colorText($text,$color='green',$direction='rtl')
    {
        return '<div style="color: '.$color.'; direction:'.$direction.'">'.$text.'</div>';
    }
}

