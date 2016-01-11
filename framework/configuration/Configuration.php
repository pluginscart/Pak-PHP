<?php

namespace Framework\Configuration;

use \Framework\Configuration\Base as Base;

/**
 * Base configuration class for browser based applications
 *  
 * Abstract class. must be inherited by a child class
 * It uses the DefaultApplicationConfiguration class
 * The DefaultApplicationConfiguration class contains default configuration values
 * Initializes objects and sets configuration
 * 
 * @category   Framework
 * @package    Configuration
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0 
 */
abstract class Configuration extends Base
{
	/** The user defined configuration */
	protected $user_configuration;
    /** Specifies the configuration information required by the application */
    protected $configuration;
    /** List of objects that can be used by the application */
    protected $component_list;
	
    /**
     * Used to set the user configuration
     * 
     * Defines the user configuration
	 * Updated the user configuration using the user defined application parameters	
     * Sets the user defined configuration as object property
	 * 
	 * @param array $parameters the application parameters given by the user	 	
     */
    public function __construct($parameters){
    	/** The user defined application configuration */
    	$this->user_configuration                                    = array();
		/** The parameters given by the user are set in the application configuration */
    	$this->user_configuration['general']['parameters']           = $parameters;
		/** The default application name is set in the application configuration */
		$this->user_configuration['general']['application_name']     = "Default";
		/** The default application option is set */
		$this->user_configuration['general']['default_option']       = "index"; 	
	}
    
    /**
     * Used to create the given object
     * 
     * Creates the given object
	 * The object must be mentioned by the user in the application configuration file 		
     * The object is created using GetInstance method if it is supported or new operator if class is not Singleton
	 * 
	 * @param array $parameters the optional object parameters
	 * If not set then then the object parameters specified in the application configuration are used
	 * 
	 * @return object $framework_class_obj the initialized object is returned	 
     */
    final protected function InitializeObject($object_name,$parameters=false)
    {
        $object_information = $this->GetConfig('required_frameworks',$object_name);		
        /** The class parameters are initialized */
        if ($parameters)
			$object_information['parameters'] = $parameters;
		else if (!isset($object_information['parameters']))
            $object_information['parameters'] = "";
       
        /** The name of the framework class */
        $framework_class_name = $object_information['class_name'];
        /** 
         * Used to check if class exists
         * The class is autoloaded if it is not already included 
         * If it does not exist then an exception is thrown
         */
       if (!class_exists($framework_class_name, true))
           throw new \Exception("Class: " . $framework_class_name . " does not exist for object name: ".$object_name);
       /**
        * Used to check if class implments Singleton pattern
        * If it has a  function called GetInstance then
        * It is assumed to be a Singleton class
        * The GetInstance method is used to get class instance
        */
       $callable_singleton_method = array(
           $framework_class_name,
           "GetInstance"
       );
	 
       if (is_callable($callable_singleton_method))
           $framework_class_obj = call_user_func_array($callable_singleton_method, array($object_information['parameters']));
       /** If it is not a Singleton class then an object of the class is created using new operator */
	   else
           $framework_class_obj = new $framework_class_name($object_information['parameters']);
	   /** The callable that allows SetConfigurationObject function to be called */
       $callable_method         = array($framework_class_obj,"SetConfigurationObject");
	   /** Used to check if class implements SetConfigurationObject function */
       if (is_callable($callable_method)) {           
           /** The configuration object is set for each object */
	       $framework_class_obj->SetConfigurationObject($this);
	   }
       /** The object is saved to object list */
       $this->component_list[$object_name] = $framework_class_obj;
	   
	   return $framework_class_obj;       
    }
    
    /**
     * Used to include required files
     * 
     * It gets list of all files that need to be included
     * Including the files given in test parameters and url handling parameters  
     */
    final protected function IncludeRequiredClasses()
    {
        /** Test mode status is returned */
        $test_mode = $this->GetConfig("testing","test_mode");
        /** The list of files to be included for testing is fetched from configuration */
        if ($test_mode)
            $include_files                               = $this->GetConfig('testing','include_files');
		/** The list of files to be included for application requests is fetched from configuration */
        else
            $include_files                               = $this->GetConfig('path','include_files');
		
        /** The application url mappings */
        $application_url_mappings                        = $this->GetConfig("general","application_url_mappings");
		/** The current application option */
		$option                                          = $this->GetConfig("general","option");
		/** 
		 * The files to be included for the current application request are merged with the files to include for testing
		 * Or they are merged with the files to include for all application requests
		 */		
        if (isset($application_url_mappings[$option]) && isset($application_url_mappings[$option]['include_files']))
            $include_files                               = array_merge_recursive($include_files,$application_url_mappings[$option]['include_files']);

        /** All files that need to be included are included */
        foreach ($include_files as $include_type => $include_files) {       
            for ($count = 0; $count < count($include_files); $count++) {
                $file_name = $include_files[$count];
			    /** If the include type is equal to vendors then the vendor folder path is prepended to the include file path */
			    if ($include_type == "vendors")
			        $file_name = $this->GetConfig('path','vendor_folder_path') . DIRECTORY_SEPARATOR . $file_name;
				/** If the include type is equal to pear then the pear folder path is prepended to the include file path */
			    if ($include_type == "pear")
			        $file_name = $this->GetConfig('path','pear_folder_path') . DIRECTORY_SEPARATOR . $file_name;
				
                if (is_file($file_name))
                    require_once($file_name);
                else
                    throw new \Exception("Invalid include file name: " . $file_name . " given for page option: " . $this->GetConfig("general","option"));
		    }
		}
    }
    
    /**
     * Used to initialize the application
     * 
     * Initializes objects needed by the application
     * Sets application configuration	  
     */
    protected function Initialize()
    {
    	/** The configuration object for the current object is set */
    	$this->SetConfigurationObject($this);        
        /** The default configuration settings */
        $default_configuration = new DefaultConfiguration();
		/** The default configuration is merged with user configuration and the result is returned */
        $this->configuration   = $default_configuration->GetUpdatedConfiguration($this->user_configuration);
        /** Php Sessions are enabled if user requested sessions */
        $this->EnableSessions();
		/** The encoded url parameter called parameters is decoded */
		$this->GetUrlParameters();
		/** The application authentication and error handling is enabled */		
        $this->EnableAuthenticationAndErrorHandling();
        /** All required classes are included */
        $this->IncludeRequiredClasses();	
    }
    
	/**
     * Used to enable authentication and error handling
     * 		 
     * This function checks for user defined callbacks
	 * It replaces callbacks with objects
	 * If the user has not defined callbacks then the default application callbacks are used
     */
    final protected function EnableAuthenticationAndErrorHandling()
    {
    	/** If the user configuration includes error handler */
    	if (isset($this->user_configuration['required_frameworks']['errorhandler'])) {    		       
            /** The errorhandler callback is checked */
            $errorhandler_callback                                                                                = $this->configuration['required_frameworks']['errorhandler']['parameters']['custom_error_handler'];
		    /** If the errorhandler callback is defined but is not callable, then the object string in the callback is replaced with the object */				
		    if (is_array($errorhandler_callback) && !is_callable($errorhandler_callback)) {
 			    $errorhandler_callback[0]                                                                         = $this->GetComponent($errorhandler_callback[0]);
			    $this->configuration['required_frameworks']['errorhandler']['parameters']['custom_error_handler'] = $errorhandler_callback;			
		    }
		   
		    /** The shutdown function callback is checked */
            $shutdown_callback                                                                                    = $this->configuration['required_frameworks']['errorhandler']['parameters']['shutdown_function'];
		    /** If the shutdown function callback is defined but is not callable, then the object string in the callback is replaced with the object */				
		    if (is_array($shutdown_callback) && !is_callable($shutdown_callback)) {
    			$shutdown_callback[0]                                                                             = $this->GetComponent($shutdown_callback[0]);
			    $this->configuration['required_frameworks']['errorhandler']['parameters']['shutdown_function']    = $shutdown_callback;			
		    }
		    /** Otherwise the default application shutdown callback is used */
		    else {
    			$errorhandler_callback[0]                                                                         = $this->GetComponent("application");
			    $this->configuration['required_frameworks']['errorhandler']['parameters']['shutdown_function'] = array($errorhandler_callback[0],"CustomShutdownFunction");			
		    }
			/** The errorhandler class object is created */
    	    $this->InitializeObject("errorhandler");    	
	    }
		/** The authentication methods */
		$authentication_methods                                                                                  = array("api","session","http");
		/** Both session and http authentication are enabled */
		for ($count =0; $count < count($authentication_methods); $count++) {
			/** The authentication method */
			$authentication_method			                                                                     = $authentication_methods[$count];
		    /** 
             * If authentication is enabled						 
             * Then authentication callback defined by the user configuration is called
		     * If the user has not defined the authentication callback
		     * Then the default authentication callback is called
            */
            if ($this->GetConfig($authentication_method.'_auth','enable')) {
        	    /** The authentication callback is checked */
                $auth_callback                                                                                   = $this->GetConfig($authentication_method.'_auth','auth_callback');
		        /** If the auth callback is defined but is not callable, then the object string in the callback is replaced with the object */				
		        if (is_array($auth_callback) && !is_callable($auth_callback)) {
   			        $auth_callback[0]                                                                            = $this->GetComponent($auth_callback[0]);
			        $this->SetConfig($authentication_method.'_auth','auth_callback',$auth_callback); 
		        }
                if (is_callable($this->GetConfig($authentication_method.'_auth','auth_callback')))
                    call_user_func($this->GetConfig($authentication_method.'_auth','auth_callback'));
                else {
               	    /** The default authentication callback is called */
				    call_user_func(array($this->GetComponent("api"),ucfirst($authentication_method)."Authentication"));
                }                
            }
        }
    }

	/**
     * Used to get url parameters
     * 		 
     * This function fetches parameter attribute given in url
	 * It decoded the parameter value and saves it to application configuration
     */
    final protected function GetUrlParameters()
    {    	       
        /** The url parameters */
		$parameters                                                    = $this->GetConfig('general','parameters');		
		/** If the url parameter called "parameter" was given then it is decoded and saved to application configuration */
		if (isset($parameters['parameters']) && is_string($parameters['parameters'])) {
			/** The parameters argument given in the url */			
			$encoded_parameters                                        = $parameters['parameters'];
			/** The parameters are decoded into an array */			
			$decoded_parameters                                        = $this->GetComponent("application")->GetUrlParameters($encoded_parameters);
			/** The decoded parameters are saved to application configuration */
			$parameters['parameters']                                  = $decoded_parameters;
			/** The original parameters string is saved to application configuration */
			$parameters['parameters']['parameter']                     = $encoded_parameters;					
			$this->SetConfig("general","parameters",$parameters);
		}				
    }
	 
	/**
     * Used to enable php sessions
     * 		 
     * This function enables php sessions
     */
    final protected function EnableSessions()
    {    	       
        /** 
         * If the application needs session support and application is called from browser	then session_start() is called
         * And $_SESSION data is saved to session parameter
         */
         
        if ($this->GetConfig('general','enable_sessions')) {
        	/** If the session is not started then it is started */
            if (!$this->IsSessionStarted()) {                	
                session_start();
				session_regenerate_id();
			}
            $this->SetConfig('general','session',$_SESSION);
        }
    }
		
	/**
     * Used to return the application configuration
     * 		 
     * This function runs the application configuration
	 * 
	 * @return array $configuration the application configuration
     */
    final public function GetConfiguration()
    {    	
    	$configuration = $this->configuration;
		return $configuration; 
    }
	 
	/**
     * Used to get the list of component objects
     * 		 
     * This function returns the list of component objects
	 * 
	 * @return array $components the application components
     */
    final public function GetComponentList()
    {    	
    	$component_list = $this->component_list;
		return $component_list; 
    }
	 	 
	/**
     * Used to set the application configuration
     * 		 
     * This function sets the application configuration
	 * 
	 * @param array $configuration the application configuration
     */
    final public function SetConfiguration($configuration)
    {    	
    	$this->configuration = $configuration;		
    }
	
    /**
     * Used to run the application
     * 		 
     * This function runs the application
	 * It first initializes the application configuration
	 * It then runs the application by calling the Main function of the application
	 * 
	 * @return string $response the application response
     */
    final public function RunApplication()
    {
    	/** The application response. it contains the string that will be returned by the application as output */
    	$response           = "";
		/** The application is initialized */
		$this->Initialize();
    	/** The application object is fetched */
        $application_object = $this->GetComponent("application");
		/** The application is run and response is returned */
        $response           = $application_object->Main();
		
		return $response;
    }
}