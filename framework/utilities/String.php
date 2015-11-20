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
 * @version    1.0.2
 * @link       N.A
 * @author 	   Nadir Latif <nadir@pakiddat.com>
 */
final class String
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
    	$is_valid = false;
		
        if (is_string($data)) {
            @json_decode($data);
            $is_valid = (json_last_error() === JSON_ERROR_NONE);
        }        
		
		return $is_valid;		
    }
	/**
     * Used to convert a string to camel case
     *
     * @since 1.0.1
     * @param string $string text to be converted to camel case
     * e.g part1_part2
	 * 
     * @return string $camelcase_text camel case string
     */
    public function CamelCase($string)
    {
    	$string = str_replace("_", " ", $string);
		$string = ucwords($string);
		$camelcase_text = str_replace(" ", "", $string); 
    	
		return $camelcase_text;	
    }
	/**
     * Used to concatenate the given strings
	 * 
	 * The function supports variable number of arguments
     *
     * @since 1.0.1
     * @param string $string text to be concatenated
	 * @param string $string text to be concatenated
	 * 
     * @return string $concatenated_text the concatenated string
     */
    public function Concatenate()
    {
    	$concatenated_text = "";
		
    	for ($count=0; $count<func_num_args(); $count++) {
            $text               = func_get_arg($count);
			$concatenated_text .= $text;
    	}
    	return $concatenated_text;
    }
}