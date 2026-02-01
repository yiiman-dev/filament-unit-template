# Encryptor Usage Examples

## Quick Start Guide

### 1. Basic Usage with Default AES Encryption

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Units\Encryptor\Casts\EncryptCast;

class User extends Model
{
    protected $fillable = ['name', 'email', 'ssn', 'phone'];
    
    protected $casts = [
        'ssn' => EncryptCast::class, // Uses AES-256-CBC by default
    ];
    
    // Usage
    $user = new User(['name' => 'John', 'ssn' => '123-45-6789']);
    $user->save(); // SSN is automatically encrypted before saving
    
    $retrievedUser = User::find($user->id);
    echo $retrievedUser->ssn; // SSN is automatically decrypted when accessing
```

### 2. Multiple Encryption Types on Same Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Units\Encryptor\Casts\EncryptCast;

class ConfidentialRecord extends Model
{
    protected $casts = [
        'personal_id' => EncryptCast::class . ':aes',           // AES encryption
        'reference_no' => EncryptCast::class . ':blowfish',     // Blowfish encryption  
        'notes' => EncryptCast::class . ':camellia',           // Camellia encryption
        'backup_code' => EncryptCast::class . ':twofish',      // Twofish encryption
    ];
}
```

### 3. Custom Key Usage

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Units\Encryptor\Casts\EncryptCast;

class SensitiveData extends Model
{
    protected $casts = [
        'secret_key' => EncryptCast::class . ':aes,my_custom_app_key',
        'auth_token' => EncryptCast::class . ':rijndael,another_secure_key',
    ];
}
```

### 4. RSA Asymmetric Encryption

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Units\Encryptor\Casts\EncryptCast;

class SecureMessage extends Model
{
    protected $casts = [
        'message' => EncryptCast::class . ':rsa,null,{"private_key_path":"/storage/keys/app.rsa","public_key_path":"/storage/keys/app.rsa.pub"}',
    ];
}

// Note: RSA keys must be generated separately
// Generate RSA keys:
// openssl genrsa -out app.rsa 2048
// openssl rsa -in app.rsa -pubout -out app.rsa.pub
```

### 5. ChaCha20-Poly1305 Authenticated Encryption

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Units\Encryptor\Casts\EncryptCast;

class SecureTransaction extends Model
{
    protected $casts = [
        'transaction_data' => EncryptCast::class . ':chacha20poly1305',
    ];
}
```

### 6. Base64 Obfuscation (No Security)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Units\Encryptor\Casts\EncryptCast;

class TempData extends Model
{
    protected $casts = [
        'temporary_data' => EncryptCast::class . ':base64', // Only for obfuscation!
    ];
}

// WARNING: Base64 provides NO security - only obfuscation
// Never use for sensitive data
```

### 7. Programmatic Usage Outside Models

```php
<?php

use Units\Encryptor\AesEncryptor;
use Units\Encryptor\RsaEncryptor;
use Units\Encryptor\Base64Encryptor;

// AES Encryption
$aes = new AesEncryptor(config('app.key'));
$encrypted = $aes->encrypt('Sensitive data');
$decrypted = $aes->decrypt($encrypted);

// RSA Encryption (requires key files)
$rsa = new RsaEncryptor('/path/to/private.pem', '/path/to/public.pem');
$encrypted = $rsa->encrypt('Secure message');
$decrypted = $rsa->decrypt($encrypted);

// Base64 (obfuscation only)
$base64 = new Base64Encryptor();
$encoded = $base64->encode('Data to obfuscate');
$decoded = $base64->decode($encoded);
```

### 8. Complete Model Example

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Units\Encryptor\Casts\EncryptCast;

class CustomerProfile extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'email', 
        'ssn',
        'credit_card',
        'medical_notes',
        'tax_info'
    ];
    
    protected $hidden = [
        'ssn',
        'credit_card',
        'medical_notes',
        'tax_info'
    ];
    
    protected $casts = [
        'ssn' => EncryptCast::class . ':aes',                    // Social Security Number
        'credit_card' => EncryptCast::class . ':aes,card_key',   // Credit card with custom key
        'medical_notes' => EncryptCast::class . ':camellia',     // Medical information
        'tax_info' => EncryptCast::class . ':twofish',          // Tax information
    ];
    
    // Accessors for encrypted fields (if needed)
    public function getSsnAttribute($value)
    {
        // Additional processing if needed
        return $value;
    }
    
    // Mutators for encrypted fields (if needed)
    public function setSsnAttribute($value)
    {
        $this->attributes['ssn'] = $value;
    }
}
```

### 9. Database Migration Example

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            
            // Encrypted fields - stored as TEXT for flexibility
            $table->text('ssn')->nullable();           // Encrypted SSN
            $table->text('credit_card')->nullable();   // Encrypted credit card
            $table->longText('medical_notes')->nullable(); // Encrypted medical notes
            $table->longText('tax_info')->nullable();  // Encrypted tax info
            
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('customer_profiles');
    }
};
```

### 10. Testing Encrypted Models

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\CustomerProfile;

class EncryptedModelTest extends TestCase
{
    public function test_encrypted_fields_are_transparently_handled()
    {
        $profile = CustomerProfile::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'ssn' => '123-45-6789',
            'credit_card' => '4111-1111-1111-1111',
        ]);
        
        // Verify data was saved
        $this->assertNotNull($profile->id);
        
        // Verify encrypted data can be retrieved
        $retrieved = CustomerProfile::find($profile->id);
        $this->assertEquals('123-45-6789', $retrieved->ssn);
        $this->assertEquals('4111-1111-1111-1111', $retrieved->credit_card);
        
        // Verify database contains encrypted data
        $rawData = \DB::table('customer_profiles')->where('id', $profile->id)->first();
        $this->assertNotEquals('123-45-6789', $rawData->ssn); // Should be encrypted
        $this->assertNotEquals('4111-1111-1111-1111', $rawData->credit_card); // Should be encrypted
    }
}
```

## Security Best Practices

### ✅ Recommended
- Use AES-256 for general encryption needs
- Store encryption keys in environment variables
- Use different keys for different data types
- Implement proper error handling
- Regular key rotation
- Audit encrypted data access

### ❌ Avoid
- Using Base64 for sensitive data (provides no security)
- Hardcoding encryption keys in source code
- Using weak passwords as encryption keys
- Sharing keys between different security domains
- Storing unencrypted sensitive data alongside encrypted data

## Performance Considerations

- RSA is slower than symmetric algorithms - use for key exchange only
- Large data should be chunked for encryption
- Consider caching frequently accessed encrypted data
- Monitor encryption/decryption performance in production
- Use appropriate algorithms based on data sensitivity and performance needs
