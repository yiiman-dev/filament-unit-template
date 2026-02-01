<?php

namespace Units\Encryptor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Units\Encryptor\Base64Encryptor;

class Base64EncryptorTest extends TestCase
{
    public function testEncodeDecode()
    {
        $encryptor = new Base64Encryptor();
        $originalData = 'Hello World!';
        
        $encoded = $encryptor->encode($originalData);
        $decoded = $encryptor->decode($encoded);
        
        $this->assertEquals($originalData, $decoded);
        $this->assertNotEquals($originalData, $encoded);
        $this->assertEquals(base64_encode($originalData), $encoded);
    }
    
    public function testSafeEncodeDecode()
    {
        $encryptor = new Base64Encryptor();
        $originalData = 'Hello World with +/= characters!';
        
        $encoded = $encryptor->safeEncode($originalData);
        $decoded = $encryptor->safeDecode($encoded);
        
        $this->assertEquals($originalData, $decoded);
        $this->assertNotEquals($originalData, $encoded);
        // Verify it's URL-safe (no +, /, or =)
        $this->assertStringNotContainsString('+', $encoded);
        $this->assertStringNotContainsString('/', $encoded);
        $this->assertStringNotContainsString('=', $encoded);
    }
    
    public function testEmptyStringEncoding()
    {
        $encryptor = new Base64Encryptor();
        $originalData = '';
        
        $encoded = $encryptor->encode($originalData);
        $decoded = $encryptor->decode($encoded);
        
        $this->assertEquals($originalData, $decoded);
    }
    
    public function testLongStringEncoding()
    {
        $encryptor = new Base64Encryptor();
        $originalData = str_repeat('This is a longer test string. ', 100);
        
        $encoded = $encryptor->encode($originalData);
        $decoded = $encryptor->decode($encoded);
        
        $this->assertEquals($originalData, $decoded);
    }
    
    public function testBinaryDataEncoding()
    {
        $encryptor = new Base64Encryptor();
        $originalData = "\x0\x01\x02\x03\xFF\xFE\xFD\xFC";
        
        $encoded = $encryptor->encode($originalData);
        $decoded = $encryptor->decode($encoded);
        
        $this->assertEquals($originalData, $decoded);
    }
    
    public function testXorEncryptionDecryption()
    {
        $encryptor = new Base64Encryptor();
        $originalData = 'Hello World!';
        $key = 'secret-key';
        
        $result = $encryptor->encodeWithXor($originalData, $key);
        $decoded = $encryptor->decodeWithXor($result, $key);
        
        $this->assertEquals($originalData, $decoded);
        $this->assertNotEquals($originalData, $result);
    }
    
    public function testXorWithDifferentKey()
    {
        $encryptor = new Base64Encryptor();
        $originalData = 'Hello World!';
        $key1 = 'secret-key-1';
        $key2 = 'secret-key-2';
        
        $result = $encryptor->encodeWithXor($originalData, $key1);
        
        // Decoding with wrong key should not return original data
        $decoded = $encryptor->decodeWithXor($result, $key2);
        $this->assertNotEquals($originalData, $decoded);
    }
    
    public function testInvalidBase64Decoding()
    {
        $encryptor = new Base64Encryptor();
        
        $this->expectException(\Exception::class);
        $encryptor->decode('invalid-base64-string!');
    }
    
    public function testInvalidSafeBase64Decoding()
    {
        $encryptor = new Base64Encryptor();
        
        $this->expectException(\Exception::class);
        $encryptor->safeDecode('invalid_safe_base64_string!');
    }
    
    public function testValidBase64Check()
    {
        $originalData = 'Hello World!';
        $validBase64 = base64_encode($originalData);
        $invalidBase64 = 'invalid-base64-string!';
        
        $this->assertTrue(Base64Encryptor::isValidBase64($validBase64));
        $this->assertFalse(Base64Encryptor::isValidBase64($invalidBase64));
    }
    
    public function testSpecialCharacters()
    {
        $encryptor = new Base64Encryptor();
        $originalData = 'Special chars: !@#$%^&*()_+-=[]{}|;:,.<>?';
        
        $encoded = $encryptor->encode($originalData);
        $decoded = $encryptor->decode($encoded);
        
        $this->assertEquals($originalData, $decoded);
    }
    
    public function testUnicodeCharacters()
    {
        $encryptor = new Base64Encryptor();
        $originalData = 'Unicode: 你好世界 🌍';
        
        $encoded = $encryptor->encode($originalData);
        $decoded = $encryptor->decode($encoded);
        
        $this->assertEquals($originalData, $decoded);
    }
}