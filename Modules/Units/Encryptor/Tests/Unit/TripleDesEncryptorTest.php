<?php

namespace Units\Encryptor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Units\Encryptor\TripleDesEncryptor;

class TripleDesEncryptorTest extends TestCase
{
    public function testEncryptionDecryption()
    {
        $encryptor = new TripleDesEncryptor('test-key-32-characters-1234567890');
        $originalData = 'Hello World!';
        
        $encrypted = $encryptor->encrypt($originalData);
        $decrypted = $encryptor->decrypt($encrypted);
        
        $this->assertEquals($originalData, $decrypted);
        $this->assertNotEquals($originalData, $encrypted);
    }
    
    public function testTwoKeyEncryptionDecryption()
    {
        $encryptor = new TripleDesEncryptor('test-key-32-characters-1234567890');
        $originalData = 'Hello World!';
        
        $encrypted = $encryptor->encryptTwoKey($originalData);
        $decrypted = $encryptor->decryptTwoKey($encrypted);
        
        $this->assertEquals($originalData, $decrypted);
        $this->assertNotEquals($originalData, $encrypted);
    }
    
    public function testEmptyStringEncryption()
    {
        $encryptor = new TripleDesEncryptor('test-key-32-characters-1234567890');
        $originalData = '';
        
        $encrypted = $encryptor->encrypt($originalData);
        $decrypted = $encryptor->decrypt($encrypted);
        
        $this->assertEquals($originalData, $decrypted);
    }
    
    public function testLongStringEncryption()
    {
        $encryptor = new TripleDesEncryptor('test-key-32-characters-1234567890');
        $originalData = str_repeat('This is a longer test string. ', 100);
        
        $encrypted = $encryptor->encrypt($originalData);
        $decrypted = $encryptor->decrypt($encrypted);
        
        $this->assertEquals($originalData, $decrypted);
    }
    
    public function testBinaryDataEncryption()
    {
        $encryptor = new TripleDesEncryptor('test-key-32-characters-1234567890');
        $originalData = "\x0\x01\x02\x03\xFF\xFE\xFD\xFC";
        
        $encrypted = $encryptor->encrypt($originalData);
        $decrypted = $encryptor->decrypt($encrypted);
        
        $this->assertEquals($originalData, $decrypted);
    }
    
    public function testDifferentKeys()
    {
        $key1 = 'test-key-32-characters-1234567890';
        $key2 = 'different-key-32-char-123456789012';
        
        $encryptor1 = new TripleDesEncryptor($key1);
        $encryptor2 = new TripleDesEncryptor($key2);
        
        $originalData = 'Secret Message';
        
        $encrypted1 = $encryptor1->encrypt($originalData);
        $encrypted2 = $encryptor2->encrypt($originalData);
        
        $this->assertNotEquals($encrypted1, $encrypted2);
        
        $decrypted1 = $encryptor1->decrypt($encrypted1);
        $decrypted2 = $encryptor2->decrypt($encrypted2);
        
        $this->assertEquals($originalData, $decrypted1);
        $this->assertEquals($originalData, $decrypted2);
    }
    
    public function testInvalidDataDecryption()
    {
        $encryptor = new TripleDesEncryptor('test-key-32-characters-1234567890');
        
        $this->expectException(\Exception::class);
        $encryptor->decrypt('invalid-base64-string!');
    }
    
    public function testConfigKeyFallback()
    {
        $encryptor = new TripleDesEncryptor();
        $originalData = 'Hello World!';
        
        $encrypted = $encryptor->encrypt($originalData);
        $decrypted = $encryptor->decrypt($encrypted);
        
        $this->assertEquals($originalData, $decrypted);
    }
}