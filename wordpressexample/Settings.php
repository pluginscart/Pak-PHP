<?php

namespace WordPressExample;

/**
 * This class implements the settings class
 * It contains functions that display the settings page
 * 
 * It registers plugin options and displays the settings page
 * 
 * @category   WordPressExample
 * @package    Settings
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0
 * @since      1.0.0
 */
final class Settings extends \Framework\Application\WordPress\Settings
{    
    /**
     * Options page callback
     *
     * @since    1.0.0
	 * @version  1.0.0	      
     */
    public function DisplaySettingsPage()
    {
    	try {       
	            /** The settings fields are displayed */
	            $settings_fields_html = $this->GetSettingsFieldsHtml();
	            $plugin_template_path = $this->GetConfig("wordpress", "plugin_template_path") . DIRECTORY_SEPARATOR . "settings.html";
	            $plugin_text_domain   = $this->GetConfig("wordpress", "plugin_text_domain");
	        
	            /** The tag replacement array is built */
	            $tag_replacement_arr = array(
	                array(
	                "heading" => __("WordPress Example", $plugin_text_domain),	                
	                "form_fields" => $settings_fields_html	                
	                )
	            );
	      
	            /** The settings page template is rendered */
	            $settings_page_html    = $this->GetComponent("template")->RenderTemplateFile($plugin_template_path, $tag_replacement_arr);
	            /** The settings page html is displayed */
	            $this->GetComponent("application")->DisplayOutput($settings_page_html);
	    }
		catch(\Exception $e){
			$this->GetComponent("errorhandler")->ExceptionHandler($e);
		}
    }
    
    /**
     * Registers and adds settings using the WordPress api
     *
     * @since    1.0.0
	 * @version  1.0.0	 
     */
    public function InitializeAdminPage()
    {
    	try {
    	    /** The plugin text domain */
	        $plugin_text_domain           = $this->GetConfig("wordpress", "plugin_text_domain");
	        /** The options id is fetched */
	        $options_id                   = $this->GetComponent("application")->GetOptionsId("options");			           
			/** The current plugin options are fetched */
			$options                      = $this->GetComponent("application")->GetPluginOptions($options_id);
			/** The plugin settings are initialized */
			$this->plugin_settings = array(
			    /** The visible fields */
				"title" => array(
				    "name" => __('Widget Title', $plugin_text_domain),
					"callback" => array("settings","TitleFieldCallback"),					
					"hidden" => false,
					"short_name" => "title",
					"args" => array("default_value" => (isset($options['title']))?$options['title']:"WordPress Example")
				),
				"text" => array(
				    "name" => __('Widget Text', $plugin_text_domain),
					"callback" => array("settings","TextFieldCallback"),					
					"hidden" => false,
					"short_name" => "text",
					"args" => array("default_value" => isset($options['text'])?$options['text']:"Welcome to WordPress!")
				)				
			);
		
	        $this->RegisterPluginOptions($this->plugin_settings);
		}
		catch(\Exception $e){
			$this->GetComponent("errorhandler")->ExceptionHandler($e);
		}
    }
        
    /**
     * Used to display the section information
     *
     * The section information is displayed
     * 
     * @since    1.0.0
	 * @version  1.0.0	 
     */
    public function PrintSectionInfo()
    {
    	/** The plugin text domain */
	    $plugin_text_domain           = $this->GetConfig("wordpress", "plugin_text_domain");
        echo __('Dashboard Settings', $plugin_text_domain);
    }
    
    /** 
     * Displays the text field settings field
     * 
	 * @since    1.0.0
	 * @version  1.0.0
	 * 
	 * @param array $args the arguments for the callback function. it is an array with one key: default_value
     */
    public function TextFieldCallback($args)
    {
        /** The options id is fetched */
	    $options_id           = $this->GetComponent("application")->GetOptionsId("options");	
        /** The path to the plugin template folder */
        $plugin_template_path = $this->GetConfig("wordpress", "plugin_template_path") . DIRECTORY_SEPARATOR;
		
		/** The plugin prefix */
        $plugin_prefix        = $this->GetConfig("wordpress", "plugin_prefix");
		/** The field name */
		$field_name           = "text";
		/** The field value */
		$field_value          = $args['default_value'];  
        /** The tag replacement array is built */
        $tag_replacement_arr = array(
            array(
                "id" => $plugin_prefix . "_" . $field_name,
                "name" => $options_id . '[' . $field_name . ']',
                "value" => $field_value
            )
        );
        /** The text area field template is rendered */
        $text_field_html      = $this->GetComponent("template")->RenderTemplateFile($plugin_template_path . "textarea.html", $tag_replacement_arr);
        /** The text area field is displayed */
        $this->GetComponent("application")->DisplayOutput($text_field_html);
    }
    
    /** 
     * Displays the title field settings
     * 
	 * @since    1.0.0
	 * @version  1.0.0
	 * 
	 * @param array $args the arguments for the callback function. it is an array with one key: default_value
     */
    public function TitleFieldCallback($args)
    {        
        /** The options id is fetched */
	    $options_id           = $this->GetComponent("application")->GetOptionsId("options");	
        /** The path to the plugin template folder */
        $plugin_template_path = $this->GetConfig("wordpress", "plugin_template_path") . DIRECTORY_SEPARATOR;
		/** The plugin prefix */
        $plugin_prefix        = $this->GetConfig("wordpress", "plugin_prefix");
		/** The field name */
		$field_name           = "title";
		/** The field value */
		$field_value          = $args['default_value'];  
        /** The tag replacement array is built */
        $tag_replacement_arr = array(
            array(
                "id" => $plugin_prefix . "_" . $field_name,
                "name" => $options_id . '[' . $field_name . ']',
                "value" => $field_value
            )
        );
        /** The text field template is rendered */
        $text_field_html      = $this->GetComponent("template")->RenderTemplateFile($plugin_template_path . "text.html", $tag_replacement_arr);
        /** The text field is displayed */
        $this->GetComponent("application")->DisplayOutput($text_field_html);
    }
}