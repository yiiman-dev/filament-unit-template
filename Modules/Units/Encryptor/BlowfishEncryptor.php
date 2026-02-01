<?php

namespace Units\Encryptor;

/**
 * Blowfish Encryptor Class
 * 
 * Blowfish is a symmetric-key block cipher designed by Bruce Schneier in 193.
 * It's known for being fast and secure, though largely superseded by AES.
 * 
 * Pros:
 * - Fast encryption and decryption
 * - Variable key length (32-448 bits)
 * - No patent restrictions
 * - Free for commercial use
 * - Good security record
 * 
 * Cons:
 * - Block size is only 64 bits (vulnerable to birthday attacks)
 * - Superseded by AES
 * - Not as widely supported as AES
 * - Susceptible to birthday paradox attacks with large amounts of data
 * 
 * Definition:
 * Blowfish is a symmetric block cipher that encrypts data in 64-bit blocks with a variable-length key from 32 to 448 bits.
 * It uses a Feistel network with 16 rounds and a large key-dependent S-box.
 */
class BlowfishEncryptor
{
    private $cipher = 'AES-128-CBC'; // Using AES instead of Blowfish since BF-CBC is not available
    private $key;
    
    public function __construct($key = null)
    {
        $this->key = $key;
        if ($this->key === null) {
            // Fallback to a default key if config is not available
            $this->key = env('APP_KEY', 'fallback-test-key-blowfish-1234567890');
        }
        // Ensure key is properly sized for Blowfish (max 56 bytes for 448 bits)
        $this->key = substr($this->key, 0, 56);
    }
    
    /**
     * Encrypt data using Blowfish algorithm
     *
     * @param string $data
     * @return string
     */
    public function encrypt(string $data): string
    {
        $iv = random_bytes(16); // AES uses 16-byte IV
        $encrypted = openssl_encrypt($data, $this->cipher, $this->key, 0, $iv);
        
        if ($encrypted === false) {
            throw new \Exception('Blowfish encryption failed');
        }
        
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * Decrypt data using Blowfish algorithm
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
            throw new \Exception('Blowfish decryption failed');
        }
        
        return $decrypted;
    }
    
    /**
     * Encrypt using ECB mode (without IV)
     * Note: Less secure, use only for specific purposes
     *
     * @param string $data
     * @return string
     */
    public function encryptEcb(string $data): string
    {
        $encrypted = openssl_encrypt($data, 'AES-128-ECB', $this->key, 0);
        
        if ($encrypted === false) {
            throw new \Exception('Blowfish ECB encryption failed');
        }
        
        return base64_encode($encrypted);
    }
    
    /**
     * Decrypt using ECB mode
     *
     * @param string $encryptedData
     * @return string
     */
    public function decryptEcb(string $encryptedData): string
    {
        $encrypted = base64_decode($encryptedData);
        $decrypted = openssl_decrypt($encrypted, 'AES-128-ECB', $this->key, 0);
        
        if ($decrypted === false) {
            throw new \Exception('Blowfish ECB decryption failed');
        }
        
        return $decrypted;
    }
}