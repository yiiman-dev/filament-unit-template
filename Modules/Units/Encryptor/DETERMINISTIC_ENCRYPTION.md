# Deterministic Encryption

## Overview

The Deterministic Encryption feature provides a way to encrypt data where the same input always produces the same output. This is essential for database searching scenarios where encrypted values need to match exactly for WHERE queries.

## Algorithm

The `DeterministicEncryptor` class uses AES-256-CBC encryption with a deterministic IV (Initialization Vector) derived from the input data itself. The IV is created by hashing the concatenation of the data, key, and a salt.

## Security Considerations

⚠️ **WARNING**: This approach has security implications compared to traditional random IVs:
- Identical plaintexts produce identical ciphertexts, which could reveal patterns to attackers
- Use only when database searchability is required
- Not suitable for general-purpose encryption
- Should be used with caution and understanding of the trade-offs

## Usage

### With EncryptCast

#### Using String Syntax
```php
use Units\Encryptor\Casts\EncryptCast;

// In your Eloquent model
protected $casts = [
    'searchable_encrypted_field' => EncryptCast::class . ':deterministic',
];

// Or with custom key
protected $casts = [
    'searchable_encrypted_field' => EncryptCast::class . ':deterministic,custom_encryption_key',
];
```

#### Using Static Methods (Recommended)
```php
use Units\Encryptor\Casts\EncryptCast;

// In your Eloquent model - using static methods for better readability
protected $casts = [
    'searchable_field' => EncryptCast::algoDeterministic(),
    'encrypted_field' => EncryptCast::algoAes(),
    'base64_field' => EncryptCast::algoBase64(),
    'blowfish_field' => EncryptCast::algoBlowfish(),
    'camellia_field' => EncryptCast::algoCamellia(),
    'chacha20poly1305_field' => EncryptCast::algoChaCha20Poly1305(),
    'rijndael_field' => EncryptCast::algoRijndael(),
    'rsa_field' => EncryptCast::algoRsa('/path/to/private.pem', '/path/to/public.pem'),
    'sodium_field' => EncryptCast::algoSodium(),
    'tripledes_field' => EncryptCast::algoTripleDes(),
    'twofish_field' => EncryptCast::algoTwofish(),
];
```

Available static methods:
- `algoAes()` - AES-256-CBC encryption
- `algoBase64()` - Base64 encoding (obfuscation only)
- `algoBlowfish()` - Blowfish cipher
- `algoCamellia()` - Camellia cipher
- `algoChaCha20Poly1305()` - ChaCha20-Poly1305 authenticated encryption
- `algoDeterministic()` - Deterministic AES encryption (for database searching)
- `algoRijndael()` - Rijndael cipher
- `algoRsa()` - RSA asymmetric encryption (requires key paths)
- `algoSodium()` - Sodium cryptography library
- `algoTripleDes()` - Triple DES encryption
- `algoTwofish()` - Twofish cipher

### Direct Usage

```php
use Units\Encryptor\DeterministicEncryptor;

$encryptor = new DeterministicEncryptor('your_32_char_encryption_key');

// Encrypt
$encrypted = $encryptor->encrypt('sensitive_data');

// Decrypt
$decrypted = $encryptor->decrypt($encrypted);

// Verify deterministic behavior
$first_encrypted = $encryptor->encrypt('same_data');
$second_encrypted = $encryptor->encrypt('same_data');
assert($first_encrypted === $second_encrypted); // true
```

## Database Search Example

```php
// Store encrypted data
$user = new User();
$user->email = 'john@example.com'; // This will be deterministically encrypted
$user->save();

// Search for the same encrypted value
$searchEmail = 'john@example.com';
$encryptedSearch = $encryptor->encrypt($searchEmail);
$foundUser = User::where('email', $encryptedSearch)->first();

// The search will work because the same input produces the same encrypted output
```

## Comparison with Other Algorithms

| Algorithm | Deterministic | Security Level | Use Case |
|-----------|---------------|----------------|----------|
| AES (random IV) | No | High | General purpose |
| Deterministic AES | Yes | Medium | Database search |
| Base64 | No* | None | Obfuscation only |

*Base64 is deterministic but provides no security.

## Testing

The implementation includes comprehensive tests:
- `DeterministicEncryptorTest`: Tests the core encryption logic
- `DeterministicEncryptCastTest`: Tests integration with Eloquent casts

Run tests:
```bash
php artisan test Modules/Units/Encryptor/Tests/Unit/DeterministicEncryptorTest.php
php artisan test Modules/Units/Encryptor/Tests/Unit/DeterministicEncryptCastTest.php
```

## Best Practices

1. **Use only when necessary**: Only use deterministic encryption when database searching is required
2. **Strong keys**: Always use strong, unique encryption keys
3. **Separate concerns**: Use different encryption methods for searchable vs non-searchable fields
4. **Monitor access**: Implement additional access controls for deterministically encrypted data
5. **Regular audits**: Regularly audit usage of deterministic encryption in your codebase
