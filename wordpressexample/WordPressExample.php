<?php

namespace WordPressExample;

/**
 * This class implements the main plugin class
 * It contains functions that implement the filter, actions and hooks defined in the application configuration
 * 
 * It is used to implement the main functions of the plugin
 * 
 * @category   WordPressExample
 * @package    Application
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0
 * @since      1.0.0
 */
final class WordPressExample extends \Framework\Application\WordPress\Application
{
	/**
     * The single static instance
     */
    protected static $instance;
    /**
	 * The settings page is created
	 *
	 * This page is displayed for an option under settings menu
	 * It displays the settings page for the plugin
	 * 
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function DisplaySettingsPage(){
		$this->GetComponent("settings")->DisplaySettingsPage();		
	}
	
	/**
	 * The admin page is initialized
	 *
	 * It initializes the admin page
	 * It registers all the fields used by the settings page
	 * 
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function InitAdminPage(){
		$this->GetComponent("settings")->InitializeAdminPage();		
	}
	
	/**
	 * The dashboard setup function is called
	 * And the dashboard widget is registered
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function SetupDashboardWidget() {
		/** The options id is fetched */
	    $options_id                        = $this->GetComponent("application")->GetOptionsId("options");
	    /** The current plugin options */
	    $plugin_options                    = $this->GetComponent("application")->GetPluginOptions($options_id);
		/** The dashboard title */
		$dashboard_title                   = $plugin_options['title'];
		/** The dashboard widget is displayed */    			
		$this->AddDashboardWidget(
		                      'dashboard-widget',
		                      __($dashboard_title,$this->GetConfig("wordpress","plugin_text_domain")),
		                      array($this->GetComponent("dashboardwidget"),'DisplayDashboardWidget')
		);
	} 
		
}