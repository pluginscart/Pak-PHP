<?php

namespace Framework\Application\WordPress;

/**
 * Base configuration class for wordpress applications
 * 
 * Singleton class
 * Abstract class. must be inherited by a child class
 * It uses the DefaultApplicationConfiguration class
 * The DefaultApplicationConfiguration class contains default configuration values
 * Initializes objects and sets configuration
 * 
 * @category   Framework
 * @package    WordPress
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0
 * @link       N.A
 */
abstract class Configuration extends \Framework\Configuration\Configuration
{   
	/**
     * Used to initialize the application
     * 
     * Initializes objects needed by the application
     * Sets application configuration
     * 
     * @since 1.0.0
	 * @throws object Exception an exception is thrown if the plugin name was not given in the user configuration
	 * @throws object Excepton an exception is thrown if the plugin version was not given in user configuration 
     */
    protected function Initialize()
    {
		/** The configuration object for the current object is set */
    	$this->SetConfigurationObject($this);        
        /** User configuration settings are merged with default configuration settings */
        $default_configuration = new DefaultConfiguration();
		/** The default configuration is merged with user configuration and the result is returned */
        $this->configuration   = $default_configuration->GetUpdatedConfiguration($this->user_configuration);		
        /** The error handler object is created */
        $this->InitializeObject("errorhandler");		
        /** All required classes are included */
        $this->IncludeRequiredClasses();
        /** Php Sessions are enabled if user requested sessions */
        $this->EnableSessions();
		
        /** The function to call after the plugin is loaded is registered. It loads the translation information */
		$this->GetComponent("application")->WP_AddAction( 'plugins_loaded', $this->GetComponent("application"), 'WP_LoadPluginTextDomain' );
		/** The css files for admin pages are registered */
		$this->GetComponent("application")->WP_AddAction( 'admin_enqueue_scripts', $this->GetComponent("application"), 'WP_AdminEnqueueStyles' );
		/** The javascript files for admin pages are registered */
		$this->GetComponent("application")->WP_AddAction( 'admin_enqueue_scripts', $this->GetComponent("application"), 'WP_AdminEnqueueScripts' );
		/** The dashboard setup function is called. If the user has specified dashboards in the application configuration, then this function will register the dashboards */
		$this->GetComponent("application")->WP_AddAction( 'wp_dashboard_setup', $this->GetComponent("application"), 'WP_DashboardSetupHooks' );
		/** The function that is run when the Admin Head Hook is called by WordPress */
		$this->GetComponent("application")->WP_AddAction( 'admin_head', $this->GetComponent("application"), 'WP_AdminHeadHooks' );	
		/** The javascript files for public pages are registered */
		$this->GetComponent("application")->WP_AddAction( 'wp_enqueue_scripts', $this->GetComponent("application"), 'WP_EnqueueScripts' );
		/** The css files for public pages are registered */
		$this->GetComponent("application")->WP_AddAction( 'wp_enqueue_scripts', $this->GetComponent("application"), 'WP_EnqueueStyles' );
		/** If the plugin should have a settings page and the admin user is logged in then the admin page is initialized and the settings page is created */
		if(is_admin()&&static::GetConfig('wordpress','use_settings')) {
		    $this->GetComponent("application")->WP_AddAction( 'admin_menu', $this->GetComponent("application"), 'WP_DisplaySettings' );
			$this->GetComponent("application")->WP_AddAction( 'admin_init', $this->GetComponent("application"), 'WP_InitAdmin' );
		}
		/** All the custom actions are registered */
		for($count=0; $count<count(static::GetConfig('wordpress','custom_actions')); $count++) {
		    /** The custom actions */
			$custom_actions                                                                                      = $this->GetConfig('wordpress','custom_actions');
			/** A custom action */
			$custom_action                                                                                       = $custom_actions[$count];
			/** The name of the custom action */
			$name                                                                                                = $custom_action['name'];
			/** The callback of the custom action */
			$callback                                                                                            = $custom_action['callback'];
			$custom_callback                                                                                     = array($this->GetComponent($callback[0]),$callback[1]);
			/** If the custom callback is not callable then an exception is thrown */
			if(!is_callable($custom_callback))throw new \Exception("Invalid custom callback function given. Details: ".var_export($callback, true));
			/** The custom action is registered */
			$this->GetComponent("application")->WP_AddAction(
			    $custom_action['name'],
				$this->GetComponent($callback[0]),
				$callback[1]
			);
		}
	}
}