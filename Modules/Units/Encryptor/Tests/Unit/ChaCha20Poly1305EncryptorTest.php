<?php

namespace Units\Encryptor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Units\Encryptor\ChaCha20Poly1305Encryptor;

class ChaCha20Poly1305EncryptorTest extends TestCase
{
    public function testEncryptionDecryption()
    {
        $encryptor = new ChaCha20Poly1305Encryptor();
        $originalData = 'Hello World!';
        $key = ChaCha20Poly1305Encryptor::generateKey();
        
        $encrypted = $encryptor->encrypt($originalData, $key);
        $decrypted = $encryptor->decrypt($encrypted, $key);
        
        $this->assertEquals($originalData, $decrypted);
        $this->assertNotEquals($originalData, $encrypted);
    }
    
    public function testKeyGeneration()
    {
        $key = ChaCha20Poly1305Encryptor::generateKey();
        $this->assertEquals(32, strlen($key)); // 256-bit key
        $this->assertIsString($key);
    }
    
    public function testEmptyStringEncryption()
    {
        $encryptor = new ChaCha20Poly1305Encryptor();
        $originalData = '';
        $key = ChaCha20Poly1305Encryptor::generateKey();
        
        $encrypted = $encryptor->encrypt($originalData, $key);
        $decrypted = $encryptor->decrypt($encrypted, $key);
        
        $this->assertEquals($originalData, $decrypted);
    }
    
    public function testLongStringEncryption()
    {
        $encryptor = new ChaCha20Poly1305Encryptor();
        $originalData = str_repeat('This is a longer test string. ', 100);
        $key = ChaCha20Poly1305Encryptor::generateKey();
        
        $encrypted = $encryptor->encrypt($originalData, $key);
        $decrypted = $encryptor->decrypt($encrypted, $key);
        
        $this->assertEquals($originalData, $decrypted);
    }
    
    public function testBinaryDataEncryption()
    {
        $encryptor = new ChaCha20Poly1305Encryptor();
        $originalData = "\x0\x01\x02\x03\xFF\xFE\xFD\xFC";
        $key = ChaCha20Poly1305Encryptor::generateKey();
        
        $encrypted = $encryptor->encrypt($originalData, $key);
        $decrypted = $encryptor->decrypt($encrypted, $key);
        
        $this->assertEquals($originalData, $decrypted);
    }
    
    public function testAadEncryptionDecryption()
    {
        $encryptor = new ChaCha20Poly1305Encryptor();
        $originalData = 'Hello World!';
        $aad = 'Additional Authenticated Data';
        $key = ChaCha20Poly1305Encryptor::generateKey();
        
        $result = $encryptor->encryptWithAad($originalData, $aad, $key);
        $decrypted = $encryptor->decryptWithAad($result, $aad, $key);
        
        $this->assertEquals($originalData, $decrypted);
    }
    
    public function testAadTamperingDetection()
    {
        $encryptor = new ChaCha20Poly1305Encryptor();
        $originalData = 'Hello World!';
        $aad1 = 'Original AAD';
        $aad2 = 'Modified AAD';
        $key = ChaCha20Poly1305Encryptor::generateKey();
        
        $result = $encryptor->encryptWithAad($originalData, $aad1, $key);
        
        $this->expectException(\Exception::class);
        $encryptor->decryptWithAad($result, $aad2, $key);
    }
    
    public function testInvalidKeyDecryption()
    {
        $encryptor = new ChaCha20Poly1305Encryptor();
        $originalData = 'Test data';
        $key1 = ChaCha20Poly1305Encryptor::generateKey();
        $key2 = ChaCha20Poly1305Encryptor::generateKey();
        
        $encrypted = $encryptor->encrypt($originalData, $key1);
        
        $this->expectException(\Exception::class);
        $encryptor->decrypt($encrypted, $key2);
    }
    
    public function testInvalidDataDecryption()
    {
        $encryptor = new ChaCha20Poly1305Encryptor();
        $key = ChaCha20Poly1305Encryptor::generateKey();
        
        $this->expectException(\Exception::class);
        $encryptor->decrypt('invalid-base64-string!', $key);
    }
    
    public function testOpenSslSupportCheck()
    {
        $availableCiphers = openssl_get_cipher_methods();
        $this->assertTrue(in_array('chacha20-poly1305', array_map('strtolower', $availableCiphers)));
    }
}