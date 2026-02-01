<?php

namespace Units\Encryptor;

/**
 * ChaCha20Poly1305 Encryptor Class
 * 
 * ChaCha20-Poly1305 is an authenticated encryption algorithm that combines the ChaCha20 stream cipher and Poly1305 authenticator.
 * It's designed to be fast and secure, especially on systems without hardware AES support.
 * 
 * Pros:
 * - Very fast performance
 * - Secure authenticated encryption
 * - Resistant to timing attacks
 * - Works well on low-power devices
 * - No special hardware requirements
 * - Better performance than AES on many platforms
 * 
 * Cons:
 * - Less widespread adoption than AES
 * - Relatively newer algorithm (though well-studied)
 * - Not as extensively deployed in legacy systems
 * - Larger code footprint than some alternatives
 * 
 * Definition:
 * ChaCha20-Poly1305 is a stream cipher that combines ChaCha20 (a variant of Salsa20) for encryption
 * with Poly1305 for authentication. It provides both confidentiality and authenticity in a single primitive.
 * It's standardized in RFC 7539 and used in TLS 1.3.
 */
class ChaCha20Poly1305Encryptor
{
    public function __construct()
    {
        // Check if ChaCha20-Poly1305 is available in this PHP/OpenSSL version
        $availableCiphers = openssl_get_cipher_methods();
        if (!in_array('chacha20-poly1305', array_map('strtolower', $availableCiphers))) {
            throw new \Exception('ChaCha20-Poly1305 is not supported in this OpenSSL version');
        }
    }
    
    /**
     * Encrypt data using ChaCha20-Poly1305
     *
     * @param string $data
     * @param string|null $key
     * @return string
     */
    public function encrypt(string $data, string $key = null): string
    {
        if (!$key) {
            $key = random_bytes(32); // 256-bit key for ChaCha20
        }
        
        $iv = random_bytes(12); // 96-bit nonce for ChaCha20
        $tag = '';
        
        $encrypted = openssl_encrypt(
            $data,
            'ChaCha20-Poly1305',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );
        
        if ($encrypted === false) {
            throw new \Exception('ChaCha20-Poly1305 encryption failed');
        }
        
        // Combine IV, tag, and encrypted data
        return base64_encode($iv . $tag . $encrypted);
    }
    
    /**
     * Decrypt data using ChaCha20-Poly1305
     *
     * @param string $encryptedData
     * @param string $key
     * @return string
     */
    public function decrypt(string $encryptedData, string $key): string
    {
        $data = base64_decode($encryptedData);
        
        // Extract IV (12 bytes), tag (16 bytes), and encrypted data
        $iv = substr($data, 0, 12);
        $tag = substr($data, 12, 16);
        $encrypted = substr($data, 28);
        
        $decrypted = openssl_decrypt(
            $encrypted,
            'ChaCha20-Poly1305',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );
        
        if ($decrypted === false) {
            throw new \Exception('ChaCha20-Poly1305 decryption failed - authentication failed');
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
        return random_bytes(32); // 256-bit key
    }
    
    /**
     * Encrypt with additional authenticated data (AAD)
     *
     * @param string $data
     * @param string $aad Additional authenticated data
     * @param string|null $key
     * @return string
     */
    public function encryptWithAad(string $data, string $aad, string $key = null): string
    {
        if (!$key) {
            $key = random_bytes(32);
        }
        
        $iv = random_bytes(12);
        $tag = '';
        
        $encrypted = openssl_encrypt(
            $data,
            'ChaCha20-Poly1305',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            $aad
        );
        
        if ($encrypted === false) {
            throw new \Exception('ChaCha20-Poly1305 encryption with AAD failed');
        }
        
        return base64_encode($iv . $tag . $encrypted);
    }
    
    /**
     * Decrypt with additional authenticated data (AAD)
     *
     * @param string $encryptedData
     * @param string $aad Additional authenticated data
     * @param string $key
     * @return string
     */
    public function decryptWithAad(string $encryptedData, string $aad, string $key): string
    {
        $data = base64_decode($encryptedData);
        
        $iv = substr($data, 0, 12);
        $tag = substr($data, 12, 16);
        $encrypted = substr($data, 28);
        
        $decrypted = openssl_decrypt(
            $encrypted,
            'ChaCha20-Poly1305',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            $aad
        );
        
        if ($decrypted === false) {
            throw new \Exception('ChaCha20-Poly1305 decryption with AAD failed');
        }
        
        return $decrypted;
    }
}