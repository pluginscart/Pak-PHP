<?php

namespace Framework\WordPress;

/**
 * Base configuration class for wordpress applications
 * 
 * Singleton class. must be inherited by a child class
 * It extends the DefaultApplicationConfiguration class
 * The Default_ApplicationConfiguration class contains default configuration values
 * Initializes objects and sets configuration
 * 
 * @category   Framework
 * @package    WordPress
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
     * @param array $configuration an array containing application configuration information		 
     */
    protected function Initialize($argv,$configuration)
    {
    	/** If the plugin name is not given in application configuration then an exception is thrown */
        if(!isset($configuration['wordpress']['plugin_name']))throw new \Exception("Plugin name was not given in application configuration");
		/** If the plugin version is not given in application configuration then an exception is thrown */
        if(!isset($configuration['wordpress']['plugin_version']))throw new \Exception("Plugin version was not given in application configuration");
	
		/** If the php session is not started then it is started */
		if ( version_compare(phpversion(), '5.4.0', '>=') )$is_session_started=(session_status() === PHP_SESSION_ACTIVE) ? true : false;
		$is_session_started=(session_id() === '') ? false : true;
		/** If the session is not started then it is started */	
		if(!$is_session_started)session_start();
			 		
		/** If the application is being tested then the application class name is set to the test class name */
		if($configuration['testing']['test_mode'])
		$configuration['required_frameworks']['application']['class_name']=$configuration['required_frameworks']['testing']['class_name'];
		
    	/** The application name is set to the plugin name */
    	$configuration['general']['application_name']=$configuration['wordpress']['plugin_name'];
		/** The default option is set to index */
    	$configuration['general']['default_option']='index';
		/** If not set then the application folder name is determined from the plugin name */
		if(!isset($configuration['application_folder']))$configuration['application_folder']=strtolower(str_replace(" ","",$configuration['wordpress']['plugin_name']));
		
		/** The parent Initialize function is called */
		parent::Initialize($argv,$configuration);
		/* WordPress configuration settings are fetched */
        $wordpress_configuration=DefaultConfiguration::GetConfiguration($argv,static::$configuration);
        /** Application configuration is merged with wordpress configuration settings */
        static::$configuration = array_replace_recursive(static::$configuration, $wordpress_configuration);																
		
		/** The function to call after the plugin is loaded is registered. It loads the translation information */
		parent::GetComponent("application")->WP_AddAction( 'plugins_loaded', parent::GetComponent("application"), 'WP_LoadPluginTextDomain' );
		/** The css files for admin pages are registered */
		parent::GetComponent("application")->WP_AddAction( 'admin_enqueue_scripts', parent::GetComponent("application"), 'WP_AdminEnqueueStyles' );
		/** The javascript files for admin pages are registered */
		parent::GetComponent("application")->WP_AddAction( 'admin_enqueue_scripts', parent::GetComponent("application"), 'WP_AdminEnqueueScripts' );
		/** The dashboard setup function is called. If the user has specified dashboards in the application configuration, then this function will register the dashboards */
		parent::GetComponent("application")->WP_AddAction( 'wp_dashboard_setup', parent::GetComponent("application"), 'WP_DashboardSetupHooks' );
		/** The function that is run when the Admin Head Hook is called by WordPress */
		parent::GetComponent("application")->WP_AddAction( 'admin_head', parent::GetComponent("application"), 'WP_AdminHeadHooks' );	
		/** The javascript files for public pages are registered */
		parent::GetComponent("application")->WP_AddAction( 'wp_enqueue_scripts', parent::GetComponent("application"), 'WP_EnqueueScripts' );
		/** The css files for public pages are registered */
		parent::GetComponent("application")->WP_AddAction( 'wp_enqueue_scripts', parent::GetComponent("application"), 'WP_EnqueueStyles' );
		/** If the plugin should have a settings page and the admin user is logged in then the admin page is initialized and the settings page is created */
		if(is_admin()&&static::$configuration['wordpress']['use_settings'])
			{
				parent::GetComponent("application")->WP_AddAction( 'admin_menu', parent::GetComponent("application"), 'WP_DisplaySettings' );
				parent::GetComponent("application")->WP_AddAction( 'admin_init', parent::GetComponent("application"), 'WP_InitAdmin' );
			}
		/** All the custom actions are registered */
		for($count=0;$count<count($configuration['wordpress']['custom_actions']);$count++)
			{
				/** A custom action */
				$custom_action   = $configuration['wordpress']['custom_actions'][$count];
				/** The name of the custom action */
				$name            = $custom_action['name'];
				/** The callback of the custom action */
				$callback        = $custom_action['callback'];
				$custom_callback        = array(parent::GetComponent($callback[0]),$callback[1]);
				/** If the custom callback is not callable then an exception is thrown */
				if(!is_callable($custom_callback))throw new \Exception("Invalid custom callback function given");
				/** The custom action is registered */
				parent::GetComponent("application")->WP_AddAction(
				    $custom_action['name'],
				    parent::GetComponent($callback[0]),
				    $callback[1]
				);
			}
	}
}