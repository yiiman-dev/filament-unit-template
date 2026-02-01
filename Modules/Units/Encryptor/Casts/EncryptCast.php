<?php

namespace Units\Encryptor\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Facades\Log;
use Units\Encryptor\AesEncryptor;
use Units\Encryptor\Base64Encryptor;
use Units\Encryptor\BlowfishEncryptor;
use Units\Encryptor\CamelliaEncryptor;
use Units\Encryptor\ChaCha20Poly1305Encryptor;
use Units\Encryptor\DeterministicEncryptor;
use Units\Encryptor\RijndaelEncryptor;
use Units\Encryptor\RsaEncryptor;
use Units\Encryptor\SodiumEncryptor;
use Units\Encryptor\TripleDesEncryptor;
use Units\Encryptor\TwofishEncryptor;

use function Laravel\Prompts\error;

/**
 * EncryptCast Class
 *
 * Eloquent attribute casting class that provides transparent encryption and decryption of model attributes.
 * Supports multiple encryption algorithms including AES, RSA, Base64, and various symmetric ciphers.
 *
 * USAGE EXAMPLES:
 *
 * 1. Basic AES encryption (default):
 *    protected $casts = [
 *        'secret_data' => EncryptCast::class,
 *    ];
 *
 * 2. Specific algorithm with custom key:
 *    protected $casts = [
 *        'encrypted_field' => EncryptCast::class . ':aes,my_custom_key',
 *    ];
 *
 * 3. RSA encryption with key paths:
 *    protected $casts = [
 *        'rsa_field' => EncryptCast::class . ':rsa,null,{"private_key_path":"/path/to/private.pem","public_key_path":"/path/to/public.pem"}',
 *    ];
 *
 * 4. ChaCha20-Poly1305 encryption:
 *    protected $casts = [
 *        'secure_data' => EncryptCast::class . ':chacha20poly1305',
 *    ];
 *
 * SUPPORTED ALGORITHMS:
 * - aes: Advanced Encryption Standard (AES-256-CBC)
 * - base64: Base64 encoding (obfuscation only)
 * - blowfish: Blowfish cipher
 * - camellia: Camellia cipher
 * - chacha20poly1305: ChaCha20-Poly1305 authenticated encryption
 * - rijndael: Rijndael cipher
 * - rsa: RSA asymmetric encryption
 * - sodium: Sodium cryptography library
 * - tripledes: Triple DES encryption
 * - twofish: Twofish cipher
 * - deterministic: Deterministic AES encryption (for database searching)
 *
 * SECURITY NOTES:
 * - Always use strong, unique keys for production
 * - RSA requires proper key file paths in options array
 * - ChaCha20Poly1305 and Sodium have special key requirements
 * - Base64 provides no real security (obfuscation only)
 * - Deterministic encryption reveals patterns when identical data is encrypted
 * - Use deterministic algorithm only when database searchability is required
 * - Consider key rotation strategies for sensitive applications
 */
class EncryptCast implements CastsAttributes
{
    protected $algorithm;
    protected $key;
    protected $options;

    /**
     * Constructor for EncryptCast
     *
     * @param string $algorithm Encryption algorithm to use
     * @param string|null $key Custom encryption key (uses app.key if null)
     * @param array $options Additional options for specific algorithms
     */
    public function __construct(string $algorithm = 'aes', ?string $key = null, array $options = [])
    {
        $this->algorithm = $algorithm;
        $this->key = $key ?: $this->getDefaultKey();
        $this->options = $options;
    }

    private function getDefaultKey(): ?string
    {
        try {
            return function_exists('config') ? config('app.key') : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function get($model, string $key, $value, array $attributes)
    {
        if (is_null($value)) {
            return null;
        }

        try {
            $encryptor = $this->getEncryptor();

            if ($this->algorithm === 'base64') {
                return $encryptor->decode($value);
            } elseif ($this->algorithm === 'rsa') {
                return $encryptor->decrypt($value);
            } elseif ($this->algorithm === 'chacha20poly1305') {
                // ChaCha20Poly1305 requires the key as a parameter
                return $encryptor->decrypt($value, $this->key);
            } elseif ($this->algorithm === 'sodium') {
                // Sodium requires the key to be exactly 32 bytes
                $sodiumKey = $this->prepareSodiumKey($this->key);
                return $encryptor->decrypt($value, $sodiumKey);
            } else {
                return $encryptor->decrypt($value);
            }
        } catch (\Exception $e) {
            // Log the error but don't expose it to prevent information leakage
            if (class_exists(Log::class)) {
//                Log:error("Encryption cast decryption failed: " . $e->getMessage());
            } else {
                error_log("Encryption cast decryption failed: " . $e->getMessage());
            }
            return null;
        }
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if (is_null($value)) {
            return null;
        }

        try {
            $encryptor = $this->getEncryptor();

            if ($this->algorithm === 'base64') {
                return $encryptor->encode((string) $value);
            } elseif ($this->algorithm === 'rsa') {
                return $encryptor->encrypt((string) $value);
            } elseif ($this->algorithm === 'chacha20poly1305') {
                // ChaCha20Poly1305 requires the key as a parameter
                return $encryptor->encrypt((string) $value, $this->key);
            } elseif ($this->algorithm === 'sodium') {
                // Sodium requires the key to be exactly 32 bytes
                $sodiumKey = $this->prepareSodiumKey($this->key);
                return $encryptor->encrypt((string) $value, $sodiumKey);
            } else {
                return $encryptor->encrypt((string) $value);
            }
        } catch (\Exception $e) {
            if (class_exists(Log::class)) {
//                Log::error("Encryption cast encryption failed: " . $e->getMessage());
            } else {
                error_log("Encryption cast encryption failed: " . $e->getMessage());
            }
            throw $e;
        }
    }

    protected function getEncryptor()
    {
        switch (strtolower($this->algorithm)) {
            case 'aes':
                return new AesEncryptor($this->key);
            case 'base64':
                return new Base64Encryptor();
            case 'blowfish':
                return new BlowfishEncryptor();
            case 'camellia':
                return new CamelliaEncryptor($this->key);
            case 'chacha20poly1305':
                return new ChaCha20Poly1305Encryptor();
            case 'rijndael':
                return new RijndaelEncryptor($this->key);
            case 'rsa':
                $privateKeyPath = $this->options['private_key_path'] ?? null;
                $publicKeyPath = $this->options['public_key_path'] ?? null;
                return new RsaEncryptor($privateKeyPath, $publicKeyPath);
            case 'sodium':
                return new SodiumEncryptor();
            case 'tripledes':
                return new TripleDesEncryptor($this->key);
            case 'twofish':
                return new TwofishEncryptor($this->key);
            case 'deterministic':
                return new DeterministicEncryptor($this->key);
            default:
                throw new \InvalidArgumentException("Unsupported encryption algorithm: {$this->algorithm}");
        }
    }

    private function prepareSodiumKey(?string $key): string
    {
        if (empty($key)) {
            return sodium_crypto_secretbox_keygen();
        }

        // Ensure the key is exactly 32 bytes for Sodium
        $keyLength = SODIUM_CRYPTO_SECRETBOX_KEYBYTES; // 32

        if (strlen($key) >= $keyLength) {
            return substr($key, 0, $keyLength);
        }

        // If key is too short, pad it with zeros or hash it to get the right length
        return str_pad($key, $keyLength, "\0");
    }

//    public function serialize($model, string $key, $value, array $attributes)
//    {
//        return $this->set($model, $key, $value, $attributes);
//    }

    /**
     * Static method for AES algorithm
     */
    public static function algoAes():string
    {
        return static::class.':aes';
    }

    /**
     * Static method for Base64 algorithm
     */
    public static function algoBase64(): string
    {
        return static::class.':base64';
    }

    /**
     * Static method for Blowfish algorithm
     */
    public static function algoBlowfish(): string
    {
        return static::class.':blowfish';
    }

    /**
     * Static method for Camellia algorithm
     */
    public static function algoCamellia(): string
    {
        return static::class.':camellia';
    }

    /**
     * Static method for ChaCha20-Poly1305 algorithm
     */
    public static function algoChaCha20Poly1305(): string
    {
        return static::class.':chacha20poly1305';
    }

    /**
     * Static method for Deterministic algorithm
     */
    public static function algoDeterministic(): string
    {
        return static::class.':deterministic';
    }

    /**
     * Static method for Rijndael algorithm
     */
    public static function algoRijndael(): string
    {
        return static::class.':rijndael';
    }

    /**
     * Static method for RSA algorithm
     */
    public static function algoRsa(): string
    {

        return static::class.':rsa';
    }

    /**
     * Static method for Sodium algorithm
     */
    public static function algoSodium(): string
    {
        return static::class.':sodium';
    }

    /**
     * Static method for TripleDES algorithm
     */
    public static function algoTripleDes(): string
    {
        return static::class.':tripledes';
    }

    /**
     * Static method for Twofish algorithm
     */
    public static function algoTwofish(): string
    {
        return static::class.':twofish';
    }
}
