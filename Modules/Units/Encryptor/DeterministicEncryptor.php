<?php

namespace Units\Encryptor;

/**
 * Deterministic Encryptor Class
 *
 * Provides deterministic encryption where the same input always produces the same output.
 * This is essential for database searching where encrypted values need to match exactly.
 * Uses AES-256-CBC with a deterministic IV derived from the input data.
 *
 * WARNING: This approach has security implications compared to traditional random IVs.
 * The deterministic nature means identical plaintexts will have identical ciphertexts,
 * which could reveal patterns to attackers. Use only when database searchability is required.
 *
 * Pros:
 * - Same input always produces same output (for database searching)
 * - Still provides confidentiality against passive attackers
 * - Compatible with existing AES infrastructure
 * - 256-bit security level
 *
 * Cons:
 * - Identical plaintexts produce identical ciphertexts (pattern disclosure risk)
 * - Less secure than random IV approach against certain attack vectors
 * - Should not be used for general purpose encryption
 *
 * Definition:
 * Uses AES-256-CBC with an IV that is deterministically derived from the input data
 * combined with a secret key, ensuring reproducibility while maintaining security.
 */
class DeterministicEncryptor
{
    private $cipher = 'AES-256-CBC';
    private $key;

    public function __construct($key = null)
    {
        $this->key = $key ?: config('app.key');
        if (!$this->key) {
            throw new \Exception('Encryption key is required for deterministic encryption');
        }

        // Ensure key is proper length for AES-256
        $this->key = $this->padOrTruncateKey($this->key, 32);
    }

    /**
     * Encrypt data using deterministic AES-256-CBC
     * Same input will always produce same output
     *
     * @param string $data
     * @return string
     */
    public function encrypt(string $data): string
    {
        // Create a deterministic IV by hashing the data + key
        $ivSource = hash('sha256', $data . $this->key . 'deterministic_salt');
        $iv = substr($ivSource, 0, 16); // Take first 16 bytes for IV

        $encrypted = openssl_encrypt($data, $this->cipher, $this->key, 0, $iv);

        if ($encrypted === false) {
            throw new \Exception('Deterministic encryption failed');
        }

        // Return base64 encoded version
        return base64_encode($iv . $encrypted);
    }

    /**
     * Decrypt data using deterministic AES-256-CBC
     *
     * @param string $encryptedData
     * @return string
     */
    public function decrypt(string $encryptedData): string
    {
        $data = base64_decode($encryptedData);

        if ($data === false || strlen($data) < 16) {
            throw new \Exception('Invalid encrypted data format');
        }

        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);

        $decrypted = openssl_decrypt($encrypted, $this->cipher, $this->key, 0, $iv);

        if ($decrypted === false) {
            throw new \Exception('Deterministic decryption failed');
        }

        return $decrypted;
    }

    /**
     * Pad or truncate key to specified length
     *
     * @param string $key
     * @param int $length
     * @return string
     */
    private function padOrTruncateKey(string $key, int $length): string
    {
        if (strlen($key) >= $length) {
            return substr($key, 0, $length);
        }

        // If key is too short, expand it using hash
        $hashedKey = hash('sha256', $key, true);
        return str_pad($key, $length, $hashedKey);
    }

    /**
     * Verify that the same input produces the same output (for testing)
     *
     * @param string $data
     * @return bool
     */
    public function verifyDeterministic(string $data): bool
    {
        $firstEnc = $this->encrypt($data);
        $secondEnc = $this->encrypt($data);
        return $firstEnc === $secondEnc;
    }
}
