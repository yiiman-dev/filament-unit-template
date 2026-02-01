<?php

namespace Units\Encryptor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Units\Encryptor\RijndaelEncryptor;

class RijndaelEncryptorTest extends TestCase
{
    public function testEncryptionDecryption()
    {
        $encryptor = new RijndaelEncryptor('test-key-32-characters-1234567890', 256);
        $originalData = 'Hello World!';
        
        $encrypted = $encryptor->encrypt($originalData);
        $decrypted = $encryptor->decrypt($encrypted);
        
        $this->assertEquals($originalData, $decrypted);
        $this->assertNotEquals($originalData, $encrypted);
    }
    
    public function testDifferentBlockSizes()
    {
        $originalData = 'Hello World!';
        
        // Test with 128-bit block size
        $encryptor128 = new RijndaelEncryptor('test-key-32-characters-1234567890', 128);
        $encrypted128 = $encryptor128->encrypt($originalData);
        $decrypted128 = $encryptor128->decrypt($encrypted128);
        $this->assertEquals($originalData, $decrypted128);
        
        // Test with 192-bit block size
        $encryptor192 = new RijndaelEncryptor('test-key-32-characters-1234567890', 192);
        $encrypted192 = $encryptor192->encrypt($originalData);
        $decrypted192 = $encryptor192->decrypt($encrypted192);
        $this->assertEquals($originalData, $decrypted192);
        
        // Test with 256-bit block size
        $encryptor256 = new RijndaelEncryptor('test-key-32-characters-1234567890', 256);
        $encrypted256 = $encryptor256->encrypt($originalData);
        $decrypted256 = $encryptor256->decrypt($encrypted256);
        $this->assertEquals($originalData, $decrypted256);
    }
    
    public function testDifferentCipherModes()
    {
        $encryptor = new RijndaelEncryptor('test-key-32-characters-1234567890', 128);
        $originalData = 'Hello World!';
        
        $modes = ['CBC', 'CFB', 'OFB']; // ECB is tested separately
        
        foreach ($modes as $mode) {
            $encrypted = $encryptor->encryptWithMode($originalData, $mode);
            $decrypted = $encryptor->decryptWithMode($encrypted, $mode);
            $this->assertEquals($originalData, $decrypted);
        }
    }
    
    public function testEcbMode()
    {
        $encryptor = new RijndaelEncryptor('test-key-32-characters-1234567890', 128);
        $originalData = 'Hello World!';
        
        $encrypted = $encryptor->encryptWithMode($originalData, 'ECB');
        $decrypted = $encryptor->decryptWithMode($encrypted, 'ECB');
        $this->assertEquals($originalData, $decrypted);
    }
    
    public function testEmptyStringEncryption()
    {
        $encryptor = new RijndaelEncryptor('test-key-32-characters-1234567890', 256);
        $originalData = '';
        
        $encrypted = $encryptor->encrypt($originalData);
        $decrypted = $encryptor->decrypt($encrypted);
        
        $this->assertEquals($originalData, $decrypted);
    }
    
    public function testLongStringEncryption()
    {
        $encryptor = new RijndaelEncryptor('test-key-32-characters-1234567890', 256);
        $originalData = str_repeat('This is a longer test string. ', 100);
        
        $encrypted = $encryptor->encrypt($originalData);
        $decrypted = $encryptor->decrypt($encrypted);
        
        $this->assertEquals($originalData, $decrypted);
    }
    
    public function testBinaryDataEncryption()
    {
        $encryptor = new RijndaelEncryptor('test-key-32-characters-1234567890', 256);
        $originalData = "\x0\x01\x02\x03\xFF\xFE\xFD\xFC";
        
        $encrypted = $encryptor->encrypt($originalData);
        $decrypted = $encryptor->decrypt($encrypted);
        
        $this->assertEquals($originalData, $decrypted);
    }
    
    public function testDifferentKeys()
    {
        $key1 = 'test-key-32-characters-1234567890';
        $key2 = 'different-key-32-char-123456789012';
        
        $encryptor1 = new RijndaelEncryptor($key1, 256);
        $encryptor2 = new RijndaelEncryptor($key2, 256);
        
        $originalData = 'Secret Message';
        
        $encrypted1 = $encryptor1->encrypt($originalData);
        $encrypted2 = $encryptor2->encrypt($originalData);
        
        $this->assertNotEquals($encrypted1, $encrypted2);
        
        $decrypted1 = $encryptor1->decrypt($encrypted1);
        $decrypted2 = $encryptor2->decrypt($encrypted2);
        
        $this->assertEquals($originalData, $decrypted1);
        $this->assertEquals($originalData, $decrypted2);
    }
    
    public function testInvalidBlockSize()
    {
        $this->expectException(\Exception::class);
        new RijndaelEncryptor('test-key', 64); // Invalid block size
    }
    
    public function testInvalidCipherMode()
    {
        $encryptor = new RijndaelEncryptor('test-key-32-characters-1234567890', 128);
        
        $this->expectException(\Exception::class);
        $encryptor->encryptWithMode('test data', 'INVALID');
    }
    
    public function testGetBlockSize()
    {
        $encryptor128 = new RijndaelEncryptor('test-key-32-characters-1234567890', 128);
        $encryptor256 = new RijndaelEncryptor('test-key-32-characters-1234567890', 256);
        
        $this->assertEquals(128, $encryptor128->getBlockSize());
        $this->assertEquals(256, $encryptor256->getBlockSize());
    }
}