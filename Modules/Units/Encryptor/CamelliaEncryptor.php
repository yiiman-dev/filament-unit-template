<?php

namespace Units\Encryptor;

/**
 * Camellia Encryptor Class
 * 
 * Camellia is a block cipher developed jointly by Mitsubishi Electric and NTT of Japan.
 * It's widely used in Japanese government and financial sectors, and is part of several international standards.
 * 
 * Pros:
 * - High security level (comparable to AES)
 * - Fast performance in both software and hardware
 * - Patented but freely available for use
 * - Supports multiple key sizes (128, 192, 256 bits)
 * - Used in many international standards
 * 
 * Cons:
 * - Less widespread adoption than AES
 * - Fewer implementations available
 * - Less hardware acceleration support
 * - Developed in Japan (regulatory considerations)
 * 
 * Definition:
 * Camellia is a 128-bit block cipher that supports 128-bit, 192-bit, and 256-bit keys.
 * It uses a Feistel network structure with 18 or 24 rounds depending on key size.
 * The algorithm is based on the design principles of E2 and incorporates improved diffusion properties.
 */
class CamelliaEncryptor
{
    private $cipher = 'CAMELLIA-256-CBC';
    private $key;
    
    public function __construct($key = null)
    {
        $this->key = $key;
        if ($this->key === null) {
            // Fallback to a default key if config is not available
            $this->key = env('APP_KEY', 'fallback-test-key-camellia-1234567890');
        }
        // Ensure key is properly sized for Camellia (32 bytes for 256-bit key)
        $this->key = substr(hash('sha256', $this->key), 0, 32);
    }
    
    /**
     * Encrypt data using Camellia algorithm
     *
     * @param string $data
     * @return string
     */
    public function encrypt(string $data): string
    {
        $iv = random_bytes(16); // Camellia uses 128-bit (16 byte) IV
        $encrypted = openssl_encrypt($data, $this->cipher, $this->key, 0, $iv);
        
        if ($encrypted === false) {
            throw new \Exception('Camellia encryption failed');
        }
        
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * Decrypt data using Camellia algorithm
     *
     * @param string $encryptedData
     * @return string
     */
    public function decrypt(string $encryptedData): string
    {
        $data = base64_decode($encryptedData);
        $iv = substr($data, 0, 16); // Extract 16-byte IV
        $encrypted = substr($data, 16);
        
        $decrypted = openssl_decrypt($encrypted, $this->cipher, $this->key, 0, $iv);
        
        if ($decrypted === false) {
            throw new \Exception('Camellia decryption failed');
        }
        
        return $decrypted;
    }
    
    /**
     * Encrypt with different key sizes
     *
     * @param string $data
     * @param int $keySize 128, 192, or 256 bits
     * @return string
     */
    public function encryptWithKeySize(string $data, int $keySize = 256): string
    {
        $validSizes = [128, 192, 256];
        if (!in_array($keySize, $validSizes)) {
            throw new \Exception('Key size must be 128, 192, or 256 bits');
        }
        
        $cipher = "CAMELLIA-{$keySize}-CBC";
        $keyBytes = $keySize / 8;
        $key = substr(hash('sha256', $this->key), 0, $keyBytes);
        
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, $cipher, $key, 0, $iv);
        
        if ($encrypted === false) {
            throw new \Exception("Camellia encryption with {$keySize}-bit key failed");
        }
        
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * Decrypt with different key sizes
     *
     * @param string $encryptedData
     * @param int $keySize 128, 192, or 256 bits
     * @return string
     */
    public function decryptWithKeySize(string $encryptedData, int $keySize = 256): string
    {
        $validSizes = [128, 192, 256];
        if (!in_array($keySize, $validSizes)) {
            throw new \Exception('Key size must be 128, 192, or 256 bits');
        }
        
        $cipher = "CAMELLIA-{$keySize}-CBC";
        $keyBytes = $keySize / 8;
        $key = substr(hash('sha256', $this->key), 0, $keyBytes);
        
        $data = base64_decode($encryptedData);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        
        $decrypted = openssl_decrypt($encrypted, $cipher, $key, 0, $iv);
        
        if ($decrypted === false) {
            throw new \Exception("Camellia decryption with {$keySize}-bit key failed");
        }
        
        return $decrypted;
    }
    
    /**
     * Check if Camellia cipher is available
     *
     * @return bool
     */
    public static function isAvailable(): bool
    {
        $availableCiphers = openssl_get_cipher_methods();
        foreach ($availableCiphers as $cipher) {
            if (stripos($cipher, 'camellia') !== false) {
                return true;
            }
        }
        return false;
    }
}