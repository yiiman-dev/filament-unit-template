<?php

namespace Modules\Basic\Helpers;

/**
 * Helper class for Iranian national code validation and generation
 */
class NationalCodeHelper
{
    /**
     * Validate Iranian national code according to the algorithm
     *
     * @param string $nationalCode 10-digit national code
     * @return bool
     */
    public static function validateIranianNationalCode(string $nationalCode): bool
    {
        // Check if code is exactly 10 digits
        if (strlen($nationalCode) !== 10 || !ctype_digit($nationalCode)) {
            return false;
        }

        // Check for invalid patterns (all digits same)
        $uniqueDigits = strlen(count_chars($nationalCode, 3));
        if ($uniqueDigits === 1) {
            return false;
        }

        // Extract the check digit (last digit)
        $checkDigit = (int)$nationalCode[9];

        // Calculate B value
        $B = 0;
        for ($i = 0; $i < 9; $i++) {
            $B += (int)$nationalCode[$i] * (10 - $i);
        }

        // Calculate C value
        $C = $B - (int)floor($B / 11) * 11;

        // Apply validation rules
        if (($C == 0 && $checkDigit == 0) ||
            ($C == 1 && $checkDigit == 1) ||
            ($C > 1 && $checkDigit == (11 - $C))) {
            return true;
        }

        return false;
    }

    /**
     * Generate a valid Iranian national code (10 digits)
     *
     * @param bool $unique Whether to ensure uniqueness (default: false)
     * @return string
     */
    public static function generateValidIranianNationalCode(bool $unique = false): string
    {
        do {
            // Generate 9 random digits
            $digits = [];
            for ($i = 0; $i < 9; $i++) {
                $digits[] = rand(0, 9);
            }

            // Create national code string
            $nationalCode = implode('', $digits);

            // Check if all digits are the same (invalid case)
            if (strlen(count_chars($nationalCode, 3)) === 1) {
                continue;
            }

            // Calculate check digit
            $checkDigit = self::calculateCheckDigit($nationalCode);

            // Create full 10-digit code
            $fullCode = $nationalCode . $checkDigit;

            // Validate the complete code
            if (self::validateIranianNationalCode($fullCode)) {
                return $fullCode;
            }
        } while (true);
    }

    /**
     * Calculate check digit for Iranian national code
     *
     * @param string $nationalCode First 9 digits of national code
     * @return string Check digit (1 digit)
     */
    private static function calculateCheckDigit(string $nationalCode): string
    {
        // Convert to array of integers
        $digits = str_split($nationalCode);

        // Calculate B value
        $B = 0;
        for ($i = 0; $i < 9; $i++) {
            $B += (int)$digits[$i] * (10 - $i);
        }

        // Calculate C value
        $C = $B - (int)floor($B / 11) * 11;

        // Apply validation rules - fallback method
        if ($C >= 0 && $C < 11) {
            return (string)(11 - $C);
        }

        // Fallback calculation
        return (string)((11 - ($B % 11)) % 11);
    }
}
