<?php

namespace FrameworkTest;

use \ApplicationConfigurationFramework\ApplicationConfiguration as ApplicationConfiguration;

/**
 * Application configuration class
 * 
 * Contains application configuration information
 * It provides configuration information and helper objects to the application
 * 
 * @category   ApplicationConfiguration
 * @package    FrameworkTest
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0
 * @link       N.A
 */
class Configuration extends ApplicationConfiguration
{
    /**
     * Used to initialize the application
     * 
     * Initializes objects needed by the application
     * Sets application configuration		 
     * 
     * @since 1.0.0
     
     */
    public function InitializeApplication()
    {        
        /** The folder name of the application **/
        $configuration['application_name']         = "frameworktest";
        /** The default application option. It is used if no option is given in url **/
        $configuration['default_option']           = "index";
        /** Test parameters **/
        /** Test mode indicates the application will be tested when its run **/
        $configuration['testing']['test_mode']     = true;
        /** Test type indicates the type of application testing. i.e functional or unit **/
        $configuration['testing']['test_type']     = 'unit';
        /** The list of classes to unit test **/
        $configuration['testing']['test_classes']  = array(
            "testing"
        );
        /** The files to include during testing **/
        $configuration['testing']['include_files'] = array(
            static::$configuration['vendor_folder_path'] . DIRECTORY_SEPARATOR . 'phpexcel' . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'IOFactory.php'
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
        $configuration['required_frameworks']['application']['class_name'] = 'FrameworkTest\FrameworkTest';
        $configuration['required_frameworks']['testing']['class_name']     = 'FrameworkTest\Testing';        

        /** The parent class constructor is class **/
        parent::Initialize($configuration);        
    }
}