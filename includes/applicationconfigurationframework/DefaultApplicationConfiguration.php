<?php

namespace ApplicationConfigurationFramework;

/**
 * Default application configuration class
 * 
 * Abstract class. must be extended by a child class
 * It provides default application configuration
 * 
 * @category   ApplicationConfiguration
 * @package    ApplicationConfigurationFramework
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0
 * @link       N.A
 */
abstract class DefaultApplicationConfiguration
{    
    /**
     * Used to get default application configuration data
     * The configuration data can be overridden by child classes
     * 		 
     * It returns an array containing application configuration data
     * 
     * @since 1.0.0
     * @param array $argv the command line parameters given by the user  
     */
    protected static function GetConfiguration($argv)
    {        
        /** If application is being run from commandline then the command line parameters are copied to $_REQUEST **/
        if (!isset($_SERVER['HTTP_HOST']) && isset($_SERVER['HTTPS_HOST']))
            parse_str(implode('&', array_slice($argv, 1)), $_REQUEST);
        
        $option                                  = isset($_REQUEST['option']) ? $_REQUEST['option'] : '';
        /** Used to indicate if application is a browser application **/
        $configuration['is_browser_application'] = (isset($_SERVER['HTTP_HOST'])) ? true : false;
       
        /** The application option and the option parameters are saved to application configuration **/
        $configuration['default_option']        = "";
        $configuration['option']                = $option;
        $configuration['development_mode']      = true;
        $configuration['parameters']            = $_REQUEST;
        $configuration['parameters']['uploads'] = (isset($_FILES)) ? $_FILES : array();
        /** If the application is a browser application then the current url is saved **/
        if ($configuration['is_browser_application'])
            $configuration['parameters']['current_url'] = (isset($_SERVER['HTTPS_HOST']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        else
            $configuration['parameters']['current_url'] = "N.A";
        
        /** HTTP authentication information **/
        /** Used to indicate if application should be protected by http authentication **/
        $configuration['http_authentication']['enable']         = false;
        /** The valid user names and passwords **/
        $configuration['http_authentication']['credentials']    = array(
            array(
                "user_name" => "",
                "password" => ""
            )
        );
        /** The title of the authentication box **/
        $configuration['http_authentication']['realm']          = "";
        /** The callback function to call in case of error **/
        $configuration['http_authentication']['error_callback'] = "";
        
        /** Session authentication information **/
        /** Used to indicate if application should be protected by session authentication **/
        $configuration['session_authentication']['enable']         = false;
        /** The valid user names and passwords **/
        $configuration['session_authentication']['credentials']    = array(
            array(
                "user_name" => "",
                "password" => ""
            )
        );
        /** The title of the authentication box **/
        $configuration['session_authentication']['realm']          = "";
        /** The callback function to call for checking if user is logged in **/
        $configuration['session_authentication']['error_callback'] = "";
        
        /** Test parameters **/
        /** Test mode indicates the application will be tested when its run **/
        $configuration['testing']['test_mode']  = false;
        /** Test type indicates the type of application testing. i.e functional or unit **/
        $configuration['testing']['test_type']  = 'unit';
        /** The application test class **/
        $configuration['testing']['test_class'] = "";
        /** The path to the test results file **/
        if ($configuration['is_browser_application'])
            $configuration['testing']['test_results_file'] = 'test_results.html';
        else
            $configuration['testing']['test_results_file'] = 'test_results.txt';
        /** Test parameters used during testing **/
        /** The list of classes to test **/
        $configuration['testing']['classes']              = array(
            ""
        );
		/** The application url mappings are set **/
		$configuration['application_url_mappings']=array();
        /** The url of html validator to use during testing **/
        $configuration['testing']['validator_url']        = "https://html5.validator.nu/";
        /** The path to the application test_data folder **/
        $configuration['testing']['test_data_folder']     = "";
        /** The path to the application test_data folder **/
        $configuration['testing']['documentation_folder'] = "documentation";
        
        /** The folder name of the application **/
        $configuration['application_name']    = "";
        /** Used to indicate if the application should save page parameters to test_data folder **/
        $configuration['testing']['save_test_data']      = false;
        /** The line break character for the application is set **/
        $configuration['line_break']          = ($configuration['is_browser_application']) ? "<br/>" : "\n";
        /** The framework folder name is set **/
        $configuration['framework_path']      = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "..";
        /** The application domain name is set **/
        $configuration['web_domain']          = "";
        /** The relative web path of the application from the document root **/
        $configuration['relative_web_domain'] = "/";
        /** The path to the include folder of the framework **/
        $configuration['include_path']        = $configuration['framework_path'] . DIRECTORY_SEPARATOR . 'includes';
        /** The path to the applications folder of the framework **/
        $configuration['applications_path']   = $configuration['framework_path'] . DIRECTORY_SEPARATOR . 'applications';
        /** The path to the templates folder of the framework **/
        $configuration['template_path']       = $configuration['framework_path'] . DIRECTORY_SEPARATOR . 'templates';
        /** The path to the application tmp folder **/
        $configuration['tmp_folder_path']     = $configuration['applications_path'] . DIRECTORY_SEPARATOR . 'tmp';
        /** The path to the vendor folder of the framework **/
        $configuration['vendor_folder_path']  = $configuration['framework_path'] . DIRECTORY_SEPARATOR . 'vendors';
        
        /** The web path to the application **/
        $configuration['default_configuration']['web_application_url'] = $configuration['web_domain'] . "/applications/" . $configuration['application_name'] . "/index.php";
        /** The web path to the application's template folder **/
        $configuration['default_configuration']['web_template_path']   = $configuration['web_domain'] . "/templates";
        /** The web path to the application's vendors folder **/
        $configuration['default_configuration']['web_vendor_path']     = $configuration['web_domain'] . "/vendors";
        
        /** The parameters array is initialized **/
        $error_handler_parameters                               = $db_parameters = $utilities_parameters = array();
        /** The logging class parameters are set **/
        /** The shutdown function callable **/
        $error_handler_parameters['shutdown_function']          = "";
        /** Used to indicate if application should use custom error handler **/
        $error_handler_parameters['register_error_handler']     = true;
        /** Used to indicate if application should display the error. If it is false then a simple javascript alert message will be shown in browser **/
        $error_handler_parameters['display_error']              = true;
        /** Custom error handler callback **/
        $error_handler_parameters['custom_error_handler']       = "";
        /** Used to indicate if the error message should be emailed to user **/
        $error_handler_parameters['email_error']                = ($configuration['is_browser_application']);
        /** Used to indicate if application is being run from browser **/
        $error_handler_parameters['is_browser_application']     = $configuration['is_browser_application'];
        /** The email at which log message is sent **/
        $error_handler_parameters['log_email']                  = '';
        /** Subject of the notification email **/
        $error_handler_parameters['notification_email_subject'] = '';
        /** Addition log email smtp headers such as From: **/
        $error_handler_parameters['log_email_header']           = "";
        /** Full path of error log file **/
        $error_handler_parameters['log_file_name']              = "";
        /** The database class parameters are set **/
        $db_parameters                                = array(
            "host" => "",
            "user" => "",
            "password" => "",
            "database" => "",
            "debug" => ""
        );
        
        /** The utilities class parameters are set **/
        $filesystem_parameters['upload_folder']           = $configuration['tmp_folder_path'];
        $filesystem_parameters['allowed_extensions']      = array(
            "xls",
            "xlsx",
            "txt"
        );
        $filesystem_parameters['max_allowed_file_size']   = "2048";
     
        /** The required framework objects are defined **/
        $configuration['required_frameworks'] = array(
            "errorhandler" => array(
                "class_name" => "\UtilitiesFramework\ErrorHandler",
                "parameters" => $error_handler_parameters
            ),
            "application" => array(
                "class_name" => "",
                "parameters" => array()
            ),
            "database" => array(                
                "class_name" => "\UtilitiesFramework\DatabaseFunctions",
                "parameters" => $db_parameters
            ),
            "filesystem" => array(                
                "class_name" => "\UtilitiesFramework\FileSystem",
                "parameters" => $filesystem_parameters
            )
        );
        
        return $configuration;        
    }    
    /**
     * Used to set default php settings
     * It also merges the user settings with the default settings
     * Certain settings are individually updated
     * For example the application name given by the user is used to update folder path information 
     * 
     * If the application is under development then all errors are displayed
     * If the application is not under development then no errors are displayed		
     * It also sets the default time zone
     * In both cases all errors are reported
     * Certain configuration settings are updated
     * 		
     * @since 1.0.0		 
     * @param boolean $is_development used to indicate if application is in development mode
     * @param array $user_configuration user defined application configuration
     * @param array $configuration the default application configuration
     */
    protected function SetApplicationSettings($user_configuration, $configuration)
    {    	         
        /** If no option is set in url then the default option is used **/
        if (static::$configuration['option'] == "")
            static::$configuration['option'] = $user_configuration['default_option'];
		
        /** The path to the application test_data folder is set in user configuration **/
        if(isset($user_configuration['testing']['test_data_folder']))        
            $user_configuration['testing']['test_data_folder'] = static::$configuration['applications_path'] . DIRECTORY_SEPARATOR . $user_configuration['application_name'] . DIRECTORY_SEPARATOR . $user_configuration['testing']['test_data_folder'];
		
        /** An exception is thrown if the test data folder does not exist **/      
        if (isset($user_configuration['testing']['test_data_folder'])&&!is_dir($user_configuration['testing']['test_data_folder']))
            throw new \Exception("Test data folder path: " . $user_configuration['testing']['test_data_folder'] . " does not exist", 150);
        
        /** The path to the application documentation folder is set in user configuration **/
        if (isset($user_configuration['testing']['documentation_folder']))
            $user_configuration['testing']['documentation_folder'] = static::$configuration['applications_path'] . DIRECTORY_SEPARATOR . $user_configuration['application_name'] . DIRECTORY_SEPARATOR . $user_configuration['testing']['documentation_folder'];       
        
        /** An exception is thrown if the documentation folder does not exist **/
        if (isset($user_configuration['testing']['documentation_folder'])&&!is_dir($user_configuration['testing']['documentation_folder']))
            throw new \Exception("Documentation folder path: " . $user_configuration['testing']['documentation_folder'] . " does not exist", 150);
        
        /** The full path to the test results file is set **/
        if (isset($user_configuration['testing']['test_results_file']))
            $user_configuration['testing']['test_results_file'] = $user_configuration['testing']['documentation_folder'] . DIRECTORY_SEPARATOR . $user_configuration['testing']['test_results_file'];
        
        /** The application domain name is set **/
        if (static::$configuration['is_browser_application'])
            $user_configuration['web_domain'] = (isset($_SERVER['HTTPS_HOST'])) ? "https://" . $_SERVER['HTTPS_HOST'] : "http://" . $_SERVER['HTTP_HOST'];
        else
            $user_configuration['web_domain'] = "N.A";
        
        /** The web path to the framework **/
        if (!isset($user_configuration['relative_web_domain']))
		    $user_configuration['relative_web_domain']="";
		
        $user_configuration['web_domain'] = $user_configuration['web_domain'] . $user_configuration['relative_web_domain'];
        
        /** The web path to the application **/
        $user_configuration['default_configuration']['web_application_url'] = $user_configuration['web_domain'] . "/applications/" . $user_configuration['application_name'] . "/index.php";
        /** The web path to the application's template folder **/
        $user_configuration['default_configuration']['web_template_path']   = $user_configuration['web_domain'] . "/templates";
        /** The web path to the application's vendors folder **/
        $user_configuration['default_configuration']['web_vendor_path']     = $user_configuration['web_domain'] . "/vendors";
        
        /** Application configuration is merged **/
        static::$configuration = array_replace_recursive(static::$configuration, $user_configuration);
       
        /** The php settings are configuration **/
        error_reporting(E_ALL);
        date_default_timezone_set('Europe/Berlin');
        if (static::$configuration['development_mode']) {
            ini_set('display_errors', true);
            ini_set('display_startup_errors', true);
        } else {
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', false);
        }               
    }    
}
?>