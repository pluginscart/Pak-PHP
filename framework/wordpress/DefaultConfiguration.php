<?php

namespace Framework\WordPress;

/**
 * Default application configuration class
 * 
 * Abstract class. must be extended by a child class
 * It provides default application configuration
 * 
 * @category   Framework
 * @package    WordPress
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0
 * @link       N.A
 */
abstract class DefaultConfiguration
{    
    /**
     * Used to get default application configuration data
     * The configuration data can be overridden by child classes
     * 		 
     * It returns an array containing application configuration data
     * 
     * @since 1.0.0
	 * @param array $argv the command line parameters given by the user
	 * @param array $user_configuration user defined application configuration     
	 * 
	 * @return array $configuration the application configuration information
     */
    public static function GetConfiguration($argv,$configuration)
    {
    	/** If the user did not specify settings configuration then the settings configuration is initialized */
    	if(!isset($configuration['wordpress']['use_settings'])){
    	    /** Used to indicate that a settings menu is required */
		    $configuration['wordpress']['use_settings'] = false;
		    /** The page title of the settings option */
			$configuration['wordpress']['settings_page_title'] = '';
			/** The menu title of the settings option */
			$configuration['wordpress']['settings_menu_title'] = '';
		    /** The minimum access rights for accessing the settings page */
		    $configuration['wordpress']['settings_menu_permissions'] = 'manage_options';
			/** The callback used to create the settings page content */
			$configuration['wordpress']['settings_page_content_callback'] = array();
			/** The callback used to initialize the admin page. This callback can be used to register fields using the WordPress settings api */
			$configuration['wordpress']['admin_init_callback'] = array();
		}
		
		/** The name of the WordPress plugin folder is determined */
		$temp_arr=explode(DIRECTORY_SEPARATOR,$configuration['path']['base_path']);
		$plugin_folder_name=$temp_arr[count($temp_arr)-1];
				
		/** The wordpress actions and filters configuration is initialized */
    	$configuration['wordpress']['actions']=$configuration['wordpress']['filters']=array();
				
		/** If the plugin text domain was not set then it is initialized */
        if (!isset($configuration['wordpress']['plugin_text_domain']))$configuration['wordpress']['plugin_text_domain']='';
		
		/** If the application is being tested then the application class name is set to the testing class name and the testing class name is removed from the list */
		if($configuration['testing']['test_mode'])$configuration['required_frameworks']['application']['class_name']=$configuration['required_frameworks']['testing']['class_name'];
							    
		/** Used to indicate if api response will be encrypted */
        $configuration['wordpress']['encrypted_api'] = false;
        /** The path to the plugin index.php file */
        $configuration['wordpress']['plugin_file_path'] = $configuration['path']['base_path'].DIRECTORY_SEPARATOR."index.php";
		/** The plugin folder name */
        $configuration['wordpress']['plugin_folder_name'] = $plugin_folder_name;
		/** The plugin url. It is used to create css and javascript file urls */
        $configuration['wordpress']['plugin_url'] = $configuration['path']['application_folder_url'];		
		/** The path to the plugin folder */
        $configuration['wordpress']['plugin_folder_path'] = $configuration['path']['base_path'];
		/** The path to the plugin template folder */		
        $configuration['wordpress']['plugin_template_path'] = $configuration['path']['base_path'].DIRECTORY_SEPARATOR.$configuration['path']['application_folder'].DIRECTORY_SEPARATOR."templates";
		/** The path to the plugin language folder */		
        $configuration['wordpress']['plugin_language_path'] = $configuration['path']['application_folder'].DIRECTORY_SEPARATOR."language".DIRECTORY_SEPARATOR;
		
		/** If the plugin text domain was not set then it is derived from the plugin name */		
        if ($configuration['wordpress']['plugin_text_domain']=='')$configuration['wordpress']['plugin_text_domain']=strtolower(str_replace(" ","-",$configuration['wordpress']['plugin_name']));
		
		/** If the plugin prefix is not specified by the user then it is calculated. It is used to create element ids. e.g submit button id */
		if(!isset($configuration['wordpress']['plugin_prefix']))
			{
        		$temp_arr=explode(" ",strtolower($configuration['wordpress']['plugin_name']));
				$configuration['wordpress']['plugin_prefix']="";
				for($count=0;$count<count($temp_arr);$count++)$configuration['wordpress']['plugin_prefix'].=substr($temp_arr[$count],0,1);
			}
			
        return $configuration;        
    }    
}
?>