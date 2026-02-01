<?php

namespace Units\Encryptor;

/**
 * RSA Encryptor Class
 * 
 * RSA (Rivest-Shamir-Adleman) is an asymmetric encryption algorithm that uses a pair of keys: public and private.
 * Commonly used for secure data transmission and digital signatures.
 * 
 * Pros:
 * - Asymmetric encryption (no key sharing needed)
 * - Secure for key exchange
 * - Digital signature capability
 * - Well-established and trusted
 * - Used in SSL/TLS protocols
 * 
 * Cons:
 * - Slower than symmetric encryption
 * - Cannot encrypt large amounts of data directly
 * - Key size affects performance significantly
 * - Vulnerable to quantum computing attacks
 * 
 * Definition:
 * RSA is an asymmetric cryptographic algorithm based on the practical difficulty of factoring the product of two large prime numbers.
 * The public key consists of the modulus and encryption exponent, while the private key includes the decryption exponent.
 */
class RsaEncryptor
{
    private $privateKey;
    private $publicKey;
    
    public function __construct($privateKeyPath = null, $publicKeyPath = null)
    {
        if ($privateKeyPath && file_exists($privateKeyPath)) {
            $this->privateKey = openssl_pkey_get_private(file_get_contents($privateKeyPath));
        }
        if ($publicKeyPath && file_exists($publicKeyPath)) {
            $this->publicKey = openssl_pkey_get_public(file_get_contents($publicKeyPath));
        }
    }
    
    /**
     * Encrypt data using RSA public key
     *
     * @param string $data
     * @return string
     */
    public function encrypt(string $data): string
    {
        if (!$this->publicKey) {
            throw new \Exception('Public key not available');
        }
        
        $encrypted = '';
        $maxChunkSize = 245; // RSA can encrypt max 256 bytes with 2048 bit key, minus padding
        
        $chunks = str_split($data, $maxChunkSize);
        foreach ($chunks as $chunk) {
            $encryptedChunk = '';
            if (!openssl_public_encrypt($chunk, $encryptedChunk, $this->publicKey)) {
                throw new \Exception('RSA encryption failed');
            }
            $encrypted .= $encryptedChunk;
        }
        
        return base64_encode($encrypted);
    }
    
    /**
     * Decrypt data using RSA private key
     *
     * @param string $encryptedData
     * @return string
     */
    public function decrypt(string $encryptedData): string
    {
        if (!$this->privateKey) {
            throw new \Exception('Private key not available');
        }
        
        $encrypted = base64_decode($encryptedData);
        $keyDetails = openssl_pkey_get_details($this->privateKey);
        $keySize = $keyDetails['bits'];
        $maxChunkSize = $keySize / 8; // Convert bits to bytes
        
        $chunks = str_split($encrypted, $maxChunkSize);
        $decrypted = '';
        
        foreach ($chunks as $chunk) {
            $decryptedChunk = '';
            if (!openssl_private_decrypt($chunk, $decryptedChunk, $this->privateKey)) {
                throw new \Exception('RSA decryption failed');
            }
            $decrypted .= $decryptedChunk;
        }
        
        return $decrypted;
    }
    
    /**
     * Generate new RSA key pair
     *
     * @param int $keySize
     * @return array
     */
    public static function generateKeyPair(int $keySize = 2048): array
    {
        $config = [
            'digest_alg' => 'sha256',
            'private_key_bits' => $keySize,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];
        
        $res = openssl_pkey_new($config);
        openssl_pkey_export($res, $privateKey);
        $publicKey = openssl_pkey_get_details($res)['key'];
        
        return [
            'private_key' => $privateKey,
            'public_key' => $publicKey
        ];
    }
}