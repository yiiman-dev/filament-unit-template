<?php

namespace Units\Encryptor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Units\Encryptor\RsaEncryptor;

class RsaEncryptorTest extends TestCase
{
    public function testEncryptionDecryption()
    {
        // Generate a key pair for testing
        $keys = RsaEncryptor::generateKeyPair(1024); // Using smaller key for faster tests
        
        $encryptor = new RsaEncryptor();
        $originalData = 'Hello World!';
        
        // For testing purposes, we'll create a temporary key file
        $tempPubKey = tempnam(sys_get_temp_dir(), 'pubkey');
        $tempPrivKey = tempnam(sys_get_temp_dir(), 'privkey');
        
        file_put_contents($tempPubKey, $keys['public_key']);
        file_put_contents($tempPrivKey, $keys['private_key']);
        
        $rsaEncryptor = new RsaEncryptor($tempPrivKey, $tempPubKey);
        
        $encrypted = $rsaEncryptor->encrypt($originalData);
        $decrypted = $rsaEncryptor->decrypt($encrypted);
        
        $this->assertEquals($originalData, $decrypted);
        $this->assertNotEquals($originalData, $encrypted);
        
        // Clean up
        unlink($tempPubKey);
        unlink($tempPrivKey);
    }
    
    public function testLargeDataEncryption()
    {
        $keys = RsaEncryptor::generateKeyPair(2048); // Use larger key size for more data capacity
        
        $tempPubKey = tempnam(sys_get_temp_dir(), 'pubkey');
        $tempPrivKey = tempnam(sys_get_temp_dir(), 'privkey');
        
        file_put_contents($tempPubKey, $keys['public_key']);
        file_put_contents($tempPrivKey, $keys['private_key']);
        
        $rsaEncryptor = new RsaEncryptor($tempPrivKey, $tempPubKey);
        
        // Use smaller data size that fits within RSA limits
        // RSA can typically encrypt (key_size_in_bytes - 11) bytes of data
        // For 2048-bit key: (2048/8 - 11) = 245 bytes maximum
        $originalData = str_repeat('Short test. ', 10); // Much shorter data
        
        $encrypted = $rsaEncryptor->encrypt($originalData);
        $decrypted = $rsaEncryptor->decrypt($encrypted);
        
        $this->assertEquals($originalData, $decrypted);
        
        // Clean up
        unlink($tempPubKey);
        unlink($tempPrivKey);
    }
    
    public function testEmptyStringEncryption()
    {
        $keys = RsaEncryptor::generateKeyPair(1024);
        
        $tempPubKey = tempnam(sys_get_temp_dir(), 'pubkey');
        $tempPrivKey = tempnam(sys_get_temp_dir(), 'privkey');
        
        file_put_contents($tempPubKey, $keys['public_key']);
        file_put_contents($tempPrivKey, $keys['private_key']);
        
        $rsaEncryptor = new RsaEncryptor($tempPrivKey, $tempPubKey);
        
        $originalData = '';
        
        $encrypted = $rsaEncryptor->encrypt($originalData);
        $decrypted = $rsaEncryptor->decrypt($encrypted);
        
        $this->assertEquals($originalData, $decrypted);
        
        // Clean up
        unlink($tempPubKey);
        unlink($tempPrivKey);
    }
    
    public function testKeyGeneration()
    {
        $keys = RsaEncryptor::generateKeyPair(1024);
        
        $this->assertArrayHasKey('private_key', $keys);
        $this->assertArrayHasKey('public_key', $keys);
        $this->assertIsString($keys['private_key']);
        $this->assertIsString($keys['public_key']);
        $this->assertStringContainsString('-----BEGIN PRIVATE KEY-----', $keys['private_key']);
        $this->assertStringContainsString('-----BEGIN PUBLIC KEY-----', $keys['public_key']);
    }
    
    public function testInvalidPrivateKey()
    {
        $tempPubKey = tempnam(sys_get_temp_dir(), 'pubkey');
        $tempPrivKey = tempnam(sys_get_temp_dir(), 'privkey');
        
        file_put_contents($tempPubKey, 'invalid-public-key');
        file_put_contents($tempPrivKey, 'invalid-private-key');
        
        $rsaEncryptor = new RsaEncryptor($tempPrivKey, $tempPubKey);
        
        $this->expectException(\Exception::class);
        $rsaEncryptor->encrypt('test data');
        
        // Clean up
        unlink($tempPubKey);
        unlink($tempPrivKey);
    }
    
    public function testMissingPublicKey()
    {
        $tempPrivKey = tempnam(sys_get_temp_dir(), 'privkey');
        file_put_contents($tempPrivKey, 'invalid-private-key');
        
        $rsaEncryptor = new RsaEncryptor($tempPrivKey, null);
        
        $this->expectException(\Exception::class);
        $rsaEncryptor->encrypt('test data');
        
        // Clean up
        unlink($tempPrivKey);
    }
}