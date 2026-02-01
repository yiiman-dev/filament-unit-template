<?php

namespace Units\Avatar\Admin;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Units\Auth\Manage\Models\UserModel;

class AdminAvatarProvider implements AvatarProvider
{
    protected $publicPath;
    protected $urlPath;
    protected $fontPath;


    public static function make()
    {
        return new self();
    }

    public function __construct()
    {
        $this->publicPath = public_path('avatars/admin');
        $this->urlPath = '/avatars/admin';
        $this->fontPath = public_path('fonts/shabnam/Shabnam-Bold-FD.ttf'); // Change this to your actual font path

        // Create directory if it doesn't exist
        if (!file_exists($this->publicPath)) {
            mkdir($this->publicPath, 0755, true);
        }
    }


    /**
     * Clear avatar cache for a specific record
     *
     * @param Model|Authenticatable|UserModel $record
     * @return bool
     */
    public function clearCache(Model | Authenticatable | UserModel $record): bool
    {
        // Generate filename based on name and size
        $filename = $this->generateFilename($record->username, 150);
        $filePath = $this->publicPath . '/' . $filename;

        // Delete existing file if it exists
        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return true;
    }

    public function get(Model | Authenticatable | UserModel $record): string
    {
        $profile=$record->getProfile();

        $name = !empty($profile['first_name']) ? $profile['first_name'] :$record->username;

        // Generate filename based on name and size
        $filename = $this->generateFilename($record->username, 150);
        $filePath = $this->publicPath . '/' . $filename;
        $fileUrl = $this->urlPath . '/' . $filename;

        // Check if file already exists
//        if (file_exists($filePath)) {
//            return asset($fileUrl);
//        }

        // Generate the avatar image
        $this->createAvatarImage($name, $filePath, 100);

        return asset($fileUrl);
    }

    protected function generateFilename($name, $size)
    {
        // Create a hash based on name and size to ensure uniqueness
        $hash = md5($name . $size);
        return "avatar_{$hash}_{$size}.png";
    }

    protected function createAvatarImage($name, $filePath, $size = 100)
    {
        // Create larger image for better character display
        $image = imagecreate($size, $size);
        // Set background color (random)
        $bgColor = imagecolorallocate($image, 0, 0, 0);
        imagefilledrectangle($image, 0, 0, $size, $size, $bgColor);

        // Set text color (white for better contrast)
        $textColor = imagecolorallocate($image, 255, 255, 255);

        // Get first character of name (uppercase)
        $firstChar = $this->getFirstCharacter($name);

        // Use TTF font with imagettftext (if available)
        $fontSize = 40; // Large font size
        $fontPath = $this->fontPath; // Your font file

        // Try to use TTF font, fallback to built-in if not available
        if (function_exists('imagettftext') && file_exists($fontPath)) {
            // Calculate text bounding box to center it properly
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $firstChar);

            // Get actual dimensions from bounding box
            $textWidth = abs($bbox[2] - $bbox[0]);
            $textHeight = abs($bbox[5] - $bbox[1]);

            // Calculate center position (correcting for baseline)
            $x = ($size - $textWidth) / 2;
            $y = ($size + $textHeight) / 2-12; // This centers vertically

            // Add text to image using TTF font
            imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontPath, $firstChar);
        } else {
            // Fallback to built-in font if TTF is not available
            $font = 6;
            $textWidth = strlen($firstChar) * 8;
            $textHeight = 14;
            $x = ($size - $textWidth) / 2;
            $y = ($size - $textHeight) / 2 + 12;
            imagestring($image, $font, $x, $y, $firstChar, $textColor);
        }

        // Save image to file
        imagepng($image, $filePath);

        // Free memory
        imagedestroy($image);
    }

    protected function getFirstCharacter($name): string
    {
        // Handle UTF-8 properly to get the first character
        if (function_exists('mb_substr')) {
            return mb_substr($name, 0, 1, 'UTF-8');
        } else {
            // Fallback for systems without mbstring
            return substr($name, 0, 1);
        }
    }
}
