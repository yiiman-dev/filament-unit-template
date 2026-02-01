<?php

namespace Modules\Basic\Helpers;

use Faker\Provider\Base;

class IranianMobileFakerProvider extends Base
{
    /**
     * Iranian mobile number prefixes (operators and regions)
     */
    private array $mobilePrefixes = [
        // Virtual operators with specific prefixes


        // Old virtual operators (deprecated but still exist)
        '0931',  // Spadana - Old virtual operator
        '0932',  // Talea - Old virtual operator
        '0934',  // TeleKish - Old virtual operator

        // Main operators (Raytel and others)
        '0920',  // Raytel main prefix
        '0921',  // Raytel specific packages
        '0922',  // New Raytel packages available nationwide
        '0930',  // MCI first prefixes
        '0933',  // MCI nationwide coverage
        '0935',  // MCI very common
        '0936',  // MCI various series
        '0937',  // MCI various series
        '0938',  // MCI various series
        '0939',  // MCI various series

        // Regional prefixes (common in different provinces)
        '0901',  // Special packages
        '0902',  // New prefixes with wide support
        '0903',  // New prefixes with wide support
        '0904',  // New prefixes with wide support
        '0905',  // New prefixes with wide support

        // Nationwide coverage prefixes
        '0910',  // New nationwide numbers
        '0911',  // Northern provinces (North)
        '0912',  // Tehran and surrounding areas
        '0913',  // Central areas (Isfahan, etc.)
        '0914',  // Azarbaijan provinces
        '0915',  // Eastern regions (Khorasan, etc.)
        '0916',  // Southwest provinces (Fars, etc.)
        '0917',  // Southern regions (Fars, etc.)
        '0918',  // Western provinces (Kermanshah, etc.)
        '0919',  // Tehran and surrounding areas

        // Additional prefixes for variety
        '0923',  // MCI additional prefix (not listed in original but exists)
        '0924',  // MCI additional prefix (not listed in original but exists)
        '0925',  // MCI additional prefix (not listed in original but exists)
        '0926',  // MCI additional prefix (not listed in original but exists)
        '0927',  // MCI additional prefix (not listed in original but exists)
        '0928',  // MCI additional prefix (not listed in original but exists)
        '0929',  // MCI additional prefix (not listed in original but exists)
    ];

    /**
     * Generate a valid Iranian mobile number
     *
     * @return string
     */
    public function iranianMobileNumber(): string
    {
        // Select a random prefix from our list
        $prefix = $this->generator->randomElement($this->mobilePrefixes);

        // Generate 7 random digits to complete the 11-digit number
        $suffix = '';
        for ($i = 0; $i < 7; $i++) {
            $suffix .= $this->generator->numberBetween(0, 9);
        }

        return $prefix . $suffix;
    }

    /**
     * Generate a unique Iranian mobile number
     *
     * @param array $existingNumbers Array of existing numbers to avoid (optional)
     * @return string
     */
    public function uniqueIranianMobileNumber(array $existingNumbers = []): string
    {
        do {
            $mobileNumber = $this->iranianMobileNumber();
        } while (in_array($mobileNumber, $existingNumbers));

        return $mobileNumber;
    }

    /**
     * Generate a valid Iranian mobile number with proper formatting (09XX XXX XXX XX)
     *
     * @return string
     */
    public function formattedIranianMobileNumber(): string
    {
        $mobile = $this->iranianMobileNumber();

        // Format as 09XX XXX XXX XX
        return substr($mobile, 0, 3) . ' ' . substr($mobile, 3, 3) . ' ' . substr($mobile, 6, 3) . ' ' . substr($mobile, 9, 2);
    }

    /**
     * Generate an Iranian mobile number with specific operator prefix
     *
     * @param string $prefix The operator prefix to use (e.g., '0912')
     * @return string
     */
    public function mobileNumberWithPrefix(string $prefix): string
    {
        // Ensure prefix is valid
        if (!in_array($prefix, $this->mobilePrefixes)) {
            // If prefix is not in our list, use a random one from the same category
            $prefix = $this->generator->randomElement($this->mobilePrefixes);
        }

        // Generate 7 random digits to complete the 11-digit number
        $suffix = '';
        for ($i = 0; $i < 7; $i++) {
            $suffix .= $this->generator->numberBetween(0, 9);
        }

        return $prefix . $suffix;
    }
}
