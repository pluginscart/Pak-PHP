<?php

namespace Framework\Application;

use \Framework\Configuration\Base as Base;

/**
 * This class implements the base Application class 
 * 
 * It provides workflow related functions
 * The class should be inherited by the user application class
 * 
 * @category   Framework
 * @package    Application
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    Release: 1.0.0
 * @link       N.A
 */
abstract class Application extends Base
{
	/**
     * Main application function
	 * All requests to the application are handled by this function
	 * It calls the function for handling the current request
     * 
     * It checks the option to function mappings in the application configuration
     * And calls the function that is defined for the requested option
	 * If no option to function mapping is found then a mapping is auto generated
	 * The auto generated mapping function is then called
     * 
     * @since 1.0.0
     * @throws object Exception an exception is thrown if the mapping function was not found
	 * @throws object Exception an exception is thrown if application is in test mode and invalid test type is given in configuration
	 * valid test types are unit and functional
	 * 
	 * @return string $response the application response
     */
    public function Main()
    {
    	/** The application response. it contains the string that will be returned by the application as output */
    	$response                                                                      = "";
        /** If the application is not in test mode then the url request is handled */
        if (!$this->GetConfig('testing','test_mode')) {        	
            /** If the save_test_data option is set to true then the page parameters are saved to test_data folder */
            if ($this->GetConfig('testing','save_test_data')) {
                /** The application test class object is fetched */
                $application_test_class_obj                                            = $this->GetComponent('testing');
                /** The application parameters are saved as test parameters */
                $application_test_class_obj->SaveTestData();
		    }
			/** The application url mappings */
			$application_url_mappings                                                  = $this->GetConfig("general","application_url_mappings");					
			/** The application option */
			$option                                                                    = $this->GetConfig("general","option");					
            /** If a controller is defined for the current url option then it is called */
            if (isset($application_url_mappings[$option]['controller'])) {
                /** The controller function is run */
                $response                                                              = $this->RunControllerFunction($option);
            }
			/** If no controller is defined for the current url option and a template is defined then the template is rendered and then displayed in the browser */
            else if (isset($application_url_mappings[$option]['templates'])) {            	
                /** The application template is rendered with parameters generated by presentation class function */
                $response                                                              = $this->GetComponent("template")->Render("root", array());
            }
			/** If no url mapping is defined for the current url option then a url mapping is auto generated */
            else if (!isset($application_url_mappings[$option]['controller'])) {
				/** The string object is fetched from application configuration */
				$class_function                                                        = $this->GetComponent("string")->Concatenate("Handle",$this->GetComponent("string")->CamelCase($option));
				$application_callback                                                  = array($this->GetComponent("application"),$class_function);				
				/** If the auto generated url mapping is callable then it is called */
				if (is_callable($application_callback)) {
				    /** The auto generated url mapping is saved to application configuration */
					$application_url_mappings[$option]['controller']['object_name']    = "application";
					$application_url_mappings[$option]['controller']['function_name']  = $class_function;
			           
					$this->SetConfig("general", "application_url_mappings", $application_url_mappings);						
					/** The controller function is run */
                	$response                                                          = $this->RunControllerFunction($option);                	                					
				}
				else throw new \Exception("Invalid url request sent to application");
			}
		}
        /** If the application is in test mode then the testing function is called for the current test parameters */
        else {
		    /** The application test class object is fetched */
            $application_test_class_obj                                                = $this->GetComponent("testing");                
            /** If the application needs to be functional tested then all application urls are tested */
            if ($this->GetConfig('testing','test_type') == "functional")
                $response                                                              = $application_test_class_obj->RunFunctionalTests();
            /** If the application needs to be unit tested then only given test class with be tested with test parameters */
            else if ($this->GetConfig('testing','test_type') == "unit")
                $response                                                              = $application_test_class_obj->RunUnitTests();
			/** If the application script needs to be called then the test class given in application configuration will be used */
            else if ($this->GetConfig('testing','test_type') == "script")
                $response                                                              = $application_test_class_obj->CallScript();
            /** If some other test type is given then an exception is thrown */
            else
                throw new \Exception("Invalid test type given in application configuration");
		}
		
		return $response;           
    }    

 	/**
     * Used to echo output
     * 
     * It simply echoes the given string
     * 
     * @since 1.0.0
     * @param string $text the text to echo     		
     */
    final public function DisplayOutput($text)
    {        
        echo $text;  
    }
	
    /**
     * Used to call the url handling function of a controller class
     * 
     * It fetches the controller object information in application configuration
     * It then calls the function of the controller object and returns the response
     * 
     * @since 1.0.0
     * @param string $option the url option
     * 
     * @return array $response		 
     */
    final public function RunControllerFunction($option)
    {        
        /** The application url mappings are fetched from application configuration */
        $application_url_mappings = $this->GetConfig('general','application_url_mappings');               
        $controller_object_name   = $application_url_mappings[$option]['controller']['object_name'];
        $controller_object        = $this->GetComponent($controller_object_name);
        $function_name            = $application_url_mappings[$option]['controller']['function_name'];
        $response                 = $controller_object->$function_name();
        return $response;        
    }    
    /**
     * Used to create an encoded url for the application
     * 
     * It creates a url that includes small parameters that can be passed in the url
     * The parameters are first json encoded and then base64 encoded and then url encoded
     * 
     * @since 1.0.0
     * @param string $option the url option
     * @param array $parameters the list of url parameters. it is an associative array. if set to false then the parameters are not used
     * @return string $encoded_url the encoded url
     * @return boolean $is_link used to indicate if url will be used in link. if it will be used in link then url & will be
     * encoded so it is compatible with html5 validator
     * @throws Exception an object of type Exception is thrown if the encoded parameters size is larger than 100 characters
     */
    final public function GetEncodedUrl($option, $parameters, $is_link)
    {
    	/** If the parameters are set then they are encoded */
    	if($parameters) {
    		 /** The url parameters are first json encoded */
             $encoded_parameters = json_encode($parameters);
             /** Then the parameters are base64 encoded */
             $encoded_parameters = base64_encode($encoded_parameters);
             /** Then the parameters are urlencoded */
             $encoded_parameters = urlencode(urlencode($encoded_parameters));
			                
             /** An exception is thrown if encoded parameter length is larger then 150 characters */
             if (strlen($encoded_parameters) > 250)
                 throw new \Exception("The size of the encoded parameters must be less than 250 characters");
    	}
        
        /** The web application base url is fetched from application configuration */
        $web_application_url = $this->GetConfig("path","framework_url");
		/** The application name is fetched from the application configuration */   
        $module_name         = $this->GetConfig("general","module");
		/** The url parameters */
		$url_parameters      = array("option"=>$option,"module"=>$module_name);
		/** If the parameters were given then they are added to the url */
		if ($parameters)
		    $url_parameters['parameters'] = $encoded_parameters;
		
        /** The encoded url is created and returned */
        /** If the url will be used in an a tag link then the parameters are separate by &amp; */ 
        if ($is_link)
		    $separator = "&amp;";
		else
            $separator = "&";
	
		$encoded_url = $web_application_url . "?";
		foreach ($url_parameters as $key=>$value) {
			$encoded_url .= $key."=".$value.$separator;
		}
             
        $encoded_url = trim($encoded_url, $separator);            
        
        return $encoded_url;        
    }    
    /**
     * Used to decode url parameters
     * 
     * It decodes the url parameters
     * The parameters are first url decoded then base64 decoded and then json decoded
     * The resulting parameters are then stripped of html tags for security purpose
     * 
     * @since 1.0.0
     * @param string $encoded_parameters the list of url parameters	 
     * 		 
     * @return array $decoded_parameters the list of decoded parameters
     */
    final public function GetUrlParameters($encoded_parameters)
    {
        /** The encoded parameters are first url decoded */
        $encoded_parameters = urldecode($encoded_parameters);
        /** The parameters are then base64 decoded */
        $encoded_parameters = base64_decode($encoded_parameters);
        /** The parameters are then json decoded */
        $decoded_parameters = json_decode($encoded_parameters, true);
        
        $temp_decoded_parameters = array();
        
        /** The html tags are stripped from each url parameter */
        if (is_array($decoded_parameters)) {		
            foreach ($decoded_parameters as $key => $value) {
                $temp_decoded_parameters[strip_tags($key)] = $value;
            }
		}
		
        $decoded_parameters = $temp_decoded_parameters;
        
        /** The parameters are then returned */
        return $decoded_parameters;        
    }
	
	/**
	 * It makes an api request to the give local module
	 *
	 * It calls the HandleRequest
	 * 
	 * @since 1.0.0
	 * @param array   $parameters list of parameters to send to local module
	 * @throws object Exception an exception is thrown if the api response contains an error
	 * 
	 * @return array $response the api response
	 */
	final public function MakeApiRequestToLocalModule($parameters){
		try {
		    /** The api response. it is fetched by making call to local module */		
		    $response                        = self::HandleRequest("local api",$parameters);
		    /** If the api request asked for the api response to be encrypted then the api response from server is decrypted and decoded */
		    if ($parameters['encrypt_response']) {		
  		        /** The api response is decrypted */
		        $response                    = $this->GetComponent("encryption")->DecryptText($response);
		    }
		    /** The api response is json decoded */		
		    $response                        = json_decode($response,true);				
		    /** If the server response contains an error then an exception is thrown */
		    if($response['result']!='success')
		        throw new \Exception("Invalid api response was returned. API resonse: ".var_export($response,true).". Parameters: ".var_export($parameters,true));
		}
		catch(\Exception $e){
		   $this->GetComponent("errorhandler")->ExceptionHandler($e);
		}
		/** If the api response text contains json data, then it is decoded */
		if ($this->GetComponent("string")->IsJson($response['text'])) {
			$response                        = json_decode($response['text'],true);
		}
		/** The api response is returned */
		return $response;
	}
    
	/**
	 * It makes an api request to the give remote module
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
	final public function MakeApiRequestToRemoteModule($parameters){		
		/** The api url with parameters is generated */
		$api_url              = $this->GenerateApiUrl($parameters);
		/** The api response is fetched */
		$response             = $this->GetComponent("filesystem")->GetFileContent($api_url);
		/** If the api request asked for the api response to be encrypted then the api response from server is decrypted and decoded */
		if ($parameters['encrypt_response']) {		
		    /** The decrypted response */
		    $response                    = $this->GetComponent("encryption")->DecryptText($response);		
			/** The decrypted and decoded response */		
			$response                    = json_decode($response,true);
		}
		/** If the server response contains an error then an exception is thrown */
		if($response['result']!='success')throw new \Exception("Invalid api response was returned. API resonse: ".var_export($response,true).". Parameters: ".var_export($parameters,true));
		
		/** The api response is returned */
		return $response;
	}
	
	/**
     * Used to handle application request
     * 
     * It is the main entry point for the application
	 * It initializes the application and returns the application response depending on the application context
     * 
     * @since 1.0.0
     * @param string $context the context of the application. e.g local api, remote api, browser or command line	 
	 * @param array $parameters the application parameters
	 * @param string $default_module optional the default module name to use in case no module name is specified by the calling application
	 * @throws object Exception exception is thrown if an invalid context was specified
	 * @throws object Exception exception is thrown if module name was not specified by the application
	 * @throws object Exception exception is thrown if the application context is command line and command line arguments are not in correct format
	 * 
	 * @return string $response the application response
     */
    final public static function HandleRequest($context, $parameters, $default_module="")
    {
    	/** The application module to be called */
		$module                                           = $default_module;
				
        /** If the application is being run from browser */
		if ($context == "browser" || $context == "local api" || $context == "remote api") {
    	    /** The current module name is determined */
    	    $module                                       = isset($parameters['module'])?$parameters['module']:$default_module;
		}
		/** If the application is being run from command line then the module name is determined */
		else if ($context == "command line") {
			/** The updated application parameters in standard key => value format */
			$updated_parameters                           = array();
			/** The application parameters are determined */
			for ($count=1; $count<count($parameters); $count++ ) {
				/** Single command line argument */
				$command                                  = $parameters[$count];
				/** If the command does not contain equal sign then an exception is thrown. only commands of the form --key=value are accepted */
				if (strpos($command, "--")!==0 || strpos($command, "=")===false)
				    throw new \Exception("Invalid command line argument was given");
				else {
					 $command                             = str_replace("--", "", $command);
					 list($key,$value)                    = explode("=", $command);
					 $updated_parameters[$key]            = $value;
				}
			}
			/** The parameters are set */
			$parameters                                   = $updated_parameters;
			/** The application module name is set */
			$module                                       = (isset($parameters['module']))?$parameters['module']:$default_module;
		}
		/** If an invalid application context is given then an exception is thrown */
	    else throw new \Exception("Invalid application context: ".$context);
				
		/** An exception is thrown if the module name cannot be determined */
		if (!$module) throw new \Exception("Module name was not given in url request");
		
		/** The application configuration class name */ 
		$class_name                                       = \Framework\Utilities\UtilitiesFramework::Factory("string")->Concatenate('\\',$module,'\\',"Configuration");
		/** An instance of the required module is created */
		$configuration                                    = new $class_name($parameters);
		/** The application output */
		$response                                         = $configuration->RunApplication();		
		/** If the application type is api then success string is added to response */
		if (isset($parameters['request_type']) && $parameters['request_type'] == "api") {			
		    /** Success string is added to response */
		    $response                                     = array("result"=>"success","message"=>$response);			
		    /** The response is json encoded */			
		    $response                                     = json_encode($response);
		}
		/** If the application response is an array, then it is json encoded */
        else if (is_array($response)) {
	   	    /** The response is json encoded */			
		    $response                                     = json_encode($response);
        }
		/** If the parameters requested encrpytion of response then the response is encrypted */
		if (isset($parameters['encrypt_response']) && $parameters['encrypt_response']) 
		    $response                                     = \Framework\Utilities\UtilitiesFramework::Factory("encryption")->EncryptText($response);		       
		
		/** The output is returned */
		return $response;
    }

    /**
     * Used to authentication the client using api authentication
	 * 
     * This function checks the api key given in application parameters
     * The api key is checked against the valid api key given in application configuration
     * 
     * @since 1.0.0		 	
     */
    public function ApiAuthentication()
    {        
        /** The application parameters containing api data are fetched **/
        $api_auth                          = $this->GetConfig("general","api_auth");		        
		/** The application parameters are fetched */
		$application_parameters            = $this->GetConfig("general","parameters");
		/** If the api key is not set in the application parameters then an exception is thrown */
		if (!isset($application_parameters['parameters']['api_key']))
		    throw new \Exception("API Key was not given in application parameters");
		
        if (!isset($api_auth['credentials']) || (isset($api_auth['credentials']) && $api_auth['credentials'] != $application_parameters['parameters']['api_key'])) {
            die("Invalid API Key");
        }       
    }
	
    /**
     * Used to redirect the user to the wulfmansworld.com backend login page
     * 
     * This function is called if user is not logged in to the backend
     * It redirects the user to the backend page
     * 
     * @since 1.0.0		 	
     */
    public function SessionAuthentication()
    {        
        /** The application parameters containing session data are fetched **/
        $session                = $this->GetConfig("general","session");		
        $session_authentication = $this->GetConfig("session_auth");
		/** The current module name */
		$module_name            = $this->GetConfig("general","module");
        /** The login url */
        $login_url              = $this->GetConfig("path","wulfmansworld_backend_login_url");
		/** The current module name is added to the login url */
		$login_url              = str_replace("{module}", $module_name, $login_url);
        if (!isset($session[$session_authentication['credentials']['key']]) || (isset($session[$session_authentication['credentials']['key']]) && $session[$session_authentication['credentials']['key']] != $session_authentication['credentials']['value'])) {
            die("<script>parent.location.href='" . $login_url."'</script>");            
        }       
    }
    
    /**
     * Used to authenticate the user using http authentication
     * 
     * It displays an error to the user if http authentication fails		 
     * 
     * @since 1.0.0		 	
     */
    public function HttpAuthentication()
    {        
        /** The http authentication parameters are fetched **/
        $http_authentication = $this->GetConfig('http_auth');
        /** The http authentication method is called **/
        $is_valid_user       = \Framework\Utilities\UtilitiesFramework::Factory("authentication")->AuthenticateUser($http_authentication['credentials'], $http_authentication['realm']);
        /** If the authentication method returns an error then an error is shown to the user in browser and script execution ends **/
        if (!$is_valid_user)
            die("Please enter a valid user name and password");       
    }
    
    /**
     * Custom error handling function
     * 
     * Used to handle an error
     * 
     * @since 1.0.0
     * @param string $log_message the error log message
     * @param array $error_parameters the error parameters. it contains following keys:
     * error_level=> the error level
     * error_message=> the error message
     * error_file=> the error file name
     * error_line=> the error line number
     * error_context=> the error context
     */
    public function CustomErrorHandler($log_message, $error_parameters)
    {
        /** The line break is fetched from application configuration **/
        $line_break = $this->GetConfig("general","line_break");
        /** The error message is displayed to the browser **/
        echo $log_message . $line_break . $line_break;
        echo "MySQL query log: " . $line_break . $line_break;
        /** The database query log is displayed **/
        $this->GetComponent("database")->df_display_query_log();       
    }
    
    /**
     * The default application shutdown function
     * 
     * Closes the database connections created by the application
     * This function is registered as a shutdown function with the ErrorHandling class    
     * 
     * @since 1.0.0 		
     */
    public function CustomShutdownFunction()
    {        
        /** The database connection is closed **/
        $this->GetComponent("database")->df_close();       
    }
}