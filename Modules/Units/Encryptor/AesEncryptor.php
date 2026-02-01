<?php

namespace Units\Encryptor;

/**
 * AES Encryptor Class
 * 
 * Advanced Encryption Standard (AES) is a symmetric encryption algorithm widely used for securing sensitive data.
 * Uses AES-256-CBC mode which provides strong security and is industry standard.
 * 
 * Pros:
 * - Very secure (256-bit key)
 * - Fast performance
 * - Industry standard
 * - Widely supported
 * - FIPS 140-2 compliant
 * 
 * Cons:
 * - Requires proper IV management
 * - Vulnerable to padding oracle attacks if not implemented correctly
 * - Fixed block size (128 bits)
 * 
 * Definition:
 * AES is a symmetric block cipher that encrypts data in fixed-size blocks of 128 bits using keys of 128, 192, or 256 bits.
 */
class AesEncryptor
{
    private $cipher = 'AES-256-CBC';
    private $key;
    
    public function __construct($key = null)
    {
        $this->key = $key ?: config('app.key');
    }
    
    /**
     * Encrypt data using AES-256-CBC
     *
     * @param string $data
     * @return string
     */
    public function encrypt(string $data): string
    {
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, $this->cipher, $this->key, 0, $iv);
        
        if ($encrypted === false) {
            throw new \Exception('Encryption failed');
        }
        
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * Decrypt data using AES-256-CBC
     *
     * @param string $encryptedData
     * @return string
     */
    public function decrypt(string $encryptedData): string
    {
        $data = base64_decode($encryptedData);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        
        $decrypted = openssl_decrypt($encrypted, $this->cipher, $this->key, 0, $iv);
        
        if ($decrypted === false) {
            throw new \Exception('Decryption failed');
        }
        
        return $decrypted;
    }
}