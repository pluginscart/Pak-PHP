<?php

namespace Framework\Utilities;

/**
 * Singleton class
 * Encryption class provides functions related to encryption
 * 
 * It includes functions such as encrypting and decrypting text
 * 
 * @category   Framework
 * @package    Utilities;
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0
 * @link       N.A
 * @author 	   Nadir Latif <nadir@pakiddat.com>
 */
class Encryption
{    
    /**
     * The single static instance
     */
    protected static $instance;
    /**
     * Holds the key used for encrypting and decrypting text
     */
    private $key;    
	
    /**
     * Used to return a single instance of the class
     * 
     * Checks if instance already exists
     * If it does not exist then it is created
     * The instance is returned
     * 
     * @since 1.0.0
     * @return Utilities static::$instance name the instance of the correct child class is returned 
     */
    public static function GetInstance()
    {
        
        if (static::$instance == null) {
            static::$instance = new static();
        }
        return static::$instance;
        
    }
	
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @var      string    $name       The name of the plugin.
     * @var      string    $version    The version of this plugin.
     */
    public function __construct()
    {
        # the key should be random binary, use scrypt, bcrypt or PBKDF2 to
        # convert a string into a key
        # key is specified using hexadecimal
        
        $this->key = pack('H*', "c7ea6ad07a6bb93686bbfb64a592c1c23c6b6e35c17a9ab73ee6b3bc25f4cf08");
        
    }
	
    /**
     * Function used to encrypt given text
     *
     * @since 1.0.0
     * @param string $text the text to encrypt
     */
    public function EncryptText($text)
    {
        
        # create a random IV to use with CBC encoding
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv      = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        
        # creates a cipher text compatible with AES (Rijndael block size = 128)
        # to keep the text confidential 
        # only suitable for encoded input that never ends with value 00h
        # (because of default zero padding)
        $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key, $text, MCRYPT_MODE_CBC, $iv);
        
        # prepend the IV for it to be available for decryption
        $ciphertext = $iv_size . $iv . $ciphertext;
        
        # base64 encode the cipher text
        $ciphertext = base64_encode($ciphertext);
        
        return $ciphertext;
        
    }
	
    /**
     * Function used to decrypt given text
     *
     * @since 1.0.0
     * @param string $ciphertext_base64 the encrypted text
     * @return string $decrypted_string the decrypted text
     */
    public function DecryptText($ciphertext_base64)
    {
        
        $ciphertext_dec = base64_decode($ciphertext_base64);
        
        # retrieves the IV size
        $iv_size = substr($ciphertext_dec, 0, 2);
        
        # retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
        $iv_dec = substr($ciphertext_dec, 2, $iv_size);
        
        # retrieves the cipher text (everything except the $iv_size in the front)
        $ciphertext_dec = substr($ciphertext_dec, $iv_size + 2);
        
        # may remove 00h valued characters from end of plain text
        $plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
        
        # remove null character from end of string and return the string **/			
        $decrypted_string = rtrim($plaintext_dec, "\0");
        
        return $decrypted_string;
        
    }
}