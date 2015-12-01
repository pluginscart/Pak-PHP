<?php

namespace Framework\Application\WordPress;

use \Framework\Object\WordPressDataObject as WordPressDataObject;

/**
 * This class implements the base BrowserApplication class 
 * 
 * It contains functions that help in constructing the user interface of browser based applications
 * The class should be inherited by the application user interface class
 * 
 * @category   Framework
 * @package    WordPress
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    Release: 1.0.1
 * @link       N.A
 */
class Application extends \Framework\Application\Application
{   
	/**
	 * Used to activate the plugin
	 *
	 * This function is called when the plugin is activated
	 *
	 * @since 1.0.0
	 */
	public static function WP_Activate()
	{

	}
	
	/**
	 * Used to deactivate the plugin
	 *
	 * This function is called when the plugin is deactivated
	 *
	 * @since 1.0.0
	 */
	public static function WP_Deactivate()
	{

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
    	 $wordpress_callback                 = array($component,$callback);
    	 /** If the function is not callable then an exception is thrown */
         if (!is_callable($wordpress_callback))throw new \Exception("Function : " . $callback . " was not found");
         /** The wordpress configuration is fetched */
		 $wordpress_configuration            = $this->GetConfig("wordpress");
		 $actions                            = $wordpress_configuration['actions'];         
		 /** The new action is added to the WordPress actions array */
         $actions                            = $this->WP_Add($actions, $hook, $component, $callback, $priority, $accepted_args);
		 /** The updated actions data is saved to the application configuration */		 
		 $this->SetConfig("wordpress", "actions", $actions);
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
    	 $wordpress_callback                  = array($component,$callback);
    	 /** If the function is not callable then an exception is thrown */
         if (!is_callable($wordpress_callback))
             throw new \Exception("Function : " . $callback . " was not found");

		/** The wordpress configuration is fetched */
		$wordpress_configuration              = $this->GetConfig("wordpress");
		$filters                              = $wordpress_configuration['filters'];         
		/** The new filter is added to the WordPress filters array */
        $filters                              = $this->WP_Add($filters, $hook, $component, $callback, $priority, $accepted_args);
		/** The updated actions data is saved to the application configuration */
		$this->SetConfig("wordpress", "filters", $filters);
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
	 * @since 1.0.0
	 */
	public function WP_LoadPluginTextDomain()
	{	
		/** Used to load the plugin's text domain */
        load_plugin_textdomain(
            $this->GetConfig("wordpress","plugin_text_domain"),
            false,
            $this->GetConfig("wordpress","plugin_language_path")
		);		
	}  
	
	/**
	 * The css files for admin pages are registered
	 *
	 * @since 1.0.0
	 */
	public function WP_AdminEnqueueStyles()
	{
		$this->WP_Enqueue("admin_styles",false);
	} 
	
	/**
	 * The javascript files for admin pages are registered
	 *
	 * @since 1.0.0
	 */
	public function WP_AdminEnqueueScripts()
	{
		$this->WP_Enqueue("admin_scripts",true);
	}
	
	/**
	 * The javascript files for public pages are registered
	 *
	 * @since 1.0.0
	 */
	public function WP_EnqueueScripts()
	{
		$this->WP_Enqueue("public_scripts",true);
	}
	
	/**
	 * The css files for public pages are registered
	 *
	 * @since 1.0.0
	 */
	public function WP_EnqueueStyles()
	{			
		$this->WP_Enqueue("public_styles",false);
	}
	
	/**
	 * The WordPress settings menu is created
	 *
	 * @since 1.0.0
	 */
	public function WP_DisplaySettings()
	{
		/** The wordpress configuration is fetched */
		$wordpress_configuration                                       = $this->GetConfig("wordpress");
		/** The object used to set the settings page content is fetched */
		$object_name                                                   = $wordpress_configuration['settings_page_content_callback'][0];
		$object                                                        = $this->GetComponent($object_name);
		$wordpress_configuration['settings_page_content_callback'][0]  = $object;
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
	 * @since 1.0.0
	 */
	public function WP_InitAdmin()
	{		
		/** The wordpress configuration is fetched */
		$wordpress_configuration                                        = $this->GetConfig("wordpress");
		/** The object used to set the settings page content is fetched */
		$object_name                                                    = $wordpress_configuration['admin_init_callback'][0];
		$object                                                         = $this->GetComponent($object_name);
		$wordpress_configuration['admin_init_callback'][0]              = $object;		
		/** If the init admin page callback is not callable then an exception is thrown */
		if(!is_callable($wordpress_configuration['admin_init_callback']))
		    throw new \Exception("Invalid callback function defined for initializing admin page");
		/** If the init admin page callback is callable then it is called */		
		call_user_func($wordpress_configuration['admin_init_callback']);
	}
	
	/**
	 * The given wordpress scripts/styles are enqueued
	 *
	 * @since 1.0.0
	 * @param $configuration_name the name of the application configuration that contains the scripts/styles to enqueue
	 * @param $is_script used to indicate if the given application configuration is a script or style
	 */
	public function WP_Enqueue($configuration_name,$is_script)
	{		
		/** The wordpress configuration is fetched */
		$wordpress_configuration=$this->GetConfig("wordpress");
		for($count = 0; $count < count($wordpress_configuration[$configuration_name]); $count++) {
            	 
            if(!$is_script)wp_enqueue_style( 
            $wordpress_configuration[$configuration_name][$count]['name'], 
            $this->GetConfig("wordpress","plugin_url") . "/". $wordpress_configuration[$configuration_name][$count]['file'],
            $wordpress_configuration[$configuration_name][$count]['dependencies'],
            $this->GetConfig("wordpress","plugin_version"),
            $wordpress_configuration[$configuration_name][$count]['media'] );
			
            else wp_enqueue_script(
            $wordpress_configuration[$configuration_name][$count]['name'],
            $this->GetConfig("wordpress","plugin_url") ."/". $wordpress_configuration[$configuration_name][$count]['file'],
            $wordpress_configuration[$configuration_name][$count]['dependencies'],
            $this->GetConfig("wordpress","plugin_version"), false );
            
			/** If the localization data is specified for the script then it is loaded to WordPress using wp_localize_script */
			if(isset($wordpress_configuration[$configuration_name][$count]['localization'])) {				
		        wp_localize_script(
		        	    $wordpress_configuration[$configuration_name][$count]['localization']["name"],
                        $wordpress_configuration[$configuration_name][$count]['localization']["variable_name"],
		                $wordpress_configuration[$configuration_name][$count]['localization']["data"]
		        );				
	    	}
        }
	}
	
	/**
	 * The plugin options are fetched
	 *
	 * It fetches the plugin options
	 * 
	 * @since 1.0.0
	 * @param string $option_id id of the option to fetch
	 * 
	 * @return array $options the plugin options
	 */
	public function GetPluginOptions($option_id)
	{		
		/** The current plugin options are fetched from WordPress */
		$options                  = get_option( $option_id );
		/** If the options is a json encoded string then it is decoded */
		if($this->GetComponent("string")->IsJson($options))
		    $options              = json_decode($options,true);
		
		return $options;		
	}
	
	/**
	 * Used to add a dashboard widget
	 *
	 * It adds the given dashboard widget
	 * 
	 * @since 1.0.0
	 * @param string $option_id id of the option to fetch
	 * 
	 * @return array $options the plugin options
	 */
	public function AddDashboardWidget($widget_id, $widget_title, $widget_callback)
	{		
		wp_add_dashboard_widget($widget_id, $widget_title,$widget_callback);		    
	}
	
	/**
	 * The options id is fetched
	 *
	 * It fetches the options id
	 * The options id is the option name with plugin prefix as the prefix and user id as the suffix
	 * 
	 * @since 1.0.0	 
	 * @param string $option_name the name of the option
	 * 
	 * @return string $option_id the id of the option
	 */
	public function GetOptionsId($option_name)
	{
		
		/** The user id of the logged in user */
    	$user_id                    = get_current_user_id();
    	/** The wordpress configuration is fetched */
		$wordpress_configuration    = $this->GetConfig("wordpress");
		/** The plugin settings id */
		$options_id                 = $wordpress_configuration['plugin_prefix'].'_'.$option_name.'_'.$user_id;						
		
		return $options_id;
	}
	
	/**
	 * Used to add a new custom taxonomy type
	 *
	 * It adds a new custom taxonomy type using the given parameters
	 * If some parameters are not given. e.g the labels and args parameters are not given
	 * Then they are auto generated
	 * The user given parameters are merged with the default parameters
	 * 
	 * @since 1.0.0
	 * @param string $name the full name of the custom taxonomy
	 * @param string $singular_name the singular name for the custom taxonomy
	 * @param string $post_type the post type or custom post type that will be used as object type of the taxonomy
	 * @param array $labels the labels for the new custom taxonomy
	 * @param array $args the parameters for the new custom taxonomy
	 */
	public function AddNewCustomTaxonomy($name,$singular_name,$post_type,$labels=array(),$args=array())
	{		
		/** The post type is converted to lower case. spaces are replaced with - */
		$post_type                     = str_replace(" ","-",strtolower($post_type));
		/** The default labels for new custom taxonomy */		
	    $default_labels                = array(
												'name'                       => _x($name,$singular_name),
												'singular_name'              => _x($singular_name,$singular_name),
												'search_items'               => __('Search '.$singular_name),
												'popular_items'              => __('Popular '.$singular_name),
												'all_items'                  => __('All '.$singular_name),
												'parent_item'                => null,
												'parent_item_colon'          => null,
												'edit_item'                  => __('Edit '.$singular_name),
												'update_item'                => __('Update '.$singular_name),
												'add_new_item'               => __('Add New '.$singular_name),
												'new_item_name'              => __('New '.$singular_name.' Name'),
												'separate_items_with_commas' => __('Separate '.$name.' with commas' ),
												'add_or_remove_items'        => __('Add or remove '.$name),
												'choose_from_most_used'      => __('Choose from the most used '.$name),
												'not_found'                  => __('No '.$name.' found.'),
												'menu_name'                  => __($name),
		);
		/** The default arguments for the new custom taxonomy */
		$default_args                  = array(
												'hierarchical'          => false,
												'labels'                => $labels,
												'show_ui'               => true,
												'show_admin_column'     => true,
												'update_count_callback' => '_update_post_term_count',
												'query_var'             => true,
												'rewrite'               => array( 'slug' => strtolower($singular_name)),
		);
		/** The default labels are merged with user given labels */
		$default_labels                = array_merge($default_labels,$labels);
		/** The default arguments are merged with user given arguments */
		$default_args                  = array_merge($default_args,$args);
		/** The labels are added to the arguments */
		$default_args['labels']        = $default_labels;
		/** The new custom taxonomy is registered */
		register_taxonomy(strtolower($singular_name),$post_type, $default_args );
	}

	/**
	 * Used to add a new custom post type
	 *
	 * It adds a new custom post type using the given parameters
	 * If some parameters are not given. e.g the labels and args parameters are not given
	 * Then they are auto generated
	 * The user given parameters are merged with the default parameters
	 * 
	 * @since 1.0.0
	 * @param string $name the full name of the custom post type
	 * @param string $singular_name the singular name for the custom post type
	 * @param array $labels the labels for the new custom post type
	 * @param array $args the parameters for the new custom post type
	 */
	public function AddNewCustomPostType($name,$singular_name,$labels=array(),$args=array())
	{	
		/** The default labels for new custom post type */		
	    $default_labels                = array(
											'name'               => _x($name,$name,$plugin_text_domain),
											'singular_name'      => _x($singular_name,$name,$plugin_text_domain),
											'menu_name'          => _x($name,$name,$plugin_text_domain),
											'name_admin_bar'     => _x($singular_name,$name,$plugin_text_domain),
											'add_new'            => _x('Add New',$name,$plugin_text_domain),
											'add_new_item'       => __('Add New '.$singular_name,$plugin_text_domain),
											'new_item'           => __('New '.$singular_name,$plugin_text_domain),
											'edit_item'          => __('Edit '.$singular_name,$plugin_text_domain),
											'view_item'          => __('View '.$singular_name,$plugin_text_domain),
											'all_items'          => __('All '.$name,$plugin_text_domain),
											'search_items'       => __('Search '.$name,$plugin_text_domain),
											'parent_item_colon'  => __('Parent :'.$name,$plugin_text_domain),
											'not_found'          => __('No '.$name.' found.',$plugin_text_domain),
											'not_found_in_trash' => __('No '.$name.' found in Trash.',$plugin_text_domain)
		 );
		/** The default arguments for the new custom post type */
		$default_args                  = array(
											'public'             => true,
											'publicly_queryable' => true,
											'show_ui'            => true,
											'show_in_menu'       => true,
											'query_var'          => true,
											'rewrite'            => array( 'slug' => strtolower($singular_name)),
											'capability_type'    => 'post',
											'has_archive'        => false,
											'hierarchical'       => false,
											'menu_position'      => null,
											'supports'           => array( 'title', 'custom-fields')
        );
		/** The default labels are merged with user given labels */
		$default_labels                = array_merge($default_labels,$labels);
		/** The default arguments are merged with user given arguments */
		$default_args                  = array_merge($default_args,$args);
		/** The labels are added to the arguments */
		$default_args['labels']        = $default_labels;
		/** The new custom post type is registered */
		register_post_type($name,$default_args);
	}
	
	/**
	 * Used to save plugin options
	 *
	 * It saves the given WordPress plugin options
	 * 
	 * @since 1.0.0
	 * @param array $options the plugin options
	 * @param string $option_id id of the option to save
	 */
	public function SavePluginOptions($options,$option_id)
	{		
		/** The options values are saved */		
		update_option($option_id, $options);
	}
	
	/**
	 * Used to import the contents of an array to WordPress
	 *
	 * It creates a new custom post for each element in the array
	 * 
	 * @since 1.0.1
	 * @param string $post_type the name of the post type
	 * @param array $file_details the details of the file to be imported. it is an array with following keys:
	 * data => the array contents
	 * key_field => the name of the key field used to uniquely identify the post
	 * title_field => the field name that will be used for the post title
	 * content_field => the field name that will be used for the post content
	 * fields => the list of field names. all field names except for title and content field names are considered as custom fields
	 * fields_to_ignore => the list of fields to exlude from the import	 	
	 * @param int $start_line the line at which to start the import. it should be greater than or equal to 1
	 * @param int $line_count the line at which to end the import. it should be less than or equal to the size of the data to be imported
	 */
	public function ImportFile($post_type,$file_details,$start_line,$line_count)
	{
	    /** The data to be imported */
		$data                                               = $file_details['data'];	
		/** The name of the key field */
		$key_field                                          = $file_details['key_field'];	
		/** The title field */
		$title_field                                        = $file_details['title_field'];
		/** The content field */
		$content_field                                      = $file_details['content_field'];
		/** The name of all the fields of the file */
		$field_list                                         = $file_details['fields'];
		/** The list of fields to ignore */
		$fields_to_ignore                                   = $file_details['fields_to_ignore'];				
		/** The components of each meta data item is extracted using regular expression */
		for ($count1 = ($start_line-1); $count1 < $line_count; $count1++) {				
		    /** The data to be saved */
			$post_data                                      = array();				
			/** The post author is set to the user id of the logged in user */
		    $post_data['post_author']                       = get_current_user_id();
			/** The concatenation of all the field values */
			$combined_values                                = "";
			/** The data to be saved is generated */
			for ($count2 = 0; $count2 < count($data[$count1]); $count2++) {
			    /** The field name */
				$field_name                                 = $field_list[$count2];
				/** The field value */
				$field_value                                = $data[$count1][$count2];
				/** All the field values are combined */
				$combined_values                            = $combined_values.$field_value;
				/** If the field name is not in list of fields to ignore */
				if (!in_array($field_name, $fields_to_ignore)) {
				    /** If the field name does not match the title field then the field is added to custom field list */
					if ($field_name == $title_field) {
				        /** The title for custom post */
					    $post_data['post_title']            = $field_value;
                    }
				    /** The field name and value are added as custom field */
				    $post_data["custom_".$field_name]       = $field_value;					    
				}
			}
            /** The md5 checksum of all the field values. It is used to ensure data has not been updated */
            $post_data['custom_checksum']                   = md5($combined_values);				
			/** The parameters for the WordPress object. It indicates the type of object to be created */
			$parameters                                     = array("type"=>"post","type_name"=>$post_type);
			/** WordPress data object is created */
			$wordpress_data_object                          = new WordPressDataObject($configuration_object,$parameters);
			/** The WordPress data object is set to read/write */
			$wordpress_data_object->SetReadOnly(false);
			/** The data is set to the WordPress data object */
			$wordpress_data_object->SetData($post_data);
			/** The key field for the object is set */
			$wordpress_data_object->SetKeyField($key_field);
			/** The WordPress data is saved */
			$wordpress_data_object->Save();
		}
	}

    /**
     * Register the filters and actions with WordPress.
     *
     * @since 1.0.0
	 * 
	 * @return boolean $output used to indicate that application has no output
	 * all output is sent by wordpress actions
     */
    public function Main()
    {
    	/** The wordpress configuration is fetched */
		$wordpress_configuration    = $this->GetConfig("wordpress");		
    	/** Used to register the function that will be called when the plugin is activated */
		\register_activation_hook( $this->GetConfig("wordpress","plugin_file_path"), array( 'Application', 'WP_Activate' ) );
		/** Used to register the function that will be called when the plugin is deactivated */
		\register_activation_hook( $this->GetConfig("wordpress","plugin_file_path"), array( 'Application', 'WP_Deactivate' ) );
				
		$filters                    = $wordpress_configuration['filters'];         
		$actions                    = $wordpress_configuration['actions'];
		/** The filters are registered with WordPress */			
        foreach ($filters as $hook) {
            \add_filter($hook['hook'], array($hook['component'],$hook['callback']), $hook['priority'], $hook['accepted_args']);
        }
        /** The actions are registered with WordPress */
        foreach ($actions as $hook) {
            \add_filter($hook['hook'], array($hook['component'],$hook['callback']), $hook['priority'], $hook['accepted_args']);
        }
		
		$output = false;
		
		return ($output);
    }
}