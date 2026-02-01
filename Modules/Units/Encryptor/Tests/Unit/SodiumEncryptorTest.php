<?php

namespace Units\Encryptor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Units\Encryptor\SodiumEncryptor;

class SodiumEncryptorTest extends TestCase
{
    public function testEncryptionDecryption()
    {
        $encryptor = new SodiumEncryptor();
        $originalData = 'Hello World!';
        $key = sodium_crypto_secretbox_keygen();
        
        $encrypted = $encryptor->encrypt($originalData, $key);
        $decrypted = $encryptor->decrypt($encrypted, $key);
        
        $this->assertEquals($originalData, $decrypted);
        $this->assertNotEquals($originalData, $encrypted);
    }
    
    public function testEncryptionWithProvidedKey()
    {
        $encryptor = new SodiumEncryptor();
        $originalData = 'Secret Message';
        $key = sodium_crypto_secretbox_keygen();
        
        $encrypted = $encryptor->encrypt($originalData, $key);
        $decrypted = $encryptor->decrypt($encrypted, $key);
        
        $this->assertEquals($originalData, $decrypted);
    }
    
    public function testKeyGeneration()
    {
        $key = SodiumEncryptor::generateKey();
        $this->assertEquals(SODIUM_CRYPTO_SECRETBOX_KEYBYTES, strlen($key));
        $this->assertIsString($key);
    }
    
    public function testEmptyStringEncryption()
    {
        $encryptor = new SodiumEncryptor();
        $originalData = '';
        $key = sodium_crypto_secretbox_keygen();
        
        $encrypted = $encryptor->encrypt($originalData, $key);
        $decrypted = $encryptor->decrypt($encrypted, $key);
        
        $this->assertEquals($originalData, $decrypted);
    }
    
    public function testLongStringEncryption()
    {
        $encryptor = new SodiumEncryptor();
        $originalData = str_repeat('This is a longer test string. ', 100);
        $key = sodium_crypto_secretbox_keygen();
        
        $encrypted = $encryptor->encrypt($originalData, $key);
        $decrypted = $encryptor->decrypt($encrypted, $key);
        
        $this->assertEquals($originalData, $decrypted);
    }
    
    public function testBinaryDataEncryption()
    {
        $encryptor = new SodiumEncryptor();
        $originalData = "\x0\x01\x02\x03\xFF\xFE\xFD\xFC";
        $key = sodium_crypto_secretbox_keygen();
        
        $encrypted = $encryptor->encrypt($originalData, $key);
        $decrypted = $encryptor->decrypt($encrypted, $key);
        
        $this->assertEquals($originalData, $decrypted);
    }
    
    public function testInvalidKeyDecryption()
    {
        $encryptor = new SodiumEncryptor();
        $originalData = 'Test data';
        $key1 = sodium_crypto_secretbox_keygen();
        $key2 = sodium_crypto_secretbox_keygen();
        
        $encrypted = $encryptor->encrypt($originalData, $key1);
        
        $this->expectException(\Exception::class);
        $encryptor->decrypt($encrypted, $key2);
    }
    
    public function testPasswordEncryptionDecryption()
    {
        $encryptor = new SodiumEncryptor();
        $originalData = 'Password protected data';
        $password = 'my-secret-password';
        
        $result = $encryptor->encryptWithPassword($originalData, $password);
        $decrypted = $encryptor->decryptWithPassword($result, $password);
        
        $this->assertEquals($originalData, $decrypted);
    }
    
    public function testPasswordDecryptionWithWrongPassword()
    {
        $encryptor = new SodiumEncryptor();
        $originalData = 'Password protected data';
        $password1 = 'password1';
        $password2 = 'password2';
        
        $result = $encryptor->encryptWithPassword($originalData, $password1);
        
        $this->expectException(\Exception::class);
        $encryptor->decryptWithPassword($result, $password2);
    }
    
    public function testSodiumExtensionCheck()
    {
        $this->assertTrue(extension_loaded('sodium'));
    }
}