<?php

namespace WordPressExample;

/**
 * Application configuration class
 * 
 * Contains application configuration information
 * It provides configuration information and helper objects to the application
 * 
 * @category   WordPressExample
 * @package    Configuration
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0
 * @since      1.0.0
 */
final class Configuration extends \Framework\Application\WordPress\Configuration
{
   /**
     * Used to set the user configuration
     * 
     * Defines the user configuration
	 * The user configuration is used to override the default configuration 
     * 
     * @since 1.0.0
	 * @param array $parameters the application parameters given by the user	 	
     */
    public function __construct($parameters)
    {
    	/** The user defined application configuration */
    	$this->user_configuration['general']['parameters']                                                     = $parameters;
    	/** The name of the plugin */
        $this->user_configuration['wordpress']['plugin_name']                                                  = "WordPress Example";    	
		/** The plugin version */
        $this->user_configuration['wordpress']['plugin_version']                                               = "1.0.0";
		    
        /** Test parameters */
        /** Test mode indicates the application will be tested when its run */
        $this->user_configuration['testing']['test_mode']                                                      = false;
        /** Test type indicates the type of application testing. i.e script, functional or unit */
        $this->user_configuration['testing']['test_type']                                                      = 'unit';
        /** The list of classes to unit test */
        $this->user_configuration['testing']['test_classes']                                                   = array(
																										            "testing"
																										        );		
		/** Used to indicate if application is being developed */
        $this->user_configuration['development_mode']                                                          = true;
		
        /** The required framework classes are specified */
        $this->user_configuration['required_frameworks']['errorhandler']['parameters']['email']                = array("email_address"=>"nadir@pakjddat.com","email_header"=>"Subject: Error occured in WordPress Example plugin. Please Check!.\nFrom: admin@pakjiddat.com\nContent-type: text/html; charset=iso-8859-1;\n");
        $this->user_configuration['required_frameworks']['errorhandler']['parameters']['display_error']        = ($this->user_configuration['development_mode'] || $this->user_configuration['testing']['test_mode']);
        $this->user_configuration['required_frameworks']['application']['class_name']                          = '\WordPressExample\WordPressExample';
		$this->user_configuration['required_frameworks']['settings']['class_name']                             = '\WordPressExample\Settings';
        $this->user_configuration['required_frameworks']['testing']['class_name']                              = '\WordPressExample\Testing';        		
		$this->user_configuration['required_frameworks']['filesystem']['class_name']                           = '\Framework\Utilities\FileSystem';
		$this->user_configuration['required_frameworks']['encryption']['class_name']                           = '\Framework\Utilities\Encryption';
		$this->user_configuration['required_frameworks']['string']['class_name']                               = '\Framework\Utilities\String';
		$this->user_configuration['required_frameworks']['template']['class_name']                             = '\Framework\Utilities\Template';		
		/** Used to indicate if application should use sessions */
	    $this->user_configuration['general']['enable_sessions']                                                = true;
			
		/** If the application is not in test mode, then the custom filters and action are registered */
		if (!$this->user_configuration['testing']['test_mode']) {					
		    /** Used to specify the WordPress settings menu */		
		    /** Used to indicate that a settings menu is required */
		    $this->user_configuration['wordpress']['use_settings']                                             = true;		
		    /** The page title of the settings option */
		    $this->user_configuration['wordpress']['settings_page_title']                                      = $this->user_configuration['wordpress']['plugin_name'];
		    /** The menu title of the settings option */
		    $this->user_configuration['wordpress']['settings_menu_title']                                      = $this->user_configuration['wordpress']['plugin_name'];
		    /** The minimum access rights for accessing the settings page */
		    $this->user_configuration['wordpress']['settings_menu_permissions']                                = 'manage_options';
		    /** The url of the settings page */
		    $this->user_configuration['wordpress']['settings_page_url']                                        = 'wordpress-example-settings-admin';
		    /** The callback used to create the settings page content */
		    $this->user_configuration['wordpress']['settings_page_content_callback']                           = array("application","DisplaySettingsPage");
		    /** The callback used to initialize the admin page. This callback can be used to register fields using the WordPress settings api */
		    $this->user_configuration['wordpress']['admin_init_callback']                                      = array("application","InitAdminPage");
		    /** The localization information for wpe-admin.js */		    
		    $admin_script_localization                                                                         = array(
			    "name"=>"wordpress-example-dashboard-widget",
			    "variable_name"=>"WPE_L10n",
			    "data"=>array(
					'title_alert' => __( "Please enter the dashboard title", "wordpress-example" ),
					'text_alert' => __( "Please enter the dashboard text", "wordpress-example" )					
				)		
		    );		   			
		    /** The WordPress admin javascript files are defined */
		    $this->user_configuration['wordpress']['admin_scripts']                                           = array(
		        array("name"=>"wpe-admin","file"=>"js/wpe-admin.js","dependencies"=>array("jquery"), "localization"=>""),
		        array("name"=>"wpe-dashboard-widget","file"=>"js/wpe-dashboard-widget.js","dependencies"=>array("jquery"), "localization"=>$admin_script_localization)
		    );
		    /** The WordPress admin css files are defined */
		    $this->user_configuration['wordpress']['admin_styles']                                            = array(
		        array("name"=>"wpe-admin","file"=>"css/wpe-admin.css","dependencies"=>"","media"=>"all")		        
		    );
		    /** The WordPress public javascript files are defined */
		    $this->user_configuration['wordpress']['public_scripts']                                          = array(array());
		    /** The WordPress public css files are defined */
		    $this->user_configuration['wordpress']['public_styles']                                           = array(array());
		    /** The custom wordpress actions are defined. for example ajax callbacks */
		    $this->user_configuration['wordpress']['custom_actions']                                          = array(		     
 		        /** Used to setup the WordPress example dashboard widget */   
			    array("name"=>"wpe_dashboard_setup","callback"=>array("application","SetupDashboardWidget")),
			    /** Ajax call for the Dashboard widget */
			    array("name"=>"wpe_ajax_dashboardwidget","callback"=>array("dashboardwidget","DashboardWidgetAjax"))			    			
		    );			       
	    }
        else {
			/** The custom wordpress filters are defined. for example xml-rpc functions */
		    $this->user_configuration['wordpress']['custom_filters']                                          = array(		     
		        /** Used to create custom posts and taxonomies */
			    array("name"=>"xmlrpc_methods","callback"=>array("testing","RegisterXmlRpcMethods"))			
		    );
			/** The custom wordpress actions are defined. for example ajax callbacks */
		    $this->user_configuration['wordpress']['custom_actions']                                          = array(		     
 		        /** Used to add custom post types to WordPress */   
			    array("name"=>"init","callback"=>array("application","AddCustomPostTypes"))			    			
		    );		
        }
    }
}