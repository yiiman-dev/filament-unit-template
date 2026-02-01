<?php

namespace Modules\Basic\Helpers;

use Faker\Provider\Base;

class NationalCodeFakerProvider extends Base
{
    /**
     * Generate a valid Iranian national code (10 digits)
     *
     * @return string
     */
    public function iranianNationalCode(): string
    {
        // Generate 9 random digits
        $nationalCode = '';
        for ($i = 0; $i < 9; $i++) {
            $nationalCode .= $this->generator->numberBetween(0, 9);
        }

        // Calculate check digit
        $checkDigit = $this->calculateCheckDigit($nationalCode);

        return $nationalCode . $checkDigit;
    }

    /**
     * Calculate check digit for Iranian national code
     *
     * @param string $nationalCode First 9 digits of national code
     * @return string Check digit (1 digit)
     */
    private function calculateCheckDigit(string $nationalCode): string
    {
        // Convert to array of integers
        $digits = str_split($nationalCode);

        // Calculate B value
        $B = 0;
        for ($i = 0; $i < 9; $i++) {
            $B += (int)$digits[$i] * (10 - $i);
        }

        // Calculate C value using modulo 11
        $C = $B % 11;

        // Apply validation rules correctly
        if ($C < 2) {
            return (string)$C;
        } else {
            return (string)(11 - $C);
        }
    }

    /**
     * Generate a valid Iranian national code with proper validation
     *
     * @param bool $unique Whether to ensure uniqueness (default: false)
     * @return string
     */
    public function validIranianNationalCode(bool $unique = false): string
    {
        do {
            // Generate 9 random digits
            $digits = [];
            for ($i = 0; $i < 9; $i++) {
                $digits[] = $this->generator->numberBetween(0, 9);
            }

            // Create national code string
            $nationalCode = implode('', $digits);

            // Check if all digits are the same (invalid case)
            if (strlen(count_chars($nationalCode, 3)) === 1) {
                continue;
            }

            // Calculate check digit
            $checkDigit = $this->calculateCheckDigit($nationalCode);

            // Create full 10-digit code
            $fullCode = $nationalCode . $checkDigit;

            // Validate the complete code
            if ($this->validateIranianNationalCode($fullCode)) {
                return $fullCode;
            }
        } while (true);
    }

    /**
     * Validate Iranian national code according to the algorithm
     *
     * @param string $nationalCode 10-digit national code
     * @return bool
     */
    public function validateIranianNationalCode(string $nationalCode): bool
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

        // Calculate C value using modulo 11
        $C = $B % 11;

        // Apply validation rules correctly
        if (($C < 2 && $checkDigit == $C) || ($C >= 2 && $checkDigit == (11 - $C))) {
            return true;
        }

        return false;
    }

    /**
     * Generate unique valid Iranian national code
     *
     * @param array $existingCodes Array of existing codes to avoid
     * @return string
     */
    public function uniqueValidIranianNationalCode(array $existingCodes = []): string
    {
        do {
            $code = $this->validIranianNationalCode();
        } while (in_array($code, $existingCodes));

        return $code;
    }
}
