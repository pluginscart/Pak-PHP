<?php

namespace IslamCompanion;

/**
 * This class implements the main plugin class
 * It contains functions that implement the filter, actions and hooks defined in the application configuration
 * 
 * It is used to implement the main functions of the plugin
 * 
 * @category   IslamCompanion
 * @package    IslamCompanion
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    2.0.0
 * @link       N.A
 */
final class IslamCompanion extends \Framework\Application\WordPress\Application
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
	 * @since    2.0.0
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
	 * @since    2.0.0
	 */
	public function InitAdminPage(){
		$this->GetComponent("settings")->InitializeAdminPage();		
	}
	
	/**
	 * The dashboard setup function is called
	 * The Holy Quran dashboard widget is registered
	 *
	 * @since    2.0.0
	 */
	public function SetupHolyQuranWidget() {		
		$this->AddDashboardWidget(
		                      'holy-quran-dashboard-widget',
		                      __('Holy Quran',$this->GetConfig("wordpress","plugin_text_domain")),
		                      array($this->GetComponent("holyqurandashboardwidget"),'DisplayDashboardWidget')
		);
	} 
		
}