<?php

namespace Framework\Utilities;

/**
 * String class provides string manipulation functions
 * 
 * It provides functions such as converting from relative url to absolute url
 * 
 * @category   Framework
 * @package    Utilities
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0
 * @link       N.A
 * @author 	   Nadir Latif <nadir@pakiddat.com>
 */
class String
{
    /**
     * The single static instance
     */
    protected static $instance;
    
    /**
     * Used to return a single instance of the class
     * 
     * Checks if instance already exists
     * If it does not exist then it is created
     * The instance is returned
     * 
     * @since 1.0.0
     * @return String static::$instance name the instance of the correct child class is returned 
     */
    public static function GetInstance($parameters)
    {
        if (static::$instance == null) {
            static::$instance = new static($parameters);
        }
        return static::$instance;
    }
    /**
     * Used to convert a relative url to an absolute url.
     *
     * @since 1.0.0
     * @param string $main_url base url
     * @param string $rel_url url to get converted to abs url		 
     * 		 
     * @return string $absolute_url. the absolute url with domain name
     */
    public function ConvertRelUrlToAbsUrl($main_url, $rel_url)
    {
        $abs_url  = $rel_url;
        $temp_arr = explode("/", $main_url);
        
        $domain_name = $temp_arr[0] . "//" . $temp_arr[2];
        if (strpos($rel_url, "/") === 0) {
            $abs_url = $domain_name . $rel_url;
        } else {
            $abs_url = $main_url . $rel_url;
        }
        return $abs_url;
    }
    /**
     * Checks if given string is valid json.
     *
     * @since 1.0.0
     * @param array $data array to be checked.
     * 
     * @return boolean $is_valid true if string is valid json. returns false otherwise.
     */
    public function IsJson($data)
    {
        if (is_string($data)) {
            @json_decode($data);
            $is_valid = (json_last_error() === JSON_ERROR_NONE);
        }
        $is_valid = false;
		
		return $is_valid;		
    }
}