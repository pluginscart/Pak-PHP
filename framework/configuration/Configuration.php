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
 * @link       N.A
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
     * @since 2.0.0
	 * @param array $parameters the application parameters given by the user	 	
     */
    public function __construct($parameters){
    	/** The user defined application configuration */
    	$this->user_configuration                                    = array();
		/** The parameters given by the user are set in the application configuration */
    	$this->user_configuration['parameters']                      = $parameters;
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
     * @since 1.0.0
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
     * 
     * @since 1.0.0		  
     */
    final protected function IncludeRequiredClasses()
    {
        /** Test mode status is returned */
        $test_mode = $this->GetConfig("testing","test_mode");
        /** The list of files to be included for testing is fetched from configuration */
        if ($test_mode)
            $test_include_files                               = $this->GetConfig('testing','include_files');
        else
            $test_include_files                               = array();		
        /** Each file that needs to be included for the current url is included */
        $application_url_mappings                             = $this->GetConfig("general","application_url_mappings");
		$option                                               = $this->GetConfig("general","option");		
        if (isset($application_url_mappings[$option]) && isset($application_url_mappings[$option]['include_files']))
            $files_to_include                                 = $application_url_mappings[$option]['include_files'];
        else
            $files_to_include                                 = array();
        
        /** The files to include from test configuration are merged with the files to include from application configuration */
        $files_to_include = array_merge($test_include_files, $files_to_include);
        /** All files that need to be included are included */        
        for ($count = 0; $count < count($files_to_include); $count++) {
            $file_name = $files_to_include[$count];
			/** The {vendor_folder_path} string is replaced with the vendor folder path */
			$file_name = str_replace('{vendor_folder_path}',$this->GetConfig('path','vendor_folder_path'),$file_name);					
            if (is_file($file_name))
                require_once($file_name);
            else
                throw new \Exception("Invalid include file name: " . $file_name . " given for page option: " . $this->GetConfig("general","option"));
        }
    }
    
    /**
     * Used to initialize the application
     * 
     * Initializes objects needed by the application
     * Sets application configuration
     * 
     * @since 1.0.0     
     */
    protected function Initialize()
    {
    	/** The configuration object for the current object is set */
    	$this->SetConfigurationObject($this);        
        /** User configuration settings are merged with default configuration settings */
        $default_configuration = new DefaultConfiguration();
		/** The default configuration object is created */
        $this->configuration   = $default_configuration->GetDefaultConfiguration($this->user_configuration);
        /** The error handler object is created */
        $this->InitializeObject("errorhandler");
        /** All required classes are included */
        $this->IncludeRequiredClasses();
        /** Php Sessions are enabled if user requested sessions */
        $this->EnableSessions();
		/** Session authentication is enabled if user requested session authentication */
		$this->EnableSessionAuthentication();
        /** Http authentication is enabled if user requested http authentication */
		$this->EnableHttpAuthentication();
		/** Url parameters given as parameter attribute in url are decoded and saved to application configuration */		
		$this->GetUrlParameters();		
    }
    
	/**
     * Used to get url parameters
     * 		 
     * This function fetches parameter attribute given in url
	 * It decoded the parameter value and saves it to application configuration
     * 
     * @since 1.0.0	
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
     * 
     * @since 1.0.0	
     */
    final protected function EnableSessions()
    {    	       
        /** 
         * If the application needs session support and application is called from browser	then session_start() is called
         * And $_SESSION data is saved to session parameter
         */
         
        if ($this->GetConfig('general','enable_sessions')) {
        	/** If the session is not started then it is started */
            if (!$this->IsSessionStarted())
                session_start();			
            $this->SetConfig('general','session',$_SESSION);
        }
    }
	
	/**
     * Used to enable session authentication
     * 		 
     * This function registers the session authentication callback if session authentication is required
     * 
     * @since 1.0.0
	 * 
	 * @return array $configuration the application configuration
     */
    final protected function EnableSessionAuthentication()
    {    	
    	/** 
         * If session authentication is required and application is called from browser						 
         * Then session authentication callback is called
         */
        if ($this->GetConfig('session_auth','enable')) {
            if (is_callable($this->GetConfig('session_auth','auth_callback')))
                call_user_func($this->GetConfig('session_auth','auth_callback'));
            else
                throw new \Exception("Please define a valid session authentication error callback");
        }
    }
	
	/**
     * Used to enable http authentication
     * 		 
     * This function registers the http authentication callback if http authentication is required
     * 
     * @since 1.0.0
	 * 
	 * @return array $configuration the application configuration
     */
    final protected function EnableHttpAuthentication()
    {    	
    	/** 
         * If http authentication is required and application is called from browser
         * Then the http authentication callback is called and user is asked to authenticate 
         */
        if ($this->GetConfig('http_auth','enable')) {
            if (is_callable($this->GetConfig('http_auth','auth_callback')))
                call_user_func($this->GetConfig('http_auth','auth_callback'));
            else
                throw new \Exception("Please define a valid http authentication error callback");
        }
    }
	
	/**
     * Used to return the application configuration
     * 		 
     * This function runs the application configuration     
     * 
     * @since 1.0.0
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
     * @since 1.0.0
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
     * @since 1.0.0	 
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
     * @since 1.0.0
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