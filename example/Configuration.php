<?php

namespace Example;

/**
 * Application configuration class
 * 
 * Contains application configuration information
 * It provides configuration information and helper objects to the application
 * 
 * @category   WebApplication
 * @package    Example
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0
 * @link       N.A
 */
class Configuration extends \Framework\WebApplication\Configuration
{
    /**
     * Used to initialize the application
     * 
     * Initializes objects needed by the application
     * Sets application configuration		 
     * 
     * @since 1.0.0
     * @param array $argv the command line parameters given by the user	
     */
    public function InitializeApplication($argv)
    {        
        /** The folder name of the application **/
        $configuration['general']['application_name']         = "Example";
        /** The default application option. It is used if no option is given in url **/
        $configuration['general']['default_option']           = "index";
        /** Test parameters **/
        /** Test mode indicates the application will be tested when its run **/
        $configuration['testing']['test_mode']     = true;
        /** Test type indicates the type of application testing. i.e functional or unit **/
        $configuration['testing']['test_type']     = 'unit';
        /** The list of classes to unit test **/
        $configuration['testing']['test_classes']  = array(
            "testing"
        );
        
		/** The database parameters are set if application is in production mode **/
		/** The database object is fetched **/
        $database_parameters = array(
            "host" => "localhost",
            "user" => "nadir",
            "password" => "kcbW5eFSCbPXbJGLHvUGG8T8",
            "database" => "dev_pakphp",
            "debug" => "1"
        );
								
        $configuration['required_frameworks']['database']['parameters']    = $database_parameters;
        $configuration['required_frameworks']['application']['class_name'] = 'Example\Example';
        $configuration['required_frameworks']['testing']['class_name']     = 'Example\Testing';        
		$configuration['required_frameworks']['authentication']['class_name'] = '\Framework\Utilities\Authentication';
		$configuration['required_frameworks']['filesystem']['class_name'] = '\Framework\Utilities\FileSystem';
		$configuration['required_frameworks']['encryption']['class_name'] = '\Framework\Utilities\Encryption';
		$configuration['required_frameworks']['caching']['class_name'] = '\Framework\Utilities\Caching';
		$configuration['required_frameworks']['caching']['parameters'] = array("table_prefix"=>"example_","db_link"=>"");
		$configuration['required_frameworks']['email']['class_name'] = '\Framework\Utilities\Email';
		$configuration['required_frameworks']['string']['class_name'] = '\Framework\Utilities\String';		
		$configuration['required_frameworks']['template']['class_name'] = '\Framework\Utilities\Template';	
		
        /** The parent class constructor is class **/
        parent::Initialize($argv, $configuration);        
    }
}