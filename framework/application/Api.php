<?php

namespace Framework\Application;

use \Framework\Configuration\Base as Base;

/**
 * This class implements the Api class 
 * 
 * It provides api related functions
 * The class should be used by application classes that need to provide an api
 * 
 * @category   Framework
 * @package    Application
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    Release: 1.0.0
 */
class Api extends Application
{		
	/**
	 * It makes an api request to the give local module
	 *
	 * It calls the HandleRequest
	 * 
	 * @param string $option the url option
	 * @param string $module_name the name of the module
	 * @param string $response_format [array~json~string~encrypted string~encrypted json] used to indicate how the output of the function should be formatted by the framework
	 * @param array $parameters list of parameters to send to local module
	 * @throws object Exception an exception is thrown if the api response contains an error
	 * 
	 * @return array $response the api response
	 */
	final public function MakeApiRequestToLocalModule($option, $module_name, $response_format, $parameters)
	{
		try {
			/** Used to indicate that the application context should be local api */
			$api_parameters['context']           = 'local api';
			/** The application option is set */
			$api_parameters['option']            = $option;
			/** The module name is set */
			$api_parameters['module_name']       = $module_name;
			/** The response format is set */
			$api_parameters['response_format']   = $response_format;
			/** Used to indicate that the application should not run in test mode */
			$api_parameters['test_mode']         = false;		
			/** The parameters are added to the parameters sub option */
			$api_parameters['parameters']        = $parameters;
		    /** The api response. it is fetched by making call to local module */
		    $response                            = self::HandleRequest("local api", $api_parameters, $api_parameters['module_name']);			
			/** The api response is returned */
			return $response;
		}
		catch(\Exception $e){
		   $this->GetComponent("errorhandler")->ExceptionHandler($e);
		}		
	}
    
	/**
	 * It makes an api request to the give remote module
	 *
	 * It builds the api url from the given parameters
	 * It makes an http request to the api url and fetches the api response
	 * If the response contains an error then an exception is thrown
	 * Otherwise the api response is returned
	 * 
	 * @param string $option the url option
	 * @param string $module_name the name of the module
	 * @param string $response_format [array~json~string~encrypted string~encrypted json] used to indicate how the output of the function should be formatted by the framework
	 * @param array $parameters list of parameters to include with url
	 * @param string $method [GET~POST] the http method to use. if GET is used then the parameters are encoded and included with url	  	
	 * 
	 * @return array $response the api response
	 */
	final public function MakeApiRequestToRemoteModule($option, $module_name, $response_format, $parameters, $method)
	{
		/** If the method is GET */
		if($method == "GET") {
		    /** The url of the api server */
		    $api_url                         = $this->GetConfig("general","api_url");
		    /** The api url with parameters is generated */
		    $api_url                         = $this->GetEncodedUrl($option, $module_name, $response_format, $parameters, false, $api_url);						
		    /** The api response is fetched */
		    $response                        = $this->GetComponent("filesystem")->GetFileContent($api_url);			
		}
		/** If the method is POST */
		else if($method == "POST") {
		    /** The url of the api server */
		    $api_url                         = $this->GetConfig("general","api_url");
			/** The api url with parameters is generated */
		    $api_url                         = $this->GetEncodedUrl($option, $module_name, $response_format, array(), false, $api_url);					
		    /** The api response is fetched */
		    $response                        = $this->GetComponent("filesystem")->GetFileContent($api_url, "POST", $parameters);
		}
		
		/** The api response is returned */
		return $response;
	}

	/**
	 * It saves the api access information to database
	 *
	 * It saves the api access data to database log table
	 * 
	 * @param string $request_type [local~remote] the type of api request
	 * @param string $option request option
	 * @param array $parameters the api parameters
	 * @param string $response_format the api response format
	 * @param array $response the api response
	 */
	final public function LogApiAccess($request_type, $option, $parameters, $response_format, $response)
	{
		/** If the response format is json then the response is json decoded */
		 if ($response_format == "json") {
		 	$response                    = json_decode($response, true);
		 }		
		
		/** The execution time for the request */
		$execution_time                  = $this->GetComponent("profiling")->GetExecutionTime();		
		/** The mysql table name where the api data will be logged */
		$api_table_name                  = $this->GetConfig("general", "mysql_table_names", "api");
		/** The logging information */
		$logging_information             = array("database_object"=>$this->GetComponent("frameworkdatabase"), "table_name"=>$api_table_name);
		/** The api response text */
		$api_response_text               = $this->GetComponent("encryption")->EncodeData($_SERVER);
		/** The api meta data */
		$api_meta_data                   = ($request_type == "local") ? "" : $this->GetComponent("encryption")->EncodeData($_SERVER);
		/** The api parameters */
		$api_parameters                  = $this->GetComponent("encryption")->EncodeData($parameters);		
		/** The api access data that needs to be logged */
		$api_access_data                 = array("request_option" => $option,
		                                         "request_parameters" => $api_parameters,
		                                         "request_type" => $request_type,
		                                         "response_format" => $response_format,
		                                         "response_status" => $response['result'],
		                                         "response_text" => $api_response_text,
		                                         "time_taken" => $execution_time,
		                                         "meta_data" => $api_meta_data,
		                                         "created_on" => time()		                                        
                                           );
						  
		/** The parameters for saving log data */
		$parameters                      = array("logging_information"=>$logging_information,
												 "logging_data"=>$api_access_data,
												 "logging_destination"=>"database",
										   );
													
		/** The test data is saved to database */
		$this->GetComponent("logging")->SaveLogData($parameters);		
	}
	
	/**
	 * It makes an api request to the given module
	 *
	 * It makes an api requests to the local api or remote api
	 * Depending on the request_type parameter
	 * 
	 * @param string [local~remote] $request_type the type of module to call
	 * @param string $option the url option
	 * @param string $module_name the name of the module
	 * @param string $response_format [array~json~string~encrypted string~encrypted json] used to indicate how the output of the function should be formatted by the framework
	 * @param array $parameters list of parameters to include with api request	 
	 * 
	 * @return array $response the api response
	 */
	final public function MakeApiRequest($request_type, $option, $module_name, $response_format, $parameters)
	{
		/** The profiling timer is started */
		$this->GetComponent("profiling")->StartProfiling("execution_time");
		/** The access log information */
		$access_log_information          = array("request_type" => $request_type,
												 "parameters" => $parameters,
												 "response_format" => $response_format,
												 "response" => ''
										   );
		/** The log access information is saved to application configuration so it can be used in other functions */
		$this->SetConfig("general","access_log_information",$access_log_information);		
		/** If the local api module needs to be called */
		if ($request_type == "local") {
			$response = $this->MakeApiRequestToLocalModule($option, $module_name, $response_format, $parameters);
		}
		/** If the remote api module needs to be called */
		else if ($request_type == "remote") {
			$response = $this->MakeApiRequestToRemoteModule($option, $module_name, $response_format, $parameters, "GET");
		}	
		/** If the response format is json, then the output is json decoded */
		if ($response_format == "json") {
			$response                    = json_decode($response, true);
		}
		/** If the result is not set to success in the response then an exception is thrown */
		if(isset($response['result']) && $response['result']!='success') {
			/** The error message is displayed by the error handler */
			$this->GetComponent("errorhandler")->ErrorHandler(
			 $response['data']['error_level'],
			 $response['data']['error_message'],
			 $response['data']['error_file'],
			 $response['data']['error_line'],
			 $response['data']['error_context']
			);			
		}			
		
		/** The api response is returned */
		return $response;
	}

	/**
     * This function is used to call the XML-RPC functions of the given server
     *
     * It calls the XML-RPC function given in the function parameters
	 * It uses the php xmlrpc extension
	 * 
	 * @param array $parameters it is an array with 2 keys:
	 * rpc_function => the name of the RPC function
	 * rpc_function_parameters => the parameters used by rpc function
	 * 
	 * @return string $response the response from the xml rpc server
     */
    final public function MakeXmlRpcCall($parameters)
    {
    	/** The RPC function name */
    	$rpc_function_name       = $parameters['rpc_function'];
		/** The RPC function parameters */
    	$rpc_function_parameters = $parameters['rpc_function_parameters'];
		
		/** The RPC server url */		
		$rpc_server_url          = $this->GetConfig('wordpress','rpc_server_information','server_url');
		
		/** The xml request */
		$xml_request             = xmlrpc_encode_request($rpc_function_name,$rpc_function_parameters);		
    	/** The xml tags are removed. the new lines are also removed */
    	$request                 = str_replace("\n","",str_replace('<?xml version="1.0" encoding="iso-8859-1"?>','',$xml_request));
		/** The url of the WordPress XML-RPC server */
    	$url                     = $rpc_server_url;
        $request_headers[]       = "Content-type: text/xml";
        $request_headers[]       = "Content-length: ".strlen($request);
    	
	    $response                = $this->GetComponent("filesystem")->GetFileContent($url,"POST",$request, $request_headers);
		
		return $response;
    }

    /**
     * Used to authentication the client using api authentication
	 * 
     * This function checks the api key given in application parameters
     * The api key is checked against the valid api key given in application configuration
     */
    final public function ApiAuthentication()
    {        
        /** The application parameters containing api data are fetched **/
        $api_auth                          = $this->GetConfig("api_auth");	        
		/** The application parameters are fetched */
		$application_parameters            = $this->GetConfig("general","parameters");
		/** If the api key is not set in the application parameters then an exception is thrown */
		if (!isset($application_parameters['api_key']))
		    throw new \Exception("API Key was not given in application parameters");
		
        if (!isset($api_auth['credentials']) || (isset($api_auth['credentials']) && $api_auth['credentials'] != $application_parameters['api_key'])) {
            die("Invalid API Key");
        }       
    }
			
	/**
     * Custom error handling function
     * 
     * Used to handle an error
	 * 
     * @param string $log_message the error log message
     * @param array $error_parameters the error parameters. it contains following keys:
     *    error_level => int the error level
	 *    error_type => int [Error~Exception] the error type. it is either Error or Exception
     *    error_message => string the error message
     *    error_file => string the error file name
     *    error_line => int the error line number
     *    error_context => array the error context
	 * 
	 * @return boolean $is_handled indicates if the error was handled
     */
    public function CustomErrorHandler($log_message, $error_parameters)
    {
		/** The error message is displayed to the user */
		$this->DisplayErrorMessage($error_parameters);		
    }	
	    
	/**
     * Used to log the given error message using a web hook
	 * 
     * This function logs the error message to a remote url
	 * 
     * @param array $error_parameters the error parameters. it contains following keys:
     *    error_level => int the error level
	 *    error_type => int [Error~Exception] the error type. it is either Error or Exception
     *    error_message => string the error message
     *    error_file => string the error file name
     *    error_line => int the error line number
     *    error_context => array the error context
     */
    final public function LogErrorToWebHook($error_parameters)
    {
    	/** The line break is fetched from application configuration **/
        $line_break                              = $this->GetConfig("general","line_break");
    	/** The server information is included with the error data */
    	$error_parameters['server_data']         = $_SERVER;		
		/** The database objects for which the query log is fetched */
        $database_object_list                    = array("database","frameworkdatabase");
		/** The mysql query log */
		$mysql_query_log                         = "";
		/** The mysql query log for each database object is fetched and appended to the error log */
		for ($count = 0; $count < count ($database_object_list); $count++) {
		    /** The database object name */
			$database_object_name                = $database_object_list[$count];			
	        /** The database query log **/
	        $query_log                           = $this->GetComponent($database_object_name)->df_display_query_log(true);
			/** The query log is appended to the mysql query log field data */
			$mysql_query_log                     = $mysql_query_log . 
											       "<span class='green-color'><b>MySQL query log for " . ucfirst($database_object_name) . " object: </b>" . 
													$line_break . $line_break . $query_log . "</span>" . $line_break . $line_break;
		}		
		/** The Mysql query log is updated */
		$error_parameters['mysql_query_log']     = $this->GetComponent("encryption")->EncodeData($mysql_query_log);
		/** All the error data is encoded */
    	foreach ($error_parameters as $key => $value) {
    		$error_parameters[$key]              = $this->GetComponent("encryption")->EncodeData($value);
    	}				
		/** The error message is sent to remote api */
        $this->MakeApiRequestToRemoteModule("log_error", "IslamCompanionApi", "json", $error_parameters, "POST");
    }

	/**
     * Used to log the given error message sent using a web hook
	 * 
     * This function logs the error message to sent by a web hook to database
	 * 
     * @param string $log_message the error log message
     * @param array $error_parameters the error parameters. it contains following keys:
     *    error_level => int the error level
	 *    error_type => int [Error~Exception] the error type. it is either Error or Exception
     *    error_message => string the error message
     *    error_file => string the error file name
     *    error_line => int the error line number
     *    error_context => array the error context
     */
    final public function HandleLogError($log_message, $error_parameters)
    {    	
    	/** The server data is fetched from the error parameters */
    	$server_data                  = $error_parameters['server_data'];
		/** The mysql query log data is fetched from the error parameters */
    	$mysql_query_log              = $error_parameters['mysql_query_log'];
		/** The server data is removed from the error parameters */
		unset($error_parameters['server_data']);
		/** The mysql query log is removed from the error parameters */
		unset($error_parameters['mysql_query_log']);
    	/** The error message is logged to database */    	
        $this->LogErrorToDatabase($error_parameters, $server_data, $mysql_query_log);
    }
}