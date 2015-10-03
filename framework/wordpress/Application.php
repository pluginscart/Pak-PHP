<?php

namespace Framework\WordPress;

/**
 * This class implements the base BrowserApplication class 
 * 
 * It contains functions that help in constructing the user interface of browser based applications
 * The class is abstract and must be inherited by the application user interface class
 * 
 * @category   Framework
 * @package    WordPress
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    Release: 1.0.0
 * @link       N.A
 */
abstract class Application
{
	/**
     * Used to display the given template contents to the browser		 
     * 
     * It echoes the template contents		 
     * 
     * @since 1.0.0
     * @param string $template_contents the contents of the template file to be displayed
     */
    public function DisplayTemplateContents($template_contents)
    {        
        echo $template_contents;       
    }
	    
    /**
     * Used to display response to json request
     * 
     * It echoes a json encoded response	 
     * 
     * @since 1.0.0
     * @param array $response the data to display as json		 
     */
    public function DisplayJsonResponse($response)
    {        
        echo json_encode($response);       
    }
	   
	/**
	 * Used to activate the plugin
	 *
	 * This function is called when the plugin is activated
	 *
	 * @since    1.0.0
	 */
	public static function WP_Activate() {

	}
	
	/**
	 * Used to deactivate the plugin
	 *
	 * This function is called when the plugin is deactivated
	 *
	 * @since    1.0.0
	 */
	public static function WP_Deactivate() {

	}
	
	/**
     * Add a new action to the collection to be registered with WordPress.
     *
     * @since      1.0.0
     * @param      string               $hook             the name of the WordPress action that is being registered
     * @param      object               $component        a reference to the instance of the object on which the action is defined
     * @param      string               $callback         the name of the function definition on the $component
     * @param      int      optional    $priority         the priority at which the function should be fired
     * @param      int      optional    $accepted_args    the number of arguments that should be passed to the $callback
     */
    public function WP_AddAction($hook, $component, $callback, $priority = 10, $accepted_args = 1)
    {
    	 /** The WordPress callback function is defined */
    	 $wordpress_callback=array($component,$callback);
    	 /** If the function is not callable then an exception is thrown */
         if (!is_callable($wordpress_callback))throw new \Exception("Function : " . $callback . " was not found");
         /** The wordpress configuration is fetched */
		 $wordpress_configuration=Configuration::GetConfig("wordpress");
		 $actions=$wordpress_configuration['actions'];         
		 /** The new action is added to the WordPress actions array */
         $actions = $this->WP_Add($actions, $hook, $component, $callback, $priority, $accepted_args);
		 /** The updated actions data is saved to the application configuration */
		 $wordpress_configuration['actions']=$actions;
		 Configuration::SetConfig("wordpress", "actions", $actions);
    }
    
    /**
     * Add a new filter to the collection to be registered with WordPress.
     *
     * @since      1.0.0
     * @param      string               $hook             the name of the WordPress filter that is being registered
     * @param      object               $component        a reference to the instance of the object on which the filter is defined
     * @param      string               $callback         the name of the function definition on the $component
     * @param      int      optional    $priority         the priority at which the function should be fired
     * @param      int      optional    $accepted_args    the number of arguments that should be passed to the $callback
     */
    public function WP_AddFilter($hook, $component, $callback, $priority = 10, $accepted_args = 1)
    {
    	/** The WordPress callback function is defined */
    	 $wordpress_callback=array($component,$callback);
    	 /** If the function is not callable then an exception is thrown */
         if (!is_callable($wordpress_callback))throw new \Exception("Function : " . $callback . " was not found");

		/** The wordpress configuration is fetched */
		$wordpress_configuration=Configuration::GetConfig("wordpress");
		$filters=$wordpress_configuration['filters'];         
		/** The new filter is added to the WordPress filters array */
        $filters = $this->Add($filters, $hook, $component, $callback, $priority, $accepted_args);
		/** The updated actions data is saved to the application configuration */
		$wordpress_configuration['filters']=$filters;
		Configuration::SetConfig("wordpress", "filters", $filters);
    }
    
    /**
     * A utility function that is used to register the actions and hooks into a single
     * collection.
     *
     * @since    1.0.0	 
     * @param    array                $hooks            the collection of hooks that is being registered (that is, actions or filters)
     * @param    string               $hook             the name of the WordPress filter that is being registered
     * @param    object               $component        a reference to the instance of the object on which the filter is defined
     * @param    string               $callback         the name of the function definition on the $component
     * @param    int      optional    $priority         the priority at which the function should be fired
     * @param    int      optional    $accepted_args    the number of arguments that should be passed to the $callback
     * 
     * @return   type                                   the collection of actions and filters registered with WordPress
     */
    private function WP_Add($hooks, $hook, $component, $callback, $priority, $accepted_args)
    {        
        $hooks[] = array(
            'hook' => $hook,
            'component' => $component,
            'callback' => $callback,
            'priority' => $priority,
            'accepted_args' => $accepted_args
        );
        
        return $hooks;        
    }
	
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function WP_LoadPluginTextDomain() {
		
		/** Used to load the plugin's text domain */
        load_plugin_textdomain(
            Configuration::GetConfig("wordpress","plugin_text_domain"),
            false,
            Configuration::GetConfig("wordpress","plugin_language_path")
		);		
	}  
	
	/**
	 * The css files for admin pages are registered
	 *
	 * @since    1.0.0
	 */
	public function WP_AdminEnqueueStyles() {
		$this->WP_Enqueue("admin_styles",false);
	} 
	
	/**
	 * The javascript files for admin pages are registered
	 *
	 * @since    1.0.0
	 */
	public function WP_AdminEnqueueScripts() {
		$this->WP_Enqueue("admin_scripts",true);
	} 
	
	/**
	 * The dashboard setup function is called. If the user has specified dashboards in the application configuration, then this function will register the dashboards
	 *
	 * @since    1.0.0
	 */
	public function WP_DashboardSetupHooks() {
		
	} 
	
	/**
	 * The function that is run when the Admin Head Hook is called by WordPress
	 *
	 * @since    1.0.0
	 */
	public function WP_AdminHeadHooks() {
					
	}
	
	/**
	 * The javascript files for public pages are registered
	 *
	 * @since    1.0.0
	 */
	public function WP_EnqueueScripts() {
		$this->WP_Enqueue("public_scripts",true);
	}
	
	/**
	 * The css files for public pages are registered
	 *
	 * @since    1.0.0
	 */
	public function WP_EnqueueStyles() {			
		$this->WP_Enqueue("public_styles",false);
	}
	
	/**
	 * The WordPress settings menu is created
	 *
	 * @since    1.0.0
	 */
	public function WP_DisplaySettings() {
		/** The wordpress configuration is fetched */
		$wordpress_configuration=Configuration::GetConfig("wordpress");
		/** The object used to set the settings page content is fetched */
		$object_name=$wordpress_configuration['settings_page_content_callback'][0];
		$object=Configuration::GetComponent($object_name);
		$wordpress_configuration['settings_page_content_callback'][0]=$object;
		/** If the settings page callback is not callable then an exception is thrown */
		if(!is_callable($wordpress_configuration['settings_page_content_callback']))throw new \Exception("Invalid callback function defined for settings page");				
		/** The settings menu is created */			
		\add_options_page(
				            $wordpress_configuration['settings_page_title'], 
				            $wordpress_configuration['settings_menu_title'], 
				            $wordpress_configuration['settings_menu_permissions'], 
				            $wordpress_configuration["settings_page_url"], 
					        $wordpress_configuration['settings_page_content_callback']
		);
	}
	
	/**
	 * The WordPress admin page is initialized
	 *
	 * @since    1.0.0
	 */
	public function WP_InitAdmin() {		
		/** The wordpress configuration is fetched */
		$wordpress_configuration=Configuration::GetConfig("wordpress");
		/** The object used to set the settings page content is fetched */
		$object_name=$wordpress_configuration['admin_init_callback'][0];
		$object=Configuration::GetComponent($object_name);
		$wordpress_configuration['admin_init_callback'][0]=$object;		
		/** If the init admin page callback is not callable then an exception is thrown */
		if(!is_callable($wordpress_configuration['admin_init_callback']))throw new \Exception("Invalid callback function defined for initializing admin page");
		/** If the init admin page callback is callable then it is called */		
		call_user_func($wordpress_configuration['admin_init_callback']);
	}
	
	/**
	 * The given wordpress scripts/styles are enqueued
	 *
	 * @since    1.0.0
	 * @param $configuration_name the name of the application configuration that contains the scripts/styles to enqueue
	 * @param $is_script used to indicate if the given application configuration is a script or style
	 */
	public function WP_Enqueue($configuration_name,$is_script) {
		/** Used to indicate if the localization script is registered. It must be registered after wp_enqueue_script */
		$is_registered = false;			
		/** The wordpress configuration is fetched */
		$wordpress_configuration=Configuration::GetConfig("wordpress");
		for($count=0;$count<count($wordpress_configuration[$configuration_name]);$count++) {
            	 
            if(!$is_script)wp_enqueue_style( 
            $wordpress_configuration[$configuration_name][$count]['name'], 
            Configuration::GetConfig("wordpress","plugin_url") . "/". $wordpress_configuration[$configuration_name][$count]['file'],
            $wordpress_configuration[$configuration_name][$count]['dependencies'],
            Configuration::GetConfig("wordpress","plugin_version"),
            $wordpress_configuration[$configuration_name][$count]['media'] );
			
            else wp_enqueue_script(
            Configuration::GetConfig("wordpress","plugin_url"),
            Configuration::GetConfig("wordpress","plugin_url") ."/". $wordpress_configuration[$configuration_name][$count]['file'],
            $wordpress_configuration[$configuration_name][$count]['dependencies'],
            Configuration::GetConfig("wordpress","plugin_version"), false );
            
			/** If the localization data is specified for the script then it is loaded to WordPress using wp_localize_script */
			if(!$is_registered&&$is_script&&isset($wordpress_configuration[$configuration_name."_localization"])) {			
		        wp_localize_script($wordpress_configuration[$configuration_name."_localization"]["name"],
                                   $wordpress_configuration[$configuration_name."_localization"]["variable_name"],
		                           $wordpress_configuration[$configuration_name."_localization"]["data"]
		        );
				$is_registered = true;
	    	}
        }
	}
	
	/**
	 * The plugin options are fetched
	 *
	 * It fetches the plugin options
	 * 
	 * @since    1.0.0
	 * @param string $option_id id of the option to fetch
	 * 
	 * @return array $options the plugin options
	 */
	public function GetPluginOptions($option_id){
		
		/** The current plugin options are fetched from WordPress */
		$options = get_option( $option_id );
		/** If the options is a json encoded string then it is decoded */
		if(Configuration::GetComponent("string")->IsJson($options))$options = json_decode($options,true);
		
		return $options;		
	}
	
	/**
	 * Used to add a dashboard widget
	 *
	 * It adds the given dashboard widget
	 * 
	 * @since    1.0.0
	 * @param string $option_id id of the option to fetch
	 * 
	 * @return array $options the plugin options
	 */
	public function AddDashboardWidget($widget_id, $widget_title, $widget_callback){		
		wp_add_dashboard_widget($widget_id, $widget_title,$widget_callback);		    
	}
	
	/**
	 * The options id is fetched
	 *
	 * It fetches the options id
	 * The options id is the option name with plugin prefix as the prefix and user id as the suffix
	 * 
	 * @since    1.0.0	 
	 * @param string $option_name the name of the option
	 * 
	 * @return string $option_id the id of the option
	 */
	public function GetOptionsId($option_name){
		
		/** The user id of the logged in user */
    	$user_id=get_current_user_id();
    	/** The wordpress configuration is fetched */
		$wordpress_configuration=Configuration::GetConfig("wordpress");
		/** The plugin settings id */
		$options_id=$wordpress_configuration['plugin_prefix'].'_'.$option_name.'_'.$user_id;						
		
		return $options_id;
	}
	
	/**
	 * The plugin options are saved
	 *
	 * It saves the plugin options
	 * 
	 * @since    1.0.0
	 * @param array $options the plugin options
	 * @param string $option_id id of the option to save
	 */
	public function SavePluginOptions($options,$option_id) {
		
		/** If the options is an array then it is json encoded */
		if(is_array($options))$options =  $options = json_encode($options);
		/** The options values are saved */		
		update_option($option_id,$options);	
	}
	
    /**
     * Register the filters and actions with WordPress.
     *
     * @since    1.0.0
     */
    public function HandleRequest()
    {
    	/** The wordpress configuration is fetched */
		$wordpress_configuration=Configuration::GetConfig("wordpress");
		
    	/** Used to register the function that will be called when the plugin is activated */
		\register_activation_hook( Configuration::GetConfig("wordpress","plugin_file_path"), array( 'Application', 'WP_Activate' ) );
		/** Used to register the function that will be called when the plugin is deactivated */
		\register_activation_hook( Configuration::GetConfig("wordpress","plugin_file_path"), array( 'Application', 'WP_Deactivate' ) );
				
		$filters=$wordpress_configuration['filters'];         
		$actions=$wordpress_configuration['actions'];
		/** The filters are registered with WordPress */			
        foreach ($filters as $hook) {
            \add_filter($hook['hook'], array($hook['component'],$hook['callback']), $hook['priority'], $hook['accepted_args']);
        }
        /** The actions are registered with WordPress */
        foreach ($actions as $hook) {
            \add_filter($hook['hook'], array($hook['component'],$hook['callback']), $hook['priority'], $hook['accepted_args']);
        }
    }
}