# Encryptor Module Documentation

This module provides a comprehensive set of encryption utilities and an Eloquent cast for transparent encryption/decryption of model attributes.

## Table of Contents
- [Overview](#overview)
- [Supported Algorithms](#supported-algorithms)
- [Usage Examples](#usage-examples)
- [EncryptCast Usage](#encryptcast-usage)
- [Individual Algorithm Classes](#individual-algorithm-classes)
- [Security Considerations](#security-considerations)

## Overview

The Encryptor module provides:
- Multiple encryption algorithms for different use cases
- An Eloquent `EncryptCast` for transparent attribute encryption
- Flexible configuration options for each algorithm
- Comprehensive error handling and logging

## Supported Algorithms

| Algorithm | Type | Security Level | Use Case |
|-----------|------|----------------|----------|
| AES | Symmetric | High | General purpose encryption |
| Base64 | Encoding | None | Data transmission/storage (obfuscation only) |
| Blowfish | Symmetric | Medium-High | Legacy systems, small data |
| Camellia | Symmetric | High | Alternative to AES |
| ChaCha20-Poly1305 | Symmetric | High | Authenticated encryption |
| Rijndael | Symmetric | High | AES predecessor |
| RSA | Asymmetric | High | Key exchange, digital signatures |
| Sodium | Modern crypto | High | Modern applications |
| TripleDES | Symmetric | Low-Medium | Legacy systems only |
| Twofish | Symmetric | High | Alternative to AES |

## Usage Examples

### 1. Basic Model Attribute Encryption

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Units\Encryptor\Casts\EncryptCast;

class User extends Model
{
    protected $casts = [
        'ssn' => EncryptCast::class, // Uses AES by default
        'phone' => EncryptCast::class . ':aes', // Explicit AES
        'email' => EncryptCast::class . ':blowfish', // Different algorithm
    ];
}
```

### 2. Custom Key Encryption

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Units\Encryptor\Casts\EncryptCast;

class ConfidentialData extends Model
{
    protected $casts = [
        'secret' => EncryptCast::class . ':aes,my_custom_key',
    ];
}
```

### 3. RSA Encryption with Key Paths

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Units\Encryptor\Casts\EncryptCast;

class SecureMessage extends Model
{
    protected $casts = [
        'message' => EncryptCast::class . ':rsa,null,{"private_key_path":"/storage/keys/private.pem","public_key_path":"/storage/keys/public.pem"}',
    ];
}
```

### 4. Programmatic Usage

```php
use Units\Encryptor\AesEncryptor;

$encryptor = new AesEncryptor('my-secret-key');

// Encrypt data
$encrypted = $encryptor->encrypt('Sensitive data');

// Decrypt data
$decrypted = $encryptor->decrypt($encrypted);
```

## EncryptCast Usage

### Constructor Parameters

```php
new EncryptCast(
    string $algorithm = 'aes',    // Encryption algorithm
    ?string $key = null,          // Custom key (uses app.key if null)
    array $options = []           // Additional options for specific algorithms
);
```

### Supported Algorithms

#### `aes` (Advanced Encryption Standard)
- **Mode**: AES-256-CBC
- **Key**: Required (256-bit)
- **Use**: General-purpose encryption

#### `base64` (Base64 Encoding)
- **Note**: Provides obfuscation only, no security
- **Methods**: `encode()` and `decode()`
- **Use**: Data transmission/storage

#### `rsa` (RSA Asymmetric)
- **Options**: `private_key_path`, `public_key_path`
- **Use**: Key exchange, digital signatures

#### `chacha20poly1305` (ChaCha20-Poly1305)
- **Key**: Required for authentication
- **Use**: Authenticated encryption

#### `sodium` (Sodium Crypto)
- **Key**: Must be 32 bytes
- **Use**: Modern, fast encryption

## Individual Algorithm Classes

### AES Encryptor

```php
use Units\Encryptor\AesEncryptor;

// Create with default key (app.key)
$aes = new AesEncryptor();

// Create with custom key
$aes = new AesEncryptor('my-32-byte-secret-key-here!');

// Encrypt/decrypt
$encrypted = $aes->encrypt('data');
$decrypted = $aes->decrypt($encrypted);
```

### Base64 Encryptor

```php
use Units\Encryptor\Base64Encryptor;

$base64 = new Base64Encryptor();

// Basic encoding/decoding
$encoded = $base64->encode('data');
$decoded = $base64->decode($encoded);

// URL-safe encoding
$safeEncoded = $base64->safeEncode('data');
$safeDecoded = $base64->safeDecode($safeEncoded);

// With XOR cipher for additional obfuscation
$xorEncoded = $base64->encodeWithXor('data', 'key');
$xorDecoded = $base64->decodeWithXor($xorEncoded, 'key');
```

### RSA Encryptor

```php
use Units\Encryptor\RsaEncryptor;

// Create with key file paths
$rsa = new RsaEncryptor('/path/to/private.pem', '/path/to/public.pem');

// Encrypt/decrypt
$encrypted = $rsa->encrypt('data');
$decrypted = $rsa->decrypt($encrypted);

// Generate new key pair
$keyPair = RsaEncryptor::generateKeyPair(2048); // 2048-bit key
```

### Sodium Encryptor

```php
use Units\Encryptor\SodiumEncryptor;

$sodium = new SodiumEncryptor();

// Encrypt/decrypt (key will be prepared automatically)
$encrypted = $sodium->encrypt('data', 'key');
$decrypted = $sodium->decrypt($encrypted, 'key');
```

## Security Considerations

### 🔒 **Strong Security Practices**

1. **Key Management**:
   - Use strong, randomly generated keys
   - Store keys securely (environment variables, key management services)
   - Implement key rotation policies
   - Never hardcode keys in source code

2. **Algorithm Selection**:
   - Use AES-256 for general purposes
   - Use RSA for key exchange (2048-bit minimum)
   - Use ChaCha20-Poly1305 for authenticated encryption
   - Avoid TripleDES in new applications

3. **Implementation**:
   - Validate input data before encryption
   - Handle errors gracefully without exposing sensitive information
   - Use proper IV management for block ciphers
   - Implement proper padding schemes

### ⚠️ **Security Warnings**

1. **Base64 Warning**:
   - Base64 provides NO security - only obfuscation
   - Never use Base64 alone for sensitive data
   - Combine with actual encryption for security

2. **Key Length Requirements**:
   - AES: 256-bit keys recommended
   - RSA: 2048-bit minimum (4096-bit preferred)
   - Sodium: 32-byte keys required

3. **Performance Considerations**:
   - RSA is slow for large data - use for key exchange only
   - Consider data chunking for large payloads
   - Monitor encryption/decryption performance

### 🛡️ **Best Practices**

1. **Environment Configuration**:
   ```env
   APP_KEY=your-32-byte-base64-encoded-key-here
   ```

2. **Error Handling**:
   ```php
   try {
       $decrypted = $encryptor->decrypt($encryptedData);
   } catch (\Exception $e) {
       // Log error securely
       Log::error('Decryption failed');
       // Don't expose error details to user
       return null;
   }
   ```

3. **Testing**:
   - Test with various data sizes
   - Verify round-trip encryption/decryption
   - Test error conditions
   - Performance testing for production loads

## Troubleshooting

### Common Issues

1. **"Encryption failed" errors**:
   - Check key length requirements
   - Verify key format
   - Ensure sufficient memory for large data

2. **"Decryption failed" errors**:
   - Verify same key used for encryption/decryption
   - Check for data corruption during storage/transmission
   - Ensure proper Base64 encoding/decoding

3. **Performance issues**:
   - Use appropriate algorithms for data size
   - Consider data chunking for large payloads
   - Optimize key management

### Debugging Tips

- Enable logging temporarily for development
- Verify key consistency between encryption and decryption
- Test with small data samples first
- Check file permissions for RSA key files
