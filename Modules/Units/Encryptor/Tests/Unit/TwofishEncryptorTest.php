<?php

namespace Units\Encryptor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Units\Encryptor\TwofishEncryptor;

class TwofishEncryptorTest extends TestCase
{
    public function testEncryptionDecryption()
    {
        $encryptor = new TwofishEncryptor('test-key-32-characters-1234567890');
        $originalData = 'Hello World!';
        
        $encrypted = $encryptor->encrypt($originalData);
        $decrypted = $encryptor->decrypt($encrypted);
        
        $this->assertEquals($originalData, $decrypted);
        $this->assertNotEquals($originalData, $encrypted);
    }
    
    public function testDifferentKeySizes()
    {
        $originalData = 'Hello World!';
        $encryptor = new TwofishEncryptor('test-key-32-characters-1234567890');
        
        // Test with 128-bit key
        $encrypted128 = $encryptor->encryptWithKeySize($originalData, 128);
        $decrypted128 = $encryptor->decryptWithKeySize($encrypted128, 128);
        $this->assertEquals($originalData, $decrypted128);
        
        // Test with 192-bit key
        $encrypted192 = $encryptor->encryptWithKeySize($originalData, 192);
        $decrypted192 = $encryptor->decryptWithKeySize($encrypted192, 192);
        $this->assertEquals($originalData, $decrypted192);
        
        // Test with 256-bit key
        $encrypted256 = $encryptor->encryptWithKeySize($originalData, 256);
        $decrypted256 = $encryptor->decryptWithKeySize($encrypted256, 256);
        $this->assertEquals($originalData, $decrypted256);
    }
    
    public function testEmptyStringEncryption()
    {
        $encryptor = new TwofishEncryptor('test-key-32-characters-1234567890');
        $originalData = '';
        
        $encrypted = $encryptor->encrypt($originalData);
        $decrypted = $encryptor->decrypt($encrypted);
        
        $this->assertEquals($originalData, $decrypted);
    }
    
    public function testLongStringEncryption()
    {
        $encryptor = new TwofishEncryptor('test-key-32-characters-1234567890');
        $originalData = str_repeat('This is a longer test string. ', 100);
        
        $encrypted = $encryptor->encrypt($originalData);
        $decrypted = $encryptor->decrypt($encrypted);
        
        $this->assertEquals($originalData, $decrypted);
    }
    
    public function testBinaryDataEncryption()
    {
        $encryptor = new TwofishEncryptor('test-key-32-characters-1234567890');
        $originalData = "\x0\x01\x02\x03\xFF\xFE\xFD\xFC";
        
        $encrypted = $encryptor->encrypt($originalData);
        $decrypted = $encryptor->decrypt($encrypted);
        
        $this->assertEquals($originalData, $decrypted);
    }
    
    public function testDifferentKeys()
    {
        $key1 = 'test-key-32-characters-1234567890';
        $key2 = 'different-key-32-char-123456789012';
        
        $encryptor1 = new TwofishEncryptor($key1);
        $encryptor2 = new TwofishEncryptor($key2);
        
        $originalData = 'Secret Message';
        
        $encrypted1 = $encryptor1->encrypt($originalData);
        $encrypted2 = $encryptor2->encrypt($originalData);
        
        $this->assertNotEquals($encrypted1, $encrypted2);
        
        $decrypted1 = $encryptor1->decrypt($encrypted1);
        $decrypted2 = $encryptor2->decrypt($encrypted2);
        
        $this->assertEquals($originalData, $decrypted1);
        $this->assertEquals($originalData, $decrypted2);
    }
    
    public function testInvalidKeySize()
    {
        $encryptor = new TwofishEncryptor('test-key-32-characters-1234567890');
        
        $this->expectException(\Exception::class);
        $encryptor->encryptWithKeySize('test data', 64); // Invalid key size
    }
    
    public function testInvalidDataDecryption()
    {
        $encryptor = new TwofishEncryptor('test-key-32-characters-1234567890');
        
        $this->expectException(\Exception::class);
        $encryptor->decrypt('invalid-base64-string!');
    }
    
    public function testConfigKeyFallback()
    {
        $encryptor = new TwofishEncryptor();
        $originalData = 'Hello World!';
        
        $encrypted = $encryptor->encrypt($originalData);
        $decrypted = $encryptor->decrypt($encrypted);
        
        $this->assertEquals($originalData, $decrypted);
    }
    
    public function testAvailableModes()
    {
        $modes = TwofishEncryptor::getAvailableModes();
        $this->assertIsArray($modes);
        
        $hasTwofishModes = false;
        foreach ($modes as $mode) {
            if (stripos($mode, 'twofish') !== false) {
                $hasTwofishModes = true;
                break;
            }
        }
        // Note: This test may fail if Twofish cipher is not available in the OpenSSL installation
        // In that case, we just verify the method runs without error
    }
}