<?php

namespace Units\Encryptor;

/**
 * TripleDES Encryptor Class
 * 
 * Triple DES (3DES) is a symmetric-key block cipher that applies the Data Encryption Standard (DES) cipher algorithm three times.
 * While being phased out, it's still used in some legacy systems.
 * 
 * Pros:
 * - Compatible with existing DES infrastructure
 * - More secure than single DES
 * - Well understood algorithm
 * - FIPS 140-2 approved
 * - Simple to implement
 * 
 * Cons:
 * - Slower than modern alternatives (3x slower than DES)
 * - Smaller key sizes compared to AES
 * - Being deprecated (NIST deprecated it in 2017)
 * - Vulnerable to meet-in-the-middle attacks
 * - 64-bit block size susceptible to birthday attacks
 * 
 * Definition:
 * TripleDES applies DES three times with either two or three different keys (EDE - Encrypt-Decrypt-Encrypt).
 * With EDE2, K1=K3, and with EDE3, all three keys are different. Provides effective key lengths of 112 or 168 bits.
 */
class TripleDesEncryptor
{
    private $cipher = 'DES-EDE3-CBC'; // Triple DES with 3 keys in CBC mode
    private $key;
    
    public function __construct($key = null)
    {
        $this->key = $key;
        if ($this->key === null) {
            // Fallback to a default key if config is not available
            $this->key = env('APP_KEY', 'fallback-test-key-tripledes-1234567890');
        }
        // Ensure key is properly sized for 3DES (24 bytes for 3-key 3DES)
        $this->key = substr(hash('sha256', $this->key), 0, 24);
    }
    
    /**
     * Encrypt data using TripleDES algorithm
     *
     * @param string $data
     * @return string
     */
    public function encrypt(string $data): string
    {
        $iv = random_bytes(8); // 3DES uses 8-byte IV
        $encrypted = openssl_encrypt($data, $this->cipher, $this->key, 0, $iv);
        
        if ($encrypted === false) {
            throw new \Exception('TripleDES encryption failed');
        }
        
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * Decrypt data using TripleDES algorithm
     *
     * @param string $encryptedData
     * @return string
     */
    public function decrypt(string $encryptedData): string
    {
        $data = base64_decode($encryptedData);
        $iv = substr($data, 0, 8); // Extract 8-byte IV
        $encrypted = substr($data, 8);
        
        $decrypted = openssl_decrypt($encrypted, $this->cipher, $this->key, 0, $iv);
        
        if ($decrypted === false) {
            throw new \Exception('TripleDES decryption failed');
        }
        
        return $decrypted;
    }
    
    /**
     * Encrypt using 2-key TripleDES
     *
     * @param string $data
     * @return string
     */
    public function encryptTwoKey(string $data): string
    {
        $cipher = 'DES-EDE-CBC'; // 2-key Triple DES
        $key = substr(hash('sha256', $this->key), 0, 16); // 16 bytes for 2-key 3DES
        
        $iv = random_bytes(8);
        $encrypted = openssl_encrypt($data, $cipher, $key, 0, $iv);
        
        if ($encrypted === false) {
            throw new \Exception('2-key TripleDES encryption failed');
        }
        
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * Decrypt using 2-key TripleDES
     *
     * @param string $encryptedData
     * @return string
     */
    public function decryptTwoKey(string $encryptedData): string
    {
        $cipher = 'DES-EDE-CBC'; // 2-key Triple DES
        $key = substr(hash('sha256', $this->key), 0, 16); // 16 bytes for 2-key 3DES
        
        $data = base64_decode($encryptedData);
        $iv = substr($data, 0, 8);
        $encrypted = substr($data, 8);
        
        $decrypted = openssl_decrypt($encrypted, $cipher, $key, 0, $iv);
        
        if ($decrypted === false) {
            throw new \Exception('2-key TripleDES decryption failed');
        }
        
        return $decrypted;
    }
}