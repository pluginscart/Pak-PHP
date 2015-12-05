<?php

namespace WebExample;

/**
 * Application configuration class
 * 
 * Contains application configuration information
 * It provides configuration information and helper objects to the application
 * 
 * @category   WebExample
 * @package    Configuration
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0
 * @since      1.0.0
 */
class Configuration extends \Framework\Configuration\Configuration
{
    /**
     * Used to set the user configuration
     * 
     * Defines the user configuration
	 * Updated the user configuration using the user defined application parameters	
     * Sets the user defined configuration as object property		 
     * 
     * @since 1.0.0
	 * @param array $parameters the application parameters given by the user	 	
     */
    public function __construct($parameters)
    {
    	/** The parent constructor is called */
    	parent::__construct($parameters);
		
        /** The folder name of the application **/
        $this->user_configuration['general']['application_name']         = "WebExample";
        /** The default application option. It is used if no option is given in url **/
        $this->user_configuration['general']['default_option']           = "index";
		
        /** Test parameters **/
        /** Test mode indicates the application will be tested when its run **/
        $this->user_configuration['testing']['test_mode']                = true;
        /** Test type indicates the type of application testing. i.e functional or unit **/
        $this->user_configuration['testing']['test_type']                = 'unit';
        /** The list of classes to unit test **/
        $this->user_configuration['testing']['test_classes']             = array(
																            "testing"
																        );
																		
        /** Used to indicate that function for generating template parameters should be automatically called */
	$this->user_configuration['general']['use_presentation']         = true;
		
	/** The database parameters are set if application is in production mode **/
	/** The database object is fetched **/
        $database_parameters = array(
            "host" => "localhost",
            "user" => "nadir",
            "password" => "kcbW5eFSCbPXbJGLHvUGG8T8",
            "database" => "dev_pakphp",
            "debug" => "1",
            "charset" => "utf8"
        );
					
	/** The application url mappings are defined */
        $this->user_configuration['general']['application_url_mappings'] = array(
        /** The index action is implemented by multiple templates */
            "index" => array(
                "templates" => array(
                    array(                        
                        "tag_name" => "root",
                        "template_file_name" => "base_page.html",
                        "object_name" => "application",
                        "function_name" => "HandleIndex"
                    ),
                    array(                        
                        "tag_name" => "body",
                        "template_file_name" => "list_page.html",
                        "object_name" => "application",
                        "function_name" => "HandleIndex"
                    )
                ),
                /** The function to call before functional testing */
                "testing" => array(
                    "object_name" => "testing",
                    "function_name" => "PrepareIndex",
                    "skip_testing" => false
                )
            ),
        /** The save action is implemented by a controller function */
			"save" => array(
                "controller" => array(
                    "object_name" => "application",
                    "function_name" => "HandleSave"
                ),
                 /** The function to call before functional testing */
                "testing" => array(
                	"object_name" => "",
                    "function_name" => "",
                	"skip_testing" => false
				)
            )
            			
        );		         			
        			
    $this->user_configuration['required_frameworks']['database']['parameters']        = $database_parameters;
    $this->user_configuration['required_frameworks']['application']['class_name']     = 'WebExample\WebExample';
    $this->user_configuration['required_frameworks']['testing']['class_name']         = 'WebExample\Testing';
    $this->user_configuration['required_frameworks']['presentation']['class_name']    = 'WebExample\Presentation';
	$this->user_configuration['required_frameworks']['authentication']['class_name']  = '\Framework\Utilities\Authentication';
	$this->user_configuration['required_frameworks']['filesystem']['class_name']      = '\Framework\Utilities\FileSystem';
	$this->user_configuration['required_frameworks']['encryption']['class_name']      = '\Framework\Utilities\Encryption';
	$this->user_configuration['required_frameworks']['caching']['class_name']         = '\Framework\Utilities\Caching';
	$this->user_configuration['required_frameworks']['caching']['parameters']         = array("table_prefix"=>"example_","db_link"=>"");
	$this->user_configuration['required_frameworks']['email']['class_name']           = '\Framework\Utilities\Email';
	$this->user_configuration['required_frameworks']['string']['class_name']          = '\Framework\Utilities\String';		
	$this->user_configuration['required_frameworks']['template_helper']['class_name'] = '\Framework\Utilities\Template';
	$this->user_configuration['required_frameworks']['template']['class_name']        = '\Framework\Templates\BasicSite\Presentation\BasicSiteTemplate';      
    }
}
