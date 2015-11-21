<?php

namespace Framework\Configuration;

use \Framework\Configuration\Base as Base;

/**
 * Default application configuration class
 * 
 * Final class. cannot be extended by child class
 * It provides default application configuration
 * 
 * @category   Framework
 * @package    Configuration
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0
 * @link       N.A
 */
class DefaultConfiguration extends Base
{
    /**
     * Used to get default general configuration
     * 
     * It returns configuration containing general parameters
     * 
     * @since 1.0.0     	
     * @param array $user_configuration the user configuration
     * @throws object Exception an exception is thrown if the application name was not set in the user configuration
     * @throws object Exception an exception is thrown if no option parameter was given in url and no default option was defined
     * 
     * @return array $configuration the default configuration information
     */
    private function GetGeneralConfig($user_configuration)
    {
        /** If the application name is not set then an exception is thrown */
        if (!isset($user_configuration['general']['application_name']))
            throw new \Exception("Application name was not set in configuration settings");
        
        /** The configuration array is initialized */
        $configuration = $user_configuration;
        
        /** Used to indicate if application is a browser application */
        if (isset($user_configuration['general']['is_browser_application']))
            $configuration['general']['is_browser_application'] = $user_configuration['general']['is_browser_application'];
        else
            $configuration['general']['is_browser_application'] = (isset($_SERVER['HTTP_HOST']) || isset($_SERVER['HTTPS_HOST'])) ? true : false;
        
        /** The module name is saved to application configuration */
        if (!isset($user_configuration['general']['module']))
            $user_configuration['general']['module'] = str_replace(" ", "", $user_configuration['general']['application_name']);
        
		/** The application option and the option parameters are saved to application configuration */
        if (!isset($user_configuration['general']['default_option']))
            $user_configuration['general']['default_option'] = "Index";
		
        $configuration['general']['option']                        = isset($user_configuration['general']['parameters']['option']) ? $user_configuration['general']['parameters']['option'] : '';
        $configuration['general']['development_mode']              = true;
        $configuration['general']['parameters']                    = (isset($user_configuration['general']['parameters'])) ? $user_configuration['general']['parameters'] : array();
        $configuration['general']['parameters']['uploads']         = (isset($_FILES)) ? $_FILES : array();
        /** If the application is a browser application then the current url is saved */
        if ($configuration['general']['is_browser_application'])
            $configuration['general']['current_url'] = (isset($_SERVER['HTTPS_HOST']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        else
            $configuration['general']['current_url'] = "N.A";
      
        /** The application url mappings are set */
        $configuration['general']['application_url_mappings'] = array();
        
		/** Used to indicate that the application will implement a presentation class that will provide template parameters */
		$configuration['general']['use_presentation']         = false;
        /** Used to indicate if application should use sessions */
        $configuration['general']['enable_sessions'] = false;
        
        /** The folder name of the application */
        $configuration['general']['application_name'] = "";
		
		/** The type of database used by the application */
		if (!isset($user_configuration['general']['database_type']))
            $configuration['general']['database_type'] = "mysql";
        
		/** The DataObject class name is set */
		if ($configuration['general']['database_type'] = "mysql")
		    $configuration['general']['database_object_class'] = "\Framework\Object\MysqlDataObject";
		else if ($configuration['general']['database_type'] = "wordpress")
		    $configuration['general']['database_object_class'] = "\Framework\Object\WordpressDataObject";
		
		
        /** The line break character for the application is set */
        $configuration['general']['line_break'] = ($configuration['general']['is_browser_application']) ? "<br/>" : "\n";
        
        /** If no option is set in url and no default option is given by user an exception is thrown */
        if ($configuration['general']['option'] == "" && isset($user_configuration['general']) && !is_string($user_configuration['general']['default_option']))
            throw new \Exception("Option parameter is not given in url and no default option is specified");
        
        /** If no option is set in url then the default option is used */
        if ($configuration['general']['option'] == "" && isset($user_configuration['general']['default_option']))
            $configuration['general']['option'] = $user_configuration['general']['default_option'];

        /** User configuration is merged */
        if (isset($user_configuration['general']))
            $configuration['general'] = array_replace_recursive($configuration['general'], $user_configuration['general']);
        
        return $configuration;
    }
    
    /**
     * Used to get default authentication configuration
     * 
     * It returns configuration containing api, http and session authentication
     * 
     * @since 1.0.0
     * @param array $configuration the default configuration
     * @param array $user_configuration the user configuration
     * 
     * @return array $configuration the default configuration information
     */
    private function GetAuthConfig($configuration, $user_configuration)
    {
    	/** The title of the authentication box */
        $configuration['http_auth']['realm']                                   = "";
    	/** The supported authentication methodd */
    	$authentication_methods                                                = array("api","http","session");
		/** The default values for each authentication method are set */
		for ($count = 0; $count < count($authentication_methods); $count++) {
			/** The authentication method */
			$authentication_method                                             = $authentication_methods[$count];
            /** Used to indicate if application should be protected by http authentication */
            $configuration[$authentication_method.'_auth']['enable']           = false;
            /** The valid user names and passwords */
            $configuration[$authentication_method.'_auth']['credentials']      = array(array("user_name" => "","password" => ""));
            /** The callback function to call for checking if user is logged in */
            $configuration[$authentication_method.'_auth']['auth_callback']    = "";
			/** User configuration is merged */
            if (isset($user_configuration[$authentication_method.'_auth']))
                $configuration[$authentication_method.'_auth'] = array_replace_recursive($configuration[$authentication_method.'_auth'], $user_configuration[$authentication_method.'_auth']);			
		}		               
		
        return $configuration;
    }
    
    /**
     * Used to get default testing related configuration
     * 
     * It returns configuration containing test information
     * 
     * @since 1.0.0
     * @param array $configuration the default configuration
     * @param array $user_configuration the user configuration
     * 
     * @return array $configuration the default configuration information
     */
    private function GetTestConfig($configuration, $user_configuration)
    {
        /** Test parameters */
        /** Test mode indicates the application will be tested when its run */
        $configuration['testing']['test_mode']         = false;
        /** Test type indicates the type of application testing. i.e script, functional or unit */
        $configuration['testing']['test_type']         = 'unit';
        /** Test include files indicates the files that need to be including during testin */
        $configuration['testing']['include_files']     = array();
        /** The application test class */
        $configuration['testing']['test_class']        = "";
        /** Test parameters used during testing */
        /** The list of classes to test */
        $configuration['testing']['classes']           = array(
            ""
        );
        /** The url of html validator to use during testing */
        $configuration['testing']['validator_url']     = "https://html5.validator.nu/";
        /** The path to the application test_data folder */
        $configuration['testing']['test_data_folder']  = "";
        /** Used to indicate if the application should save page parameters to test_data folder */
        $configuration['testing']['save_test_data']    = false;
		/** Used to indicate if the application should append the parameters to test data file */
        $configuration['testing']['append_test_data']  = true;
        /** Used to indicate if the application should save test results */
        $configuration['testing']['save_test_results'] = true;
        /** The name of the test results file */
        if (!isset($user_configuration['testing']['test_results_file']))
            $user_configuration['testing']['test_results_file'] = 'test_results.txt';
        /** The path to the application documentation folder */
        if (!isset($user_configuration['testing']['documentation_folder']))
            $user_configuration['testing']['documentation_folder'] = 'documentation';
        
        /** The path to the application test data folder is set in user configuration */
        if (isset($user_configuration['testing']['test_data_folder']))
            $user_configuration['testing']['test_data_folder'] = $configuration['path']['application_path'] . DIRECTORY_SEPARATOR . $user_configuration['testing']['test_data_folder'];
        
        /** The path to the application documentation folder is set in user configuration */
        if (isset($user_configuration['testing']['documentation_folder']))
            $user_configuration['testing']['documentation_folder'] = $configuration['path']['application_path'] . DIRECTORY_SEPARATOR . $user_configuration['testing']['documentation_folder'];
        
        /** The full path to the test results file is set */
        if (isset($user_configuration['testing']['test_results_file']))
            $user_configuration['testing']['test_results_file'] = $user_configuration['testing']['documentation_folder'] . DIRECTORY_SEPARATOR . $user_configuration['testing']['test_results_file'];
 
        /** User configuration is merged */
        if (isset($user_configuration['testing']))
            $configuration['testing'] = array_replace_recursive($configuration['testing'], $user_configuration['testing']);
        
        return $configuration;
    }
    
    /**
     * Used to get default path related configuration
     * 
     * It returns configuration containing path information
     * 
     * @since 1.0.0
     * @param array $configuration the default configuration
     * @param array $user_configuration the user configuration
     * @throws 
     * @return array $configuration the default configuration information
     */
    private function GetPathConfig($configuration, $user_configuration)
    {
        /** The document root of the application is set */
        $configuration['path']['document_root'] = $_SERVER['DOCUMENT_ROOT'];
        /** The base folder path is set. All the application files including the framework are in this folder */
        $configuration['path']['base_path']     = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "..");
        
        /** If the application template name is not set then the default template basicsite is used */
        if (!isset($user_configuration['general']['template']))
            $user_configuration['general']['template'] = "basicsite";
        /** The application folder name is derived from the application name */
        if (isset($user_configuration['general']['application_name']) && !isset($user_configuration['path']['application_folder']))
            $user_configuration['path']['application_folder'] = strtolower(\Framework\Utilities\UtilitiesFramework::Factory("string")->CamelCase($user_configuration['general']['application_name']));
        
        /** The application domain name is set */
        if (isset($parameters['web_domain']))
            $user_configuration['path']['web_domain'] = $parameters['web_domain'];
        else if (isset($_SERVER['HTTP_HOST']) || isset($_SERVER['HTTPS_HOST']))
            $user_configuration['path']['web_domain'] = (isset($_SERVER['HTTPS_HOST'])) ? "https://" . $_SERVER['HTTPS_HOST'] : "http://" . $_SERVER['HTTP_HOST'];
        else
            $user_configuration['path']['web_domain'] = "http://example.com";
        
        /** The web path to the framework */
        if (!isset($user_configuration['path']['relative_web_domain']))
            $user_configuration['path']['relative_web_domain'] = trim(str_replace($configuration['path']['document_root'], "", $configuration['path']['base_path']), "/");
        
        /** The framework url is set */
        $user_configuration['path']['framework_url'] = $user_configuration['path']['web_domain'] . "/" . $user_configuration['path']['relative_web_domain'] . "/index.php";
        
        /** The web path to the application */
        if (!isset($user_configuration['path']['application_folder_url'])) /** The web path to the application */ 
            $user_configuration['path']['application_folder_url'] = $user_configuration['path']['web_domain'] . "/" . $user_configuration['path']['relative_web_domain'] . "/" . $user_configuration['path']['application_folder'];
        /** The url to the framework's template folder */
        if (!isset($user_configuration['path']['framework_template_url']))
            $user_configuration['path']['framework_template_url'] = $user_configuration['path']['web_domain'] . "/" . $user_configuration['path']['relative_web_domain'] . "/framework/templates/" . $user_configuration['general']['template'];
        
        /** The url to the application's template folder */
        if (!isset($user_configuration['path']['application_template_folder']))
            $user_configuration['path']['application_template_folder'] = "templates";
        $user_configuration['path']['application_template_url'] = $user_configuration['path']['web_domain'] . "/" . $user_configuration['path']['relative_web_domain'] . "/" . $user_configuration['path']['application_folder'] . "/" . $user_configuration['path']['application_template_folder'];
        
        /** The web path to the application's vendors folder */
        if (!isset($user_configuration['path']['web_vendor_path']))
            $user_configuration['path']['web_vendor_path']    = $user_configuration['path']['application_folder_url'] . "/vendors";
		else
			$user_configuration['path']['web_vendor_path']    = $user_configuration['path']['web_domain'] . "/" . $user_configuration['path']['relative_web_domain'] . "/" . $user_configuration['path']['web_vendor_path'];

        /** The path to the framework folder */
        $configuration['path']['framework_path']            = $configuration['path']['base_path'] . DIRECTORY_SEPARATOR . "framework";
        /** The path to the application folder */
        $configuration['path']['application_path']          = $configuration['path']['base_path'] . DIRECTORY_SEPARATOR . $user_configuration['path']['application_folder'];
        /** The path to the framework templates html folder */
        $configuration['path']['template_path']             = $configuration['path']['framework_path'] . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $user_configuration['general']['template'] . DIRECTORY_SEPARATOR . "html";
        /** The path to the application templates folder */
        $configuration['path']['application_template_path'] = $configuration['path']['application_path'] . DIRECTORY_SEPARATOR . $user_configuration['path']['application_template_folder'];
        /** The path to the application tmp folder */
        $configuration['path']['tmp_folder_path']           = $configuration['path']['application_path'] . DIRECTORY_SEPARATOR . 'tmp';
        /** The path to the vendor folder */
        $configuration['path']['vendor_folder_path']        = $configuration['path']['application_path'] . DIRECTORY_SEPARATOR . 'vendors';
					
		/** The folder path to the application's vendors folder */
        if (!isset($user_configuration['path']['vendor_folder_path']))
            $user_configuration['path']['vendor_folder_path'] = $configuration['path']['application_path'] . DIRECTORY_SEPARATOR . 'vendors';
		else
			$user_configuration['path']['vendor_folder_path'] = $configuration['path']['base_path'] . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $user_configuration['path']['vendor_folder_path'];		
		        
        /** User configuration is merged */
        if (isset($user_configuration['path']))
            $configuration['path'] = array_replace_recursive($configuration['path'], $user_configuration['path']);
        
        return $configuration;
    }
    
    /**
     * Used to get default required frameworks configuration
     * 
     * It returns configuration containing required frameworks information
     * 
     * @since 1.0.0
     * @param array $configuration the default configuration
     * @param array $user_configuration the user configuration
     * 
     * @return array $configuration the application configuration information
     */
    private function GetRequiredFrameworksConfig($configuration, $user_configuration)
    {
        /** The parameters array is initialized */
        $error_handler_parameters                           = $db_parameters = $filesystem_parameters = array();
        /** The logging class parameters are set */
        /** The shutdown function callable */
        $error_handler_parameters['shutdown_function']      = "";
        /** Used to indicate if application should use custom error handler */
        $error_handler_parameters['register_error_handler'] = true;
        /** Used to indicate if application is in development mode */
        $error_handler_parameters['development_mode']       = (isset($user_configuration['general']['development_mode'])) ? $user_configuration['general']['development_mode'] : true;
        /** Custom error handler callback */
        $error_handler_parameters['custom_error_handler']   = "";
        /** Used to indicate if the error message should be emailed to user */
        $error_handler_parameters['email']['enable']        = (isset($user_configuration['required_frameworks']['errorhandler']['parameters']['enable'])) ? $user_configuration['required_frameworks']['errorhandler']['parameters']['enable'] : false;
        /** Used to indicate if application is being run from browser */
        $error_handler_parameters['is_browser_application'] = $configuration['general']['is_browser_application'];
        /** The email at which log message is sent */
        $error_handler_parameters['email']['email_address'] = '';
        /** Subject of the notification email */
        $error_handler_parameters['email']['email_subject'] = '';
        /** Addition log email smtp headers such as From: */
        $error_handler_parameters['email']['email_header']  = "";
        /** Full path of error log file */
        $error_handler_parameters['log_file_name']          = "";
        /** Used to indicate if error should be logged using web hook */
        $error_handler_parameters['web_hook']['enable']     = "";
        $error_handler_parameters['web_hook']['url']        = "";
        /** The database class parameters are set */
        $db_parameters                                      = array(
            "host" => "",
            "user" => "",
            "password" => "",
            "database" => "",
            "debug" => ""
        );
        
        /** The utilities class parameters are set */
        $filesystem_parameters['table_prefix']            = "";
        $filesystem_parameters['function_cache_duration'] = array();
        $filesystem_parameters['upload_folder']           = $configuration['path']['tmp_folder_path'];
        $filesystem_parameters['allowed_extensions']      = array(
            "xls",
            "xlsx",
            "txt"
        );
        $filesystem_parameters['max_allowed_file_size']   = "2048";
        $filesystem_parameters['link']                    = '';
        /** The required framework objects are defined */
        $configuration['required_frameworks']             = array(
            "errorhandler" => array(
                "class_name" => "Framework\Utilities\ErrorHandler",
                "parameters" => $error_handler_parameters
            ),
            "application" => array(
                "class_name" => "",
                "parameters" => array()
            ),
            "database" => array(
                "class_name" => "Framework\Utilities\DatabaseFunctions",
                "parameters" => $db_parameters
            ),
            "filesystem" => array(
                "class_name" => "Framework\Utilities\FileSystem",
                "parameters" => $filesystem_parameters
            )
        );
        
        /** User configuration is merged */
        if (isset($user_configuration['required_frameworks']))
            $configuration['required_frameworks'] = array_replace_recursive($configuration['required_frameworks'], $user_configuration['required_frameworks']);
        
        return $configuration;
    }
    /**
     * Used to initialize php settings
     * 
     * It sets the php settings
     * 
     * @since 1.0.0
     * @param array $user_configuration the user configuration
     */
    private function InitializePhpSettings($user_configuration)
    {
        error_reporting(E_ALL);
        date_default_timezone_set('Asia/Karachi');
        if ($user_configuration['general']['development_mode']) {
            ini_set('display_errors', E_ALL);
            ini_set('display_startup_errors', true);
        } else {
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', false);
        }
    }
    
    /**
     * Used to get updated application configuration data
     * The configuration data can be overridden by child classes
     * 		 
     * It returns an array containing application configuration data
     * Used to set default php settings
     * It also merges the user settings with the default settings
     * Certain settings are individually updated
     * For example the application name given by the user is used to update folder path information     
     * If the application is under development then all errors are displayed
     * If the application is not under development then no errors are displayed		
     * It also sets the default time zone     
     * 
     * @since 1.0.0     
     * @param array $user_configuration user defined application configuration
     * 
     * @return array $configuration the application configuration information
     */
    public function GetUpdatedConfiguration($user_configuration)
    {	
        /** The general default configuration is fetched */
        $configuration = self::GetGeneralConfig($user_configuration);
     
	 	/** The php error configuration is set */
    	$this->InitializePhpSettings($configuration);
		   
        /** The http and session authentication default configuration is fetched */
        $configuration = array_replace_recursive($configuration, self::GetAuthConfig($configuration, $user_configuration));
        
        /** The path default configuration is fetched */
        $configuration = array_replace_recursive($configuration, self::GetPathConfig($configuration, $user_configuration));
        
        /** The test default configuration is fetched */
        $configuration = array_replace_recursive($configuration, self::GetTestConfig($configuration, $user_configuration));
        
        /** The required frameworks default configuration is fetched */
        $configuration = array_replace_recursive($configuration, self::GetRequiredFrameworksConfig($configuration, $user_configuration));
        
        return $configuration;
    }
}
?>