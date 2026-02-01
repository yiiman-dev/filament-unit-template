<?php

namespace Units\Encryptor;

/**
 * Sodium Encryptor Class
 * 
 * Sodium is a modern cryptography library that provides easy-to-use, high-security encryption functions.
 * Uses libsodium which implements the NaCl (Networking and Cryptography library) cryptographic primitives.
 * 
 * Pros:
 * - Modern and secure
 * - Easy to use (hard to misuse)
 * - Fast performance
 * - Built-in protection against timing attacks
 * - Small code footprint
 * - Authenticated encryption by default
 * 
 * Cons:
 * - Less widespread adoption than AES
 * - Requires PHP 7.2+ with sodium extension
 * - Limited customization options
 * - Not as widely studied as older algorithms
 * 
 * Definition:
 * Sodium provides authenticated encryption using the XChaCha20-Poly1305 algorithm, which combines
 * the XChaCha20 stream cipher for encryption and Poly1305 for authentication.
 */
class SodiumEncryptor
{
    public function __construct()
    {
        if (!extension_loaded('sodium')) {
            throw new \Exception('Sodium extension is required');
        }
    }
    
    /**
     * Encrypt data using Sodium's authenticated encryption
     *
     * @param string $data
     * @param string|null $key
     * @return string
     */
    public function encrypt(string $data, string $key = null): string
    {
        if (!$key) {
            $key = sodium_crypto_secretbox_keygen();
        }
        
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $encrypted = sodium_crypto_secretbox($data, $nonce, $key);
        
        return base64_encode($nonce . $encrypted);
    }
    
    /**
     * Decrypt data using Sodium's authenticated encryption
     *
     * @param string $encryptedData
     * @param string $key
     * @return string
     */
    public function decrypt(string $encryptedData, string $key): string
    {
        $data = base64_decode($encryptedData);
        if ($data === false) {
            throw new \Exception('Invalid base64 data');
        }
        
        if (strlen($data) < SODIUM_CRYPTO_SECRETBOX_NONCEBYTES) {
            throw new \Exception('Data too short for decryption');
        }
        
        $nonce = substr($data, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $encrypted = substr($data, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        
        $decrypted = sodium_crypto_secretbox_open($encrypted, $nonce, $key);
        
        if ($decrypted === false) {
            throw new \Exception('Decryption failed - invalid key or corrupted data');
        }
        
        return $decrypted;
    }
    
    /**
     * Generate a random encryption key
     *
     * @return string
     */
    public static function generateKey(): string
    {
        return sodium_crypto_secretbox_keygen();
    }
    
    /**
     * Encrypt with password using key derivation
     *
     * @param string $data
     * @param string $password
     * @return array
     */
    public function encryptWithPassword(string $data, string $password): array
    {
        $salt = random_bytes(SODIUM_CRYPTO_PWHASH_SALTBYTES); // Use correct salt size
        $key = sodium_crypto_pwhash(
            SODIUM_CRYPTO_SECRETBOX_KEYBYTES,
            $password,
            $salt,
            SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
            SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
        );
        
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $encrypted = sodium_crypto_secretbox($data, $nonce, $key);
        
        return [
            'data' => base64_encode($nonce . $encrypted),
            'salt' => base64_encode($salt),
            'key' => base64_encode($key)
        ];
    }
    
    /**
     * Decrypt with password using key derivation
     *
     * @param array $encryptedData
     * @param string $password
     * @return string
     */
    public function decryptWithPassword(array $encryptedData, string $password): string
    {
        $salt = base64_decode($encryptedData['salt']);
        $key = sodium_crypto_pwhash(
            SODIUM_CRYPTO_SECRETBOX_KEYBYTES,
            $password,
            $salt,
            SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
            SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
        );
        
        $data = base64_decode($encryptedData['data']);
        $nonce = substr($data, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $encrypted = substr($data, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        
        $decrypted = sodium_crypto_secretbox_open($encrypted, $nonce, $key);
        
        if ($decrypted === false) {
            throw new \Exception('Decryption failed - invalid password or corrupted data');
        }
        
        return $decrypted;
    }
}