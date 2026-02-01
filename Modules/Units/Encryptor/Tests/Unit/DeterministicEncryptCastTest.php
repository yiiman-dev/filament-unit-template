<?php

use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;
use Units\Encryptor\Casts\EncryptCast;

class DeterministicEncryptCastTest extends TestCase
{
    private $key = 'test_key_for_deterministic_encryption_32_chars';


    public function testDeterministicCastEncryption()
    {
        $cast = new EncryptCast('deterministic', $this->key);

        $model = new class extends Model {};

        $originalValue = 'test_value_for_deterministic_cast';

        // Test encryption
        $encrypted = $cast->set($model, 'field', $originalValue, []);

        // Same value should encrypt to same result
        $encryptedAgain = $cast->set($model, 'field', $originalValue, []);

        $this->assertEquals($encrypted, $encryptedAgain);
    }

    public function testDeterministicCastRoundTrip()
    {
        $cast = new EncryptCast('deterministic', $this->key);

        $model = new class extends Model {};

        $originalValue = 'round_trip_test_value';

        // Encrypt
        $encrypted = $cast->set($model, 'field', $originalValue, []);

        // Decrypt
        $decrypted = $cast->get($model, 'field', $encrypted, []);

        $this->assertEquals($originalValue, $decrypted);
    }

    public function testDeterministicCastDifferentValues()
    {
        $cast = new EncryptCast('deterministic', $this->key);

        $model = new class extends Model {};

        $value1 = 'first_test_value';
        $value2 = 'second_test_value';

        $encrypted1 = $cast->set($model, 'field', $value1, []);
        $encrypted2 = $cast->set($model, 'field', $value2, []);

        // Different values should produce different encrypted results
        $this->assertNotEquals($encrypted1, $encrypted2);

        // Both should decrypt correctly
        $this->assertEquals($value1, $cast->get($model, 'field', $encrypted1, []));
        $this->assertEquals($value2, $cast->get($model, 'field', $encrypted2, []));
    }

    public function testDeterministicCastConsistencyAcrossInstances()
    {
        $model = new class extends Model {};

        $originalValue = 'consistency_test_value';

        $cast1 = new EncryptCast('deterministic', $this->key);
        $cast2 = new EncryptCast('deterministic', $this->key);

        $encrypted1 = $cast1->set($model, 'field', $originalValue, []);
        $encrypted2 = $cast2->set($model, 'field', $originalValue, []);

        // Two casts with same algorithm and key should produce same results
        $this->assertEquals($encrypted1, $encrypted2);

        // Both should decrypt correctly
        $this->assertEquals($originalValue, $cast1->get($model, 'field', $encrypted2, []));
        $this->assertEquals($originalValue, $cast2->get($model, 'field', $encrypted1, []));
    }

    public function testDeterministicCastNullHandling()
    {
        $cast = new EncryptCast('deterministic', $this->key);

        $model = new class extends Model {};

        // Null should remain null
        $this->assertNull($cast->set($model, 'field', null, []));
        $this->assertNull($cast->get($model, 'field', null, []));
    }

    public function testDeterministicCastDatabaseSearchSimulation()
    {
        $cast = new EncryptCast('deterministic', $this->key);

        $model = new class extends Model {};

        $searchValue = 'searchable_encrypted_value';

        // Encrypt the same value multiple times (simulating different records)
        $encrypted1 = $cast->set($model, 'field', $searchValue, []);
        $encrypted2 = $cast->set($model, 'field', $searchValue, []);
        $encrypted3 = $cast->set($model, 'field', $searchValue, []);

        // All should be identical (perfect for database searching)
        $this->assertEquals($encrypted1, $encrypted2);
        $this->assertEquals($encrypted2, $encrypted3);
        $this->assertEquals($encrypted1, $encrypted3);

        // All should decrypt back to the same value
        $this->assertEquals($searchValue, $cast->get($model, 'field', $encrypted1, []));
        $this->assertEquals($searchValue, $cast->get($model, 'field', $encrypted2, []));
        $this->assertEquals($searchValue, $cast->get($model, 'field', $encrypted3, []));
    }

    public function testDeterministicCastWithDifferentAlgorithms()
    {
        $deterministicCast = new EncryptCast('deterministic', $this->key);
        $aesCast = new EncryptCast('aes', $this->key);

        $model = new class extends Model {};

        $value = 'comparison_test_value';

        $deterministicEncrypted = $deterministicCast->set($model, 'field', $value, []);
        $aesEncrypted = $aesCast->set($model, 'field', $value, []);

        // Different algorithms should produce different results
        $this->assertNotEquals($deterministicEncrypted, $aesEncrypted);

        // Both should decrypt correctly with their respective casts
        $this->assertEquals($value, $deterministicCast->get($model, 'field', $deterministicEncrypted, []));
        $this->assertEquals($value, $aesCast->get($model, 'field', $aesEncrypted, []));
    }
}
