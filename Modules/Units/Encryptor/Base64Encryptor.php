<?php

namespace Units\Encryptor;

/**
 * Base64 Encryptor Class
 * 
 * Base64 is not technically an encryption algorithm but an encoding scheme that represents binary data in ASCII string format.
 * It's commonly used for data transmission and storage, often combined with actual encryption.
 * 
 * Pros:
 * - Universal compatibility
 * - Human-readable representation
 * - No data loss during encoding/decoding
 * - Standardized across platforms
 * - Easy to transmit over text-based protocols
 * 
 * Cons:
 * - Does not provide security (only obfuscation)
 * - Increases data size by ~33%
 * - Not suitable for sensitive data alone
 * - Can be easily decoded by anyone
 * 
 * Definition:
 * Base64 encoding converts binary data into a string of 64 ASCII characters (A-Z, a-z, 0-9, +, /) with '=' padding.
 * It represents 3 bytes of binary data as 4 ASCII characters, making it suitable for transmission over text-based systems.
 * While called "encoding", it provides no encryption or security.
 */
class Base64Encryptor
{
    /**
     * Encode data using Base64
     *
     * @param string $data
     * @return string
     */
    public function encode(string $data): string
    {
        return base64_encode($data);
    }
    
    /**
     * Decode data using Base64
     *
     * @param string $encodedData
     * @return string
     */
    public function decode(string $encodedData): string
    {
        $decoded = base64_decode($encodedData, true);
        
        if ($decoded === false) {
            throw new \Exception('Base64 decoding failed - invalid input');
        }
        
        return $decoded;
    }
    
    /**
     * Safe Base64 encode (URL-safe)
     *
     * @param string $data
     * @return string
     */
    public function safeEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Safe Base64 decode (URL-safe)
     *
     * @param string $encodedData
     * @return string
     */
    public function safeDecode(string $encodedData): string
    {
        // Restore URL-safe characters and padding
        $encodedData = str_pad(strtr($encodedData, '-_', '+/'), strlen($encodedData) % 4, '=', STR_PAD_RIGHT);
        
        $decoded = base64_decode($encodedData, true);
        
        if ($decoded === false) {
            throw new \Exception('Safe Base64 decoding failed - invalid input');
        }
        
        return $decoded;
    }
    
    /**
     * Encode with additional encryption layer (combines Base64 with XOR cipher)
     *
     * @param string $data
     * @param string $key
     * @return string
     */
    public function encodeWithXor(string $data, string $key): string
    {
        // Apply XOR cipher
        $xorData = '';
        $keyLength = strlen($key);
        
        for ($i = 0; $i < strlen($data); $i++) {
            $xorData .= $data[$i] ^ $key[$i % $keyLength];
        }
        
        // Then encode with Base64
        return base64_encode($xorData);
    }
    
    /**
     * Decode with XOR cipher
     *
     * @param string $encodedData
     * @param string $key
     * @return string
     */
    public function decodeWithXor(string $encodedData, string $key): string
    {
        // First decode Base64
        $decoded = base64_decode($encodedData, true);
        
        if ($decoded === false) {
            throw new \Exception('Base64 decoding failed - invalid input');
        }
        
        // Then reverse XOR cipher
        $originalData = '';
        $keyLength = strlen($key);
        
        for ($i = 0; $i < strlen($decoded); $i++) {
            $originalData .= $decoded[$i] ^ $key[$i % $keyLength];
        }
        
        return $originalData;
    }
    
    /**
     * Validate if a string is valid Base64
     *
     * @param string $input
     * @return bool
     */
    public static function isValidBase64(string $input): bool
    {
        $decoded = base64_decode($input, true);
        return $decoded !== false && base64_encode($decoded) === $input;
    }
}