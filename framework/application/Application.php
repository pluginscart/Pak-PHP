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
     * @throws object Exception an exception is thrown if the mapping function was not found
	 * @throws object Exception an exception is thrown if application is in test mode and invalid test type is given in configuration
	 * valid test types are unit and functional
	 * 
	 * @return array $function_output the application response or empty if the application is being tested
     */
    public function Main()
    {
    	/** The application response. it contains the string that will be returned by the application as output */
    	$response                                                                      = "";
		/** The application url mappings */
		$application_url_mappings                                                      = $this->GetConfig("general","application_url_mappings");					
		/** The application option */
		$option                                                                        = $this->GetConfig("general","option");
		/** The application function output */
		$function_output                                                               = array("result" => "", "data" => "");		
        /** If the application is not in test mode then the url request is handled */
        if (!$this->GetConfig('testing','test_mode')) {
            /** The application function output */
            $function_output                                                           = $this->RunApplicationFunction($option);			
			/** If the save_test_data option is set to true then the page parameters are saved to test_data folder */
    	    if ($this->GetConfig('testing','save_test_data'))$this->SaveTestData($function_output['function_type']);
		}
        /** If the application is in test mode then the testing function is called for the current test parameters */
        else {
		    /** The application test class object is fetched */
            $application_test_class_obj                                                = $this->GetComponent("testing");                
            /** If the application needs to be functional tested then all application urls are tested */
            if ($this->GetConfig('testing','test_type') == "functional")
                $application_test_class_obj->RunFunctionalTests();
            /** If the application needs to be unit tested then only given test class with be tested with test parameters */
            else if ($this->GetConfig('testing','test_type') == "unit")
                $application_test_class_obj->RunUnitTests();
			/** If the application script needs to be called then the test class given in application configuration will be used */
            else if ($this->GetConfig('testing','test_type') == "script")
                $application_test_class_obj->CallScript();
            /** If some other test type is given then an exception is thrown */
            else
                throw new \Exception("Invalid test type given in application configuration");
		}
		
		return $function_output['data'];           
    }

	/**
     * Used to run the function for the given option
     * 
     * It calls a controller function or a template function for the given option
	 * If no controller function or template function is defined in the application configuration
	 * Then a controller function name is auto generated from the application option and the corresponding function is called	 
     *      
	 * @param string $option the application option
	 * 
	 * @throws Exception an object of type Exception is thrown if no function was found for the given option
	 * 
	 * @return array $function_output the application function output and the function type
	 *    data => mixed the application function response
	 *    function_type => the function type
     */
    final public function RunApplicationFunction($option)
    {
    	/** The application url mappings */
		$application_url_mappings                                                  = $this->GetConfig("general","application_url_mappings");					
    	/** The type of function that is called. e.g template, controller or not defined */
		$function_type                                                             = "";	
    	/** The response of the application function */
    	$response                                                                  = "";
    	/** If a controller is defined for the current url option then it is called */
        if (isset($application_url_mappings[$option]['controller'])) {
            /** The controller function is run */
            $response                                                              = $this->RunControllerFunction($option);
			/** The function type is set to controller */
			$function_type                                                         = "controller";
        }
		/** If no controller is defined for the current url option and a template is defined then the template is rendered and then displayed in the browser */
        else if (isset($application_url_mappings[$option]['templates'])) {            	
            /** The application template is rendered with parameters generated by presentation class function */
            $response                                                              = $this->GetComponent("template")->Render("root", array());
			/** The function type is set to controller */
			$function_type                                                         = "template";
        }
		/** If no url mapping is defined for the current url option then a url mapping is auto generated */
        else  {
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
				/** The function type is set to controller */
				$function_type                                                     = "not defined";                	                					
		    }
		    else throw new \Exception("Invalid url request sent to application");
	    }

		/** The function output */
		$function_output                                                           = array("data" => $response, "function_type" => $function_type);
		
        return $function_output;
    }

	/**
     * Used to save the test data
     * 
     * It saves the current application parameters as test data
     * 
	 * @param string $function_type [template~controller~not defined] the function type
     */
    final public function SaveTestData($function_type)
    {
    	/** The application url mappings */
		$application_url_mappings                                              = $this->GetConfig("general","application_url_mappings");					
		/** The application option */
		$option                                                                = $this->GetConfig("general","option");        
        /** If a template is defined for the option */
        if ($function_type == "template") {
            /** The name of the object that has the function */
			$object_name                                                       = $application_url_mappings[$option]['templates'][0]['object_name'];
			/** The name of the function used to handle the url request */
			$function_name                                                     = $application_url_mappings[$option]['templates'][0]['function_name'];
		}
		/** If a controller is defined for the option or no template or controller is defined */
		else {                        
			/** The name of the object that has the function */
			$object_name                                                       = $application_url_mappings[$option]['controller']['object_name'];
			/** The name of the function used to handle the url request */
			$function_name                                                     = $application_url_mappings[$option]['controller']['function_name'];            
		}
		
		/** The application parameters are saved as test parameters */
        $this->GetComponent('testing')->SaveTestData($object_name, $function_name, $function_type);
    }	
	
 	/**
     * Used to echo output
     * 
     * It simply echoes the given string
     * 
     * @param string $text the text to echo     		
     */
    final public function DisplayOutput($text)
    {        
        echo $text;  
    }	
		
	/**
     * Used to display the error message
	 * 
     * This function displays the error message to the user
	 * It stops script execution
     * 
	 * @param array $error_parameters the error parameters. it contains following keys:
     *    error_level => int the error level
	 *    error_type => int [Error~Exception] the error type. it is either Error or Exception
     *    error_message => string the error message
     *    error_file => string the error file name
     *    error_line => int the error line number
     *    error_context => array the error context
     */
    final public function DisplayErrorMessage($error_parameters)
    {
  	     /** The response format for the application request */
	    $response_format                         = 'json';		
	    /** The response is converted to an array. Error message is added to response */
		$response                                = array("result"=>"error","data"=>$error_parameters);				
		/** The response is json encoded */
		$response                                = json_encode($response);
		/** The error response from api is displayed */
		die($response);
    }
	
    /**
     * Used to call the url handling function of a controller class
     * 
     * It fetches the controller object information in application configuration
     * It then calls the function of the controller object and returns the response
     *      
     * @param string $option the url option
     * 
     * @return array $response		 
     */
    final public function RunControllerFunction($option)
    {
    	/** The application parameters */
		$parameters               = $this->GetConfig("general","parameters");        
        /** The application url mappings are fetched from application configuration */
        $application_url_mappings = $this->GetConfig('general','application_url_mappings');
        /** The name of the controller object */    
        $controller_object_name   = $application_url_mappings[$option]['controller']['object_name'];
		/** The controller object is fetched */
        $controller_object        = $this->GetComponent($controller_object_name);
		/** The controller function name */
        $function_name            = $application_url_mappings[$option]['controller']['function_name'];
		/** The application request is pre processed */
		$this->PreProcessRequest($option, $controller_object, $function_name, $parameters);		
		/** The controller function is called */
		$response                 = $controller_object->$function_name($parameters);
        	    
		/** The processed response. The application request is post processed */
		$processed_response       = $this->PostProcessRequest($option, $controller_object, $function_name, $response, $parameters);
		/** The formatted response */
		$response                 = $processed_response['formatted_output'];
		
        return $response;        
    }    
    /**
     * Used to create an encoded url for the application
     * 
     * It creates a url that includes small parameters that can be passed in the url
     * The parameters are first json encoded and then base64 encoded and then url encoded
     * 
     * @param string $option the url option
	 * @param string $module_name the name of the module
     * @param array $parameters the list of url parameters. it is an associative array. if set to false then the parameters are not used
	 * @param boolean $is_link used to indicate if url will be used in link. if it will be used in link then url & will be
     * encoded so it is compatible with html5 validator
     * @param string optional $url the server url. if omitted then the framework url is used	 
	 * 
     * @return string $encoded_url the encoded url
     
     * @throws Exception an object of type Exception is thrown if the encoded parameters size is larger than 350 characters
     */
    final public function GetEncodedUrl($option, $module_name, $response_format, $parameters, $is_link, $url="")
    {
    	/** If the parameters are set then they are encoded */
    	if($parameters) {
    		 /** The url parameters are first json encoded */
             $encoded_parameters          = json_encode($parameters);
             /** Then the parameters are base64 encoded */
             $encoded_parameters          = base64_encode($encoded_parameters);
             /** Then the parameters are urlencoded */
             $encoded_parameters          = urlencode(urlencode($encoded_parameters));			               
             /** An exception is thrown if encoded parameter length is larger then 350 characters */
             if (strlen($encoded_parameters) > 350)
                 throw new \Exception("The size of the encoded parameters must be less than 350 characters");
    	}
        
        /** The web application base url. If not given as a parameter then it is fetched from application configuration */
        $web_application_url              = ($url != "") ? $url : $this->GetConfig("path","framework_url");
		/** The url parameters */
		$url_parameters                   = array("option"=>$option, "module"=>$module_name, "response_format"=>$response_format);
		/** If the parameters were given then they are added to the url */
		if ($parameters)
		    $url_parameters['parameters'] = $encoded_parameters;
		
        /** The encoded url is created and returned */
        /** If the url will be used in an a tag link then the parameters are separate by &amp; */ 
        if ($is_link)
		    $separator                    = "&amp;";
		else
            $separator                    = "&";
	
		$encoded_url                      = $web_application_url . "?";
		foreach ($url_parameters as $key=>$value) {
			$encoded_url .= ($key."=".$value.$separator);
		}
             
        $encoded_url                      = trim($encoded_url, $separator);            
        
        return $encoded_url;        
    }    
    /**
     * Used to decode url parameters
     * 
     * It decodes the url parameters
     * The parameters are first url decoded then base64 decoded and then json decoded
     * The resulting parameters are then stripped of html tags for security purpose
     * 
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
     * Used to handle application request
     * 
     * It is the main entry point for the application
	 * It initializes the application and returns the application response depending on the application context
     * 
     * @param string $context the context of the application. e.g local api, remote api, browser or command line	 
	 * @param array $parameters the application parameters
	 * @param string $default_module optional the default module name to use in case no module name is specified by the calling application
	 * 
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
			/** The application context is added to the application parameters */
		    $parameters['context']                        = $context;			
		}
		/** If the application is being run from command line then the module name is determined */
		else if ($context == "command line") {
			/** The updated application parameters in standard key => value format */
			$updated_parameters                           = array();
			/** The application context is added to the application parameters */
		    $updated_parameters['context']                = $context;
			/** The application parameters are determined */
			for ($count=1; $count<count($parameters); $count++ ) {
				/** Single command line argument */
				$command                                  = $parameters[$count];
				/** If the command does not contain equal sign then an exception is thrown. only commands of the form --key=value are accepted */
				if (strpos($command, "--")!==0 || strpos($command, "=")===false)
				    throw new \Exception("Invalid command line argument was given. Command line arguments: ".var_export($parameters,true));
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

		/** The output is returned */
		return $response;
    }

    /**
     * Used to redirect the user to the wulfmansworld.com backend login page
     * 
     * This function is called if user is not logged in to the backend
     * It redirects the user to the backend page
     *      		 
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
        $line_break                   = $this->GetConfig("general","line_break");
		/** Lines breaks are added to the log message */
		$log_message                  = $log_message . $line_break . $line_break;
		/** The mysql query log */		
        $mysql_query_log              = $this->GetComponent("database")->df_display_query_log(true);
		/** The mysql query log is appended to the log message */
		$log_message                  = $log_message . "MySQL query log: " . $line_break . $line_break;       
        /** The error message is displayed to the browser */
       $this->DisplayOutput($log_message);       
    }
    
    /**
     * The default application shutdown function
     * 
     * Closes the database connections created by the application
     * This function is registered as a shutdown function with the ErrorHandling class    
     * 
     */
    public function CustomShutdownFunction()
    {        
        /** The database connection is closed **/
        $this->GetComponent("database")->df_close();       
    }
	
	/**
     * Used to pre process the application request
     * 
     * This function is called before an application request is processed
	 * It can be used to validate the request parameters
	 * This method should be overridden by child classes
     * By default the function does nothing
	 *      
	 * @param string $option the application option
	 * @param object $controller_object the object that contains the api function
	 * @param string $function_name the api function name
	 * @param array $parameters the parameters for the callback function	 
     */
    protected function PreProcessRequest($option, $controller_object, $function_name, $parameters)
    {
    	/** The current context of the application */
    	$context                             = $this->GetConfig("general","parameters","context");
    	/** The custom validation callback */
    	$custom_validation_callback          = array($this, "ValidateFunctionParameter");
    	/** The reflection object is fetched */
    	$reflection                          = $this->GetComponent("reflection");
		/**
		 * The result of validating the parameters
		 * Method parameters are validated against the information in the Doc Block comments
		 */		
		$validation_result                   = $reflection->ValidateMethodParametersAndContext($controller_object, $function_name, $context, $parameters, $custom_validation_callback);
		
		return $validation_result;
    }
	
	/**
     * Used to post process the application request
     * 
     * This function is called after an application request has been handled
	 * It formats the output of the request handling function according to the information given in the Doc Block comments
	 * It also validates the output of the function against the return type given in the Doc Block comments
	 * It also saves the details of function execution to database
	 * For example execution time, ip address and function parameters	     
	 * 
	 * @param string $option the application option
	 * @param object $controller_object the object that contains the api function
	 * @param string $function_name the api function name
	 * @param mixed $response the api function response
	 * @param array $parameters the value of all the method parameters
	 * @param array $all_parsed_parameters details of all the method parameters
	 * 
	 * @return array $processed_response an array containing the formatted output and validation results
	 * formatted_output => string formatted output
	 * validation_result => array the result of validating the function
	 *                      is_valid => boolean indicates if the parameters are valid
	 *                      validation_message => string the validation message if the parameters could not be validated
     */
    protected function PostProcessRequest($option, $controller_object, $function_name, $response, $parameters)
    {
    	/** The processed response */
    	$processed_response                      = array();        
        /** The custom validation callback */
    	$custom_validation_callback              = array($this, "ValidateFunctionParameter");
    	/** The reflection object is fetched */
    	$reflection                              = $this->GetComponent("reflection");
		/** The result of validating the return value. The return value is validated against the information in the Doc Block comments */
		$validation_result                       = $reflection->ValidateMethodReturnValue($controller_object, $function_name, $response, $custom_validation_callback, $parameters);
        /** The validation result */		
		$processed_response['validation_result'] = $validation_result;	
		/** If the response format is set in the function parameters then the output is formatted according to the response format parameter */
		if (isset($parameters['response_format'])) {
			/** If the required response format is an array */
		    if ($parameters['response_format'] == "array") {
		    	/** The response is converted to an array. Success string is added to response */
		        $response                        = array("result"=>"success","data"=>$response);
			}
			/** If the required response format is json or encrypted json */
			else if ($parameters['response_format'] == "json" || $parameters['response_format'] == "encrypted json") {
				/** The response is converted to an array. Success string is added to response */
		        $response                        = array("result"=>"success","data"=>$response);				
		        /** The response is json encoded */
		        $response                        = json_encode($response);
				/** If the response format is encrypted json */
				if ($parameters['response_format'] == "encrypted json") {
					/** The json string is encrypted */
					$response                    = $this->GetComponent("encryption")->EncryptText($response);
				}
			}						
		}
	
		$processed_response['formatted_output']  = $response;
			
		return $processed_response;
    }

	/**
     * Used to validate certain function parameters
     * 
     * It checks if the given function parameter is valid
     * 	      
	 * @internal
	 * @param string $parameter_name the name of the parameter
	 * @param string $parameter_value the value of the parameter
	 * @param array $all_parameter_values the value of all the method parameters
	 * @param array $all_parsed_parameters details of all the method parameters
	 * @param array $all_return_values the return value of the function
	 * @param array $all_return_parameters details of all the return value parameters
	 * @param bool $is_return if set to true then the return value needs to be validated
	 * 
	 * @return array $validation_result the result of validating the method parameters
	 *    is_valid => boolean indicates if the parameters are valid
	 *    validation_message => string the validation message if the parameters could not be validated
     */
    public function ValidateFunctionParameter($parameter_name, $parameter_value, $all_parameter_values, $all_parsed_parameters, $all_return_values, $all_return_parameters, $is_return)
	{
		/** The result of validating the parameter */
    	$validation_result    = array("is_valid"=>true,"validation_message"=>"");
		
		return $validation_result;
	}
		
    /**
     * Used to save the error message to database
	 * 
     * This function formats the error message and saves it to database
     *      
     * @param array $error_parameters the error parameters. it contains following keys:
     *    error_level => int the error level
	 *    error_type => int [Error~Exception] the error type. it is either Error or Exception
     *    error_message => string the error message
     *    error_file => string the error file name
     *    error_line => int the error line number
     *    error_context => array the error context
	 * @param array $server_data the information about the server that sent the error data	 
     */
    final private function LogErrorToDatabase($error_parameters, $server_data, $mysql_query_log)
    {        
        /** The line break is fetched from application configuration **/
        $line_break                                  = $this->GetConfig("general","line_break");
		/** The error context is encoded */
		$error_parameters['error_context']           = $this->GetComponent("encryption")->Encode($error_parameters['error_context']);
		/** The server data is added to the error data */
		$error_parameters['server_data']             = $server_data;
		/** The mysql query log is added to the error data */
		$error_parameters['mysql_query_log']         = $mysql_query_log;
		
		/** The timestamp is added to the error message */
		$error_parameters['created_on']              = time();
		/** The mysql table name where the error data will be logged */
		$error_table_name                            = $this->GetConfig("general", "mysql_table_names", "error");
		/** The logging information */
		$logging_information                         = array("database_object"=>$this->GetComponent("frameworkdatabase"), "table_name"=>$error_table_name);
		/** The parameters for saving log data */
		$parameters                                  = array("logging_information"=>$logging_information,
														 "logging_data"=>$error_parameters,
														 "logging_destination"=>"database"
													    );											
		/** The error data is saved to database */
		$this->GetComponent("logging")->SaveLogData($parameters);
    }
}