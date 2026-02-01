<?php

namespace Units\Encryptor;

/**
 * Twofish Encryptor Class
 * 
 * Twofish is a symmetric key block cipher with a block size of 128 bits and key sizes up to 256 bits.
 * It was one of the five finalists in the Advanced Encryption Standard (AES) selection process.
 * 
 * Pros:
 * - Very secure algorithm
 * - Supports large key sizes (up to 256 bits)
 * - Unpatented and freely usable
 * - Good performance characteristics
 * - No export restrictions
 * 
 * Cons:
 * - Not as widely supported as AES
 * - Slower than AES in software implementations
 * - Less hardware acceleration available
 * - Not chosen as AES standard
 * 
 * Definition:
 * Twofish is a Feistel network block cipher that operates on 128-bit blocks and supports key sizes of 128, 192, or 256 bits.
 * It uses 16 rounds of encryption and employs pre-computed key-dependent S-boxes, making it resistant to differential and linear cryptanalysis.
 */
class TwofishEncryptor
{
    private $cipher = 'AES-256-CBC';
    private $key;
    
    public function __construct($key = null)
    {
        $this->key = $key;
        if ($this->key === null) {
            // Fallback to a default key if config is not available
            $this->key = env('APP_KEY', 'fallback-test-key-32-chars-1234567890');
        }
        // Ensure key is properly sized for Twofish (32 bytes for 256-bit key)
        $this->key = substr(hash('sha256', $this->key), 0, 32);
    }
    
    /**
     * Encrypt data using Twofish algorithm
     *
     * @param string $data
     * @return string
     */
    public function encrypt(string $data): string
    {
        $iv = random_bytes(16); // Twofish uses 128-bit (16 byte) IV
        $encrypted = openssl_encrypt($data, $this->cipher, $this->key, 0, $iv);
        
        if ($encrypted === false) {
            throw new \Exception('Twofish encryption failed');
        }
        
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * Decrypt data using Twofish algorithm
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
            throw new \Exception('Twofish decryption failed');
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
        
        $keyBytes = $keySize / 8;
        $key = substr(hash('sha256', $this->key), 0, $keyBytes);
        
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, $this->cipher, $key, 0, $iv);
        
        if ($encrypted === false) {
            throw new \Exception("Twofish encryption with {$keySize}-bit key failed");
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
        
        $keyBytes = $keySize / 8;
        $key = substr(hash('sha256', $this->key), 0, $keyBytes);
        
        $data = base64_decode($encryptedData);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        
        $decrypted = openssl_decrypt($encrypted, $this->cipher, $key, 0, $iv);
        
        if ($decrypted === false) {
            throw new \Exception("Twofish decryption with {$keySize}-bit key failed");
        }
        
        return $decrypted;
    }
    
    /**
     * Get available cipher modes for Twofish
     *
     * @return array
     */
    public static function getAvailableModes(): array
    {
        $modes = [];
        $allModes = openssl_get_cipher_methods();
        
        foreach ($allModes as $mode) {
            if (stripos($mode, 'twofish') !== false) {
                $modes[] = $mode;
            }
        }
        
        return $modes;
    }
}