<?php

namespace Framework\Frameworks\WordPress;

use \Framework\Configuration\Base as Base;

/**
 * This class implements the main plugin class
 * It contains functions that implement the filter, actions and hooks defined in the application configuration
 * 
 * It is used to implement the main functions of the plugin
 * 
 * @category   Framework
 * @package    WordPress
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0
 * @link       N.A
 */
abstract class Settings extends Base
{	    
    /**
     * Used to display the settings fields
     *
     * The output of this function should be shown inside a form
     * It shows the registered fields
     * 
     * @since    1.0.0
     * 
     * @return string $field_settings_html the field settings string is returned 
     */
    final public function GetSettingsFieldsHtml()
    {
        /** Output buffering is started so the field content can be fetched */
        ob_start();
        /** The wordpress configuration is fetched */
        $wordpress_configuration          = $this->GetConfig("wordpress");
        /** The registered option page fields are displayed */
        settings_fields($wordpress_configuration['plugin_prefix'] . '_option_group');
        /** The registered section title and fields for the given page are displayed */
        do_settings_sections($wordpress_configuration['settings_page_url']);        
        /** The field settings string is fetched */
        $field_settings_html              = ob_get_clean();
        
        return $field_settings_html;
    }
    
    /**
     * Register plugin options
     *
     * The plugin settings options are registered
     * 
     * @since    1.0.0	 
     * @param $plugin_settings an array of options each element is an associative array with 1 key and 5 values
     * the key is the short name of the field. the values are:
     * name => field label,
     * callback => the callback used to render the field
     * default_value => the default value of the field
     * hidden => used to indicate if the field is hidden
     * short_name => the short field name
     * args => the arguments for the callback function
     */
    final public function RegisterPluginOptions($plugin_settings)
    {
    	/** 
		 * Used to indicate if the plugin option should be saved
		 * It allows the plugin options to be used even if the user has not saved the plugin settings from the settings page
		 */
    	$save_plugin_option                   = false;
        /** The wordpress configuration is fetched */
        $wordpress_configuration              = $this->GetConfig("wordpress");
        /** The options id is fetched */
        $options_id                           = $this->GetComponent("application")->GetOptionsId("options");
        /** The current plugin options are fetched */
        $options                              = $this->GetComponent("application")->GetPluginOptions($options_id);        
        /** The settings group is registered */
        register_setting($wordpress_configuration['plugin_prefix'] . '_option_group', $options_id, array(
            $this,
            'Sanitize'
        ));
        
        /** If the print section info callback is not defined then an exception is thrown */
        $print_section_info_callback = array(
            $this,
            'PrintSectionInfo'
        );
        if (!is_callable($print_section_info_callback))
            throw new \Exception("PrintSectionInfo callback function is not defined");
        
        /** The settings section is registered */
        add_settings_section($wordpress_configuration['plugin_prefix'] . '_settings_id', // ID
            '', // Title
            $print_section_info_callback, // Callback
            $wordpress_configuration['settings_page_url'] // Page
        );
       
        /** All of the plugin settings are registered */
        foreach ($plugin_settings as $field_short_name => $field_information) {        	
            /** The field callback. If it is given as an object name then the object is fetched from application configuration */
            $field_callback                  = (is_object($field_information['callback'][0])) ? $field_information['callback'][0] : $this->GetComponent($field_information['callback'][0]);
			$field_callback                  = array($field_callback, $field_information['callback'][1]);
			
            /** The field label */
            $field_label                     = $field_information['name'];            
            /** Short field name. Used to create name of callback function */
            $short_field_name                = $field_information['short_name'];
            /** Indicates if field is hidden */
            $is_hidden                       = $field_information['hidden'];
            /** Callback function arguments */
            $args                            = $field_information['args'];
			/** The default field value */
            $default_value                   = $args['default_value'];
			/** The default field value */
            $args['field_name']              = $field_information['short_name'];
			
            /** If the settings field is hidden then the field label is set to empty */
            if ($is_hidden)
                $field_label                 = "";
			
            /** If the field callback is not defined then an exception is thrown */
            if (!is_callable($field_callback))
                throw new \Exception("The callback for the field: " . $field_label . " was not defined");
			
            /** The settings field is added to the plugin settings form */
            add_settings_field($wordpress_configuration['plugin_prefix'] . '_' . $short_field_name, $field_label, $field_callback, $wordpress_configuration['settings_page_url'], $wordpress_configuration['plugin_prefix'] . '_settings_id', $args);
            
            /** The default option value is set */
            if (!isset($options[$short_field_name])) {
                $options[$short_field_name] = $default_value;
				$save_plugin_option         = true;
			}
        }
        
        /** The options are saved */
        if ($save_plugin_option)
            $this->GetComponent("application")->SavePluginOptions($options, $options_id);
    }
    
    /**
     * Sanitize each setting field as needed
     * 
     * This function is automatically called when the user submits the settings form
     * 
     * @param array $input Contains all settings fields as array keys
     */
    final public function Sanitize($input)
    {
        /** The updated input fields array is initialized */
        $new_input = array();
        foreach ($input as $field_name => $field_value) {
            /** The user submitted data is sanitized */
            if (isset($input[$field_name]))
                $new_input[$field_name] = sanitize_text_field($input[$field_name]);
        }
        
        return $new_input;
    }
    
}