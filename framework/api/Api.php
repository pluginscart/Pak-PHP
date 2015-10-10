<?php

namespace Framework\Api;

use Framework\WebApplication\Configuration as Configuration;

/**
 * This class implements the api class
 * 
 * It contains functions that help in constructing client and server apis
 * 
 * @category   Framework
 * @package    Api
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    Release: 1.0.0
 * @link       N.A
 */
class Api
{
	/**
	 * The api url. Used to make requests to the api server
	 */
	private $api_url;
	/**
     * The single static instance
     */
    protected static $instance;
    /**
     * Class constructor
     * Used to prevent creating an object of this class outside of the class using new operator
     * 
     * Used to implement Singleton class
     * 
     * @since 1.0.0
	 * @param array $parameters parameters for the api class
     */
    protected function __construct($parameters)
    {
    	/** If the api url is given as parameter then it is set */
        if(isset($parameters['api_url']))$this->api_url = $parameters['api_url'];
    }
    /**
     * Used to return a single instance of the class
     * 
     * Checks if instance already exists
     * If it does not exist then it is created
     * The instance is returned
     * 
     * @since 1.0.0
     * @param array $parameters parameters for the api class
	 * 
     * @return Api static::$instance name the instance of the correct child class is returned 
     */
    public static function GetInstance($parameters)
    {
        if (static::$instance == null) {
            static::$instance = new static($parameters);
        }
        return static::$instance;
    }
	/**
	 * The url with parameters is generated
	 *
	 * It generates the url containing parameters from the given parameter information
	 * 
	 * @since 1.0.0
	 * @param array $parameters the parameters used to generate the url
	 * 
	 * @return string $api_url the api url containing the parameters is returned	 
	 */
	public function GenerateApiUrl($parameters){
		/** The url of the api in parts */
		$api_url              = array();
		/** 
		 * The api url containing the parameters is created
		 * The current timestamp is added so the api server can validate the api call
		 */
		$parameters['timestamp'] = time();			
		/** Each api parameter is added to the url */
		foreach($parameters as $key=>$value)
			{
				$api_url[]=(($key)."=".urlencode(base64_encode($value)));
			}			
		/** The trailing & is removed */
		$api_url             = $this->api_url."?".implode("&",$api_url);		
		/** The api url is returned */
		return $api_url;
	}
	
	/**
	 * It builds the api url from the given parameters and makes the api request
	 *
	 * It builds the api url from the given parameters
	 * It makes an http request to the api url and fetches the api response
	 * If the response contains an error then an exception is thrown
	 * Otherwise the api response is returned	 
	 * 
	 * @since 1.0.0
	 * @param array   $parameters list of parameters to include with url
	 * @throws object Exception an exception is thrown if the api response contains an error
	 * 
	 * @return array $response the api response
	 */
	public function MakeAPIRequestFromParams($parameters){		
		/** The api url with parameters is generated */
		$api_url              = $this->GenerateApiUrl($parameters);
		/** The api response is fetched */
		$response             = $this->MakeAPIRequestFromUrl($api_url);
		/** If the server response contains an error then an exception is thrown */
		if($response['result']!='success')throw new \Exception("Invalid api response was returned. API url: ".$api_url.". Details: ".$response['text']);
		/** The api response is returned */
		
		return $response;
	}
	
	/**
	 * It makes an api request using the given url
	 *
	 * It makes the api request
	 * If encryption is enabled then the api response is decrypted
	 * The api response is json decoded
	 * 
	 * @since    1.0.0
	 * @param string $url the api url with parameters
	 * @throws object Exception an exception is thrown if the api response contains an error
	 * 
	 * @return array $response the api response
	 */
	public function MakeAPIRequestFromUrl($url){
		/** The api response is fetched */
		$response             = Configuration::GetComponent("filesystem")->GetFileContent($url);
		/** The server response is decrypted */
		$response             = Configuration::GetComponent("encryption")->DecryptText($response);		
		/** The server response is first base64 decoded and then json decoded */		
		$response             = json_decode($response,true);
		/** If the server response contains an error then an exception is thrown */
		if($response['result']!='success')throw new \Exception("Invalid api response was returned. API url: ".$url.". Details: ".$response['text']);
		/** The api response is returned */
		
		return $response;
	}
}