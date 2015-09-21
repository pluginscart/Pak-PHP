<?php

namespace ApplicationConfigurationFramework;

/**
 * Base configuration class for browser based applications
 * 
 * Singleton class. must be inherited by a child class
 * It extends the Default_ApplicationConfiguration class
 * The Default_ApplicationConfiguration class contains default configuration values
 * Initializes objects and sets configuration
 * 
 * @category   ApplicationConfiguration
 * @package    ApplicationConfigurationFramework
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0
 * @link       N.A
 */
class ApplicationConfiguration extends DefaultApplicationConfiguration
{
    /**
     * The single static instance
     */
    protected static $instance;
    /**
     * Specifies the frameworks required by the application
     */
    protected static $configuration;
    /**
     * List of objects that can be used by the application
     */
    protected static $component_list;
    /**
     * Class constructor
     * Used to prevent creating an object of this class outside of the class using new operator
     * 
     * Used to implement Singleton class
     * Sets default configuration values
     * 
     * @since 1.0.0
     * @param array $argv the command line parameters given by the user		  
     */
    protected function __construct($argv)
    {
        static::$configuration = DefaultApplicationConfiguration::GetConfiguration($argv);
    }
    /**
     * Used to return a single instance of the class
     * 
     * Checks if instance already exists
     * If it does not exist then it is created
     * The instance is returned
     * 
     * @since 1.0.0
     * @param array $argv the command line parameters given by the user
     * 
     * @return ApplicationConfiguration static::$instance name the instance of the correct child class is returned 
     */
    public static function GetInstance($argv)
    {        
        if (static::$instance == null) {
            static::$instance = new static($argv);
        }
        return static::$instance;       
    }
    /**
     * Used to create framework objects specified by the user
     * 
     * Creates the objects specified by the user and adds the objects to application configuration 		
     * Objects are created using GetInstance method if it is supported or new operator if class is not Singleton
     * 		
     * @since 1.0.0		 
     */
    private function InitializeFrameworkObjects()
    {
        /** Each framework object is created an added to application configuration **/
        foreach (static::$configuration['required_frameworks'] as $framework_name => $object_information) {
        	/** The class parameters are initialized **/
        	if(!isset($object_information['parameters']))$object_information['parameters']="";
			 
            /** The name of the framework class **/
            $framework_class_name = $object_information['class_name'];
            /** 
             * Used to check if class exists
             * The class is autoloaded if it is not already included 
             * If it does not exist then an exception is thrown
             */
            if (!class_exists($framework_class_name, true))
                throw new \Exception("Class: " . $framework_class_name . " does not exist", 1);
            /**
             * Used to check if class implments Singleton pattern
             * If it has a static function called GetInstance then
             * It is assumed to be a Singleton class
             * The GetInstance method is used to get class instance
             */
            $callable_singleton_method = array(
                $framework_class_name,
                "GetInstance"
            );
            if (is_callable($callable_singleton_method))
                $framework_class_obj = call_user_func_array($callable_singleton_method, array(
                    $object_information['parameters']
                ));
            /** If it is not a Singleton class then an object of the class is created using new operator **/
            else
                $framework_class_obj = new $framework_class_name($object_information['parameters']);
            /** The object is saved to object list **/
            static::$component_list[$framework_name] = $framework_class_obj;
        }       
    }
    /**
     * Used to include required files
     * 
     * It gets list of all files that need to be included
     * Including the files given in test parameters and url handling parameters
     * 
     * @since 1.0.0		  
     */
    private function IncludeRequiredClasses()
    {        
        /** Test mode status is returned **/
        $test_mode = static::$configuration['testing']["test_mode"];
        /** The list of files to be included for testing is fetched from configuration **/
        if ($test_mode)
            $test_include_files = static::$configuration['testing']['include_files'];
        else
            $test_include_files = array();
        /** Each file that needs to be included for the current url is included **/
        if (isset(static::$configuration['application_url_mappings'][static::$configuration['option']]['include_files']))
            $files_to_include = static::$configuration['application_url_mappings'][static::$configuration['option']]['include_files'];
        else
            $files_to_include = array();
        
        /** The files to include from test configuration are merged with the files to include from application configuration **/
        $files_to_include = array_merge($test_include_files, $files_to_include);
        /** All files that need to be included are included **/
        for ($count = 0; $count < count($files_to_include); $count++) {
            $file_name = $files_to_include[$count];
            if (is_file($file_name))
                require_once($file_name);
            else
                throw new \Exception("Invalid include file name: " . $file_name . " given for page option: " . static::$configuration['option'], 1);
        }       
    }
    /**
     * Used to initialize the application
     * 
     * Initializes objects needed by the application
     * Sets application configuration
     * 
     * @since 1.0.0		 
     * @param array $user_configuration an array containing application configuration information		 
     */
    protected function Initialize($user_configuration)
    {        
        /**
         * User configuration settings are merged with default configuration settings
         * Php settings such as display_error settings are set  
         */
        $this->SetApplicationSettings($user_configuration, static::$configuration);
        
        /**
         * The required frameworks are loaded
         * For each required framework class, an object is created with the given parameters
         * Checks if class has the method GetInstance
         * If it does then it is a singleton class and this method is used to get object instance
         * The object is saved to the object list						 
         * Autoload function should auto load all the class files
         */
        $this->InitializeFrameworkObjects();
        /** All required classes are included **/
        $this->IncludeRequiredClasses();
        
        /** 
         * If http authentication is required and application is called from browser
         * Then the http authentication callback is called and user is asked to authenticate 
         */
        if (static::$configuration['http_authentication']['enable'] && static::$configuration['is_browser_application']) {
            if (is_callable(static::$configuration['http_authentication']['error_callback']))
                call_user_func(static::$configuration['http_authentication']['error_callback']);
            else
                throw new \Exception("Please define a valid http authentication error callback", 1);
        }
        /** 
         * If the application needs session support and application is called from browser	then session_start() is called
         * And $_SESSION data is saved to session parameter
         */
        if (isset(static::$configuration['use_sessions']) && static::$configuration['is_browser_application']) {
            session_start();
            static::$configuration['parameters']['session'] = $_SESSION;
        }
        
        /** 
         * If session authentication is required and application is called from browser						 
         * Then session authentication callback is called
         */
        if (static::$configuration['session_authentication']['enable'] && static::$configuration['is_browser_application']) {
            if (is_callable(static::$configuration['session_authentication']['error_callback']))
                call_user_func(static::$configuration['session_authentication']['error_callback']);
            else
                throw new \Exception("Please define a valid session authentication error callback", 1);
        }        
    }
    /**
     * Used to get components and configuration values
     * 
     * Throws an exception if the specified object or configuration could not be found
     * Otherwise returns the required component objects and configuration values
     * 		
     * @since 1.0.0
     * @param array $object_names an array of object names to fetch
     * @param array $configuration_names an array of configuration values to fetch
     * @throws Exception an exception is thrown if required component of configuration does not exist
     * 
     * @return array $components_and_configuration and array with 2 elements
     * first is the list of component objects
     * second is the list of configuration values
     */
    public static function GetComponentsAndConfiguration($object_names, $configuration_names)
    {        
        $component_objects_list    = array();
        $configuration_values_list = array();
        
        /** The list of component objects is fetched **/
        for ($count = 0; $count < count($object_names); $count++) {
            $object_name = $object_names[$count];
            if (!isset(static::$component_list[$object_name]))
                throw new \Exception("Application component object: " . $object_name . " could not be found", 10);
            $component_objects_list[$object_name] = static::$component_list[$object_name];
        }
        
        /** The list of configuration values is fetched **/
        for ($count = 0; $count < count($configuration_names); $count++) {
            $configuration_name = $configuration_names[$count];
            if (!isset(static::$configuration[$configuration_name]))
                throw new \Exception("Application configuration: " . $configuration_name . " could not be found", 10);
            $configuration_values_list[$configuration_name] = static::$configuration[$configuration_name];
        }
        
        $components_and_configuration = array(
            $component_objects_list,
            $configuration_values_list
        );
        
        return $components_and_configuration;       
    }
    /**
     * Used to get the specified object
     * 
     * Throws an exception if the specified object could not be found
     * Otherwise returns the object		 		
     * 		
     * @since 1.0.0
     * @param object $object_name name of the required object
     */
    public static function GetComponent($object_name)
    {        
        if (!isset(static::$component_list[$object_name]))
            throw new \Exception("Application object: " . $object_name . " could not be found", 10);
        else
            return static::$component_list[$object_name];       
    }
    /**
     * Used to get the specified configuration setting
     * 
     * Throws an exception if the specified configuration setting could not be found
     * Otherwise returns the configuration setting
     * 
     * @since 1.0.0
     * @param string $config_name name of the required configuration
     */
    public static function GetConfig($config_name)
    {        
        if (!isset(static::$configuration[$config_name]))
            throw new \Exception("Application configuration could not be found for config name: " . $config_name, 10);
        else
            return static::$configuration[$config_name];       
    }
    /**
     * Used to set the specified configuration setting
     * 
     * Throws an exception if the specified configuration setting could not be found
     * Otherwise returns the configuration setting
     * 
     * @since 1.0.0
     * @param string $config_name name of the required configuration
     * @param string $config_value value of the required configuration
     */
    public static function SetConfig($config_name, $config_value)
    {        
        static::$configuration[$config_name] = $config_value;       
    }
    /**
     * Used to run the application
     * 		 
     * This function runs the application
     * It can also run the test version of the application depending on value of test_mode configuration
     * 
     * @since 1.0.0
     */
    public function RunApplication()
    {        
        $application_object = static::$component_list['application'];
        $application_object->HandleRequest();       
    }
}