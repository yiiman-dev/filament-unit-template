<?php
/*
 * Copyright (C) Saman beheshtian, Inc - All Rights Reserved
 * 2025.
 *
 * Author        Saman beheshtian
 * Position      Developer
 * Email         amintado@gmail.com
 * Phone         +989353466620
 * Date          4/7/25, 2:03 PM
 */

if (!function_exists('getReferrerInfo')){

    /**
     * will return function caller info
     * return data is :
     * ```
     * [
     * 'file' => 'No referrer',
     * 'line' => 'No referrer',
     * ]
     *
     * ```
     * @return array|string[]
     */
    function getReferrerInfo()
    {
        // Get the backtrace
        $backtrace = debug_backtrace();

        // Check if there is a calling function in the backtrace
        if (isset($backtrace[1])) {
            $caller = $backtrace[1]; // Caller is at index 1

            // Extract file and line number
            $file = $caller['file'] ?? 'Unknown file';
            $line = $caller['line'] ?? 'Unknown line';

            return [
                'file' => $file,
                'line' => $line,
            ];
        }

        return [
            'file' => 'No referrer',
            'line' => 'No referrer',
        ];
    }
}

