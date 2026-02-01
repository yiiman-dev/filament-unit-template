# Encryptor Module Requirements

## Overview
The Encryptor module provides various encryption and decryption algorithms for securing data in the application. It includes implementations of multiple cryptographic algorithms with standardized interfaces.

## System Requirements

### PHP Version
- **Minimum**: PHP 8.2+
- **Recommended**: PHP 8.2+ with latest security patches

### Required Extensions
```ini
extension=sodium
extension=openssl
```

### Memory Requirements
- Minimum 128MB memory limit (recommended 256MB+ for optimal performance)

## Supported Algorithms

### Symmetric Encryption
- **AES (Advanced Encryption Standard)**
  - AES-128-CBC, AES-192-CBC, AES-256-CBC
  - AES-128-GCM, AES-192-GCM, AES-256-GCM
  - Key sizes: 128, 192, 256 bits

- **ChaCha20-Poly1305**
  - Authenticated encryption
  - 256-bit key requirement
  - Nonce-based encryption

- **Sodium Crypto Secretbox**
  - XChaCha20-Poly1305
  - Authenticated encryption
  - 256-bit keys

### Asymmetric Encryption
- **RSA**
  - Key sizes: 1024, 2048, 3072, 4096 bits
  - PKCS#1 v1.5 and OAEP padding
  - Public/private key pairs

### Legacy/Alternative Algorithms
- **Camellia**
  - 128, 192, 256-bit key support
  - CBC, ECB, CFB, OFB modes

- **TripleDES (3DES)**
  - DES-EDE3-CBC (3-key)
  - DES-EDE-CBC (2-key)

- **Blowfish**
  - 32-448 bit variable key length
  - CBC, ECB modes

## Security Requirements

### Key Management
- Keys must be generated using cryptographically secure random number generators
- Key derivation using PBKDF2, scrypt, or Argon2 for password-based encryption
- Proper key rotation policies
- Secure key storage (consider using environment variables or secure vaults)

### IV/Nonce Requirements
- Unique IVs/nonces for each encryption operation
- Proper IV/nonce size according to algorithm specifications
- Never reuse IVs with the same key

### Data Handling
- Input validation and sanitization
- Maximum data size limits for asymmetric algorithms
- Secure deletion of temporary data

## Dependencies

### PHP Extensions
- `openssl` - Required for most symmetric and asymmetric operations
- `sodium` - Required for Sodium-based encryption algorithms
- `hash` - Built-in, required for key derivation

### External Libraries
- OpenSSL library (system-level)
- libsodium (system-level)

## Performance Considerations

### Algorithm Selection
- **AES-256-GCM**: Best for performance and security combination
- **ChaCha20-Poly1305**: Good for systems without AES-NI hardware acceleration
- **RSA**: Use only for small amounts of data; prefer hybrid encryption
- **Sodium**: Recommended for new applications

### Key Sizes vs Performance
- Larger key sizes = better security but slower performance
- RSA key size trade-offs:
  - 1024-bit: Fast but not recommended for production
  - 2048-bit: Good balance (minimum for production)
  - 4096-bit: Maximum security but slower

## Testing Requirements

### Unit Tests
- All encryption/decryption methods must have corresponding tests
- Test vectors for known answer tests
- Edge cases (empty strings, maximum sizes, etc.)
- Invalid input handling

### Security Tests
- Known plaintext/ciphertext attack scenarios
- Side-channel attack resistance verification
- Randomness quality testing for IVs/nonces

## Compatibility Requirements

### Operating Systems
- Linux (Ubuntu 20.04+, CentOS 8+)
- macOS (10.15+)
- Windows Server 2019+ (with WSL2 recommended)

### Framework Compatibility
- Laravel 10.x
- PHP 8.2+ compatibility

## Installation Notes

### Required Configuration
```bash
# Ensure OpenSSL extension is enabled
extension=openssl

# Enable Sodium extension (usually built-in PHP 7.2+)
extension=sodium

# Increase memory limit if processing large files
memory_limit = 256M
```

### Environment Variables
```env
# Optional: Custom encryption key (if not using Laravel's APP_KEY)
ENCRYPTOR_DEFAULT_KEY=your-custom-key-here
```

## Security Best Practices

### Do's
- ✅ Use authenticated encryption when possible (AES-GCM, ChaCha20-Poly1305)
- ✅ Generate keys using secure random functions
- ✅ Validate inputs before encryption/decryption
- ✅ Use appropriate key sizes for security requirements
- ✅ Implement proper error handling without leaking sensitive information

### Don'ts
- ❌ Reuse IVs/nonces with the same key
- ❌ Store keys in plain text
- ❌ Use weak passwords for key derivation
- ❌ Expose internal errors to end users
- ❌ Use deprecated algorithms for new implementations

## Maintenance Requirements

### Regular Updates
- Keep PHP and extensions updated
- Monitor for cryptographic vulnerabilities
- Update test vectors regularly
- Review and update key rotation policies

### Monitoring
- Log encryption/decryption failures
- Monitor performance metrics
- Track key usage and rotation
- Audit access to encrypted data