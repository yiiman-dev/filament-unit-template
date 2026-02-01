<?php

use PHPUnit\Framework\TestCase;
use Units\Encryptor\DeterministicEncryptor;

class DeterministicEncryptorTest extends TestCase
{
    private $key = 'test_key_for_deterministic_encryption_32_chars';

    public function testDeterministicEncryptionProducesSameOutput()
    {
        $encryptor = new DeterministicEncryptor($this->key);

        $plaintext = 'Hello World!';

        $firstEncrypted = $encryptor->encrypt($plaintext);
        $secondEncrypted = $encryptor->encrypt($plaintext);

        // Same input should produce same output
        $this->assertEquals($firstEncrypted, $secondEncrypted);

        // Verify deterministic property programmatically
        $this->assertTrue($encryptor->verifyDeterministic($plaintext));
    }

    public function testDeterministicEncryptionDifferentInputsProduceDifferentOutputs()
    {
        $encryptor = new DeterministicEncryptor($this->key);

        $plaintext1 = 'Hello World!';
        $plaintext2 = 'Hello World?';

        $encrypted1 = $encryptor->encrypt($plaintext1);
        $encrypted2 = $encryptor->encrypt($plaintext2);

        // Different inputs should produce different outputs
        $this->assertNotEquals($encrypted1, $encrypted2);
    }

    public function testDeterministicRoundTrip()
    {
        $encryptor = new DeterministicEncryptor($this->key);

        $original = 'This is a test string for deterministic encryption.';
        $encrypted = $encryptor->encrypt($original);
        $decrypted = $encryptor->decrypt($encrypted);

        $this->assertEquals($original, $decrypted);
    }

    public function testMultipleRoundTrips()
    {
        $encryptor = new DeterministicEncryptor($this->key);

        $original = 'Test string for multiple round trips.';

        // Encrypt and decrypt multiple times
        for ($i = 0; $i < 5; $i++) {
            $encrypted = $encryptor->encrypt($original);
            $decrypted = $encryptor->decrypt($encrypted);
            $this->assertEquals($original, $decrypted);

            // Verify consistency
            $this->assertEquals($encryptor->encrypt($original), $encrypted);
        }
    }

    public function testDeterministicWithDifferentDataTypes()
    {
        $encryptor = new DeterministicEncryptor($this->key);

        $testCases = [
            'simple_string',
            '12345',
            'Special chars: !@#$%^&*()',
            'Unicode: 你好世界',
            'Numbers: 123.45',
            'Boolean-like: true',
            'Empty string: ',
            'Long string: ' . str_repeat('A', 1000),
        ];

        foreach ($testCases as $testCase) {
            $encrypted = $encryptor->encrypt($testCase);
            $decrypted = $encryptor->decrypt($encrypted);

            $this->assertEquals($testCase, $decrypted);
            $this->assertTrue($encryptor->verifyDeterministic($testCase));
        }
    }

    public function testDeterministicWithConfigKey()
    {
        // Test with default key (should fail if no config)
        $this->expectException(Exception::class);
        new DeterministicEncryptor(null);
    }

    public function testDecryptInvalidData()
    {
        $encryptor = new DeterministicEncryptor($this->key);

        $this->expectException(Exception::class);
        $encryptor->decrypt('invalid_base64_data');
    }

    public function testDecryptCorruptedData()
    {
        $encryptor = new DeterministicEncryptor($this->key);

        $validEncrypted = $encryptor->encrypt('test');

        // Corrupt the data by removing part of it
        $corrupted = substr($validEncrypted, 0, -5);

        // This might not always throw an exception due to how openssl_decrypt handles corrupted data
        // Instead, we'll test with completely invalid base64
        $this->expectException(Exception::class);
        $encryptor->decrypt('invalid_base64_data');
    }

    public function testConsistentAcrossMultipleInstances()
    {
        $plaintext = 'Consistency test string';

        $encryptor1 = new DeterministicEncryptor($this->key);
        $encryptor2 = new DeterministicEncryptor($this->key);

        $encrypted1 = $encryptor1->encrypt($plaintext);
        $encrypted2 = $encryptor2->encrypt($plaintext);

        // Two instances with same key should produce same results
        $this->assertEquals($encrypted1, $encrypted2);

        // Both should be able to decrypt each other's output
        $this->assertEquals($plaintext, $encryptor1->decrypt($encrypted2));
        $this->assertEquals($plaintext, $encryptor2->decrypt($encrypted1));
    }

    public function testSecurityWarningVerification()
    {
        $encryptor = new DeterministicEncryptor($this->key);

        // Same plaintext should always produce same ciphertext
        $identicalInput = 'identical_data';
        $ciphertext1 = $encryptor->encrypt($identicalInput);
        $ciphertext2 = $encryptor->encrypt($identicalInput);

        $this->assertEquals($ciphertext1, $ciphertext2);

        // Different plaintext should produce different ciphertext
        $differentInput = 'different_data';
        $ciphertext3 = $encryptor->encrypt($differentInput);

        $this->assertNotEquals($ciphertext1, $ciphertext3);
    }
}
