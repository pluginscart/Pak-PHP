<?php

namespace Framework\Templates;
use Framework\ApplicationConfiguration\ApplicationConfiguration as ApplicationConfiguration;

/**
 * This class implements the base BrowserApplicationTemplate class 
 * 
 * It contains functions that help in constructing the template widgets
 * The class is abstract and must be inherited by the template classes
 * 
 * @category   Framework
 * @package    Templates
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    Release: 1.0.0
 * @link       N.A
 */
abstract class BrowserApplicationTemplate
{
    /**
     * The single static instance
     */
    protected static $instance;
    /**
     * Class constructor
     * Used to prevent creating an object of this class outside of the class using new operator
     * 
     * Used to implement Singleton class
     * 
     * @since 1.0.0		  
     */
    protected function __construct()
    {
        
    }
    /**
     * Used to return a single instance of the class
     * 
     * Checks if instance already exists
     * If it does not exist then it is created
     * The instance is returned
     * 
     * @since 1.0.0
     * 
     * @return BrowserApplicationTemplate static::$instance name the instance of the correct child class is returned 
     */
    public static function GetInstance()
    {        
        if (static::$instance == null) {
            static::$instance = new static();
        }
        return static::$instance;        
    }
    /**
     * Used to render a user interface element. e.g textbox or html table
     * 
     * It calls the relavant private function that renders the user interface element
     * This function is abstract and must be defined in the child class		 		
     * 
     * @since 1.0.0
     * @param string $option the type of object that needs to be rendered. e.g textbox
     * @param array $parameters the parameters needed for rendering the user interface object		 		 
     */
    abstract function Render($option, $parameters);
    /**
     * Used to get tags within given template file
     * 
     * It reads a template file and extracts all the tags in the template file
     * 
     * @since 1.0.0
     * @param string $template_category template category. folder path of template file relative to templates folder		 
     * @param string $template_name short name of the template file without extension or the name of a tag		
     * 
     * @return array $template_information an array with 2 elements.
     * first one is the template contents string.
     * the second one is the template tag replacement string
     */
    function ExtractTemplateFileTags($template_category, $template_name)
    {        
        $template_path     = ApplicationConfiguration::GetConfig("template_path") . DIRECTORY_SEPARATOR . $template_category . DIRECTORY_SEPARATOR . $template_name;
        $template_contents = ApplicationConfiguration::GetComponent("utilities")->ReadLocalFile($template_path);
        
        /** All template tags of the form {} are extracted from the template file */
        preg_match_all("/\{(.+)\}/iU", $template_contents, $matches);
        
        $template_tag_list = $matches[1];
        
        $template_information = array(
            $template_contents,
            $template_tag_list
        );
        
        return $template_information;        
    }
    /**
     * Call template handling function
     * 
     * It gets the template handling function details from application configuration
     * And calls the function. The function should return tag replacement parameters
     * That will be used to replace the tags in the template
     * 
     * @since 1.0.0		 		
     * @param string $tag_name the name of a template tag
     * @throws Exception an object of type Exception is thrown if no template handling function was found for the given template and option
     * 
     * @return array $template_information an array with 3 elements
     * first is template category
     * second is template name
     * third is $tag_replacement_arr. this is a list of tags values that will replace the tags in the given template file 
     */
    function GetTemplateTagsFromFunction($tag_name)
    {        
        /** Configuration values are fetched from application configuration **/
        $component_list      = array(
            "presentation"
        );
        $configuration_names = array(
            "option",
            "parameters",
            "application_url_mappings"
        );
        
        list($components, $configuration) = ApplicationConfiguration::GetComponentsAndConfiguration($component_list, $configuration_names);
        /** If no url mapping is defined for the current url option then an exception is thrown **/
        if (!isset($configuration['application_url_mappings'][$configuration['option']]))
            throw new \Exception("Invalid url request sent to application", 110);
        /** The list of templates defined for the given url option **/
        $url_templates = $configuration['application_url_mappings'][$configuration['option']]["templates"];
        
        $tag_values_found    = false;
        $tag_replacement_arr = array();
        for ($count = 0; $count < count($url_templates); $count++) {
            if ($url_templates[$count]["tag_name"] == $tag_name) {
                $ui_object_name      = $url_templates[$count]['object_name'];
                $ui_object           = ApplicationConfiguration::GetComponent($ui_object_name);
                $function_name       = $url_templates[$count]['function_name'];
                $template_name       = $url_templates[$count]['template_name'];
                $template_category   = $url_templates[$count]['template_path'];
                $template_parameters = $components['presentation']->GetTemplateParameters($configuration['option'], $template_name);
                
                /** The template callback function is defined **/
                $template_callback = array(
                    $ui_object,
                    $function_name
                );
                /** If the function is callable then it is called **/
                if (is_callable($template_callback))
                    $tag_replacement_arr = call_user_func_array($template_callback, array(
                        $template_name,
                        $template_parameters
                    ));
                /** If it is not callable then an exception is thrown **/
                else
                    throw new \Exception("Template function : " . $function_name . " was not found", 110);
                
                $tag_values_found = true;
                break;
            }
        }
        
        if (!$tag_values_found)
            throw new \Exception("Template information for the tag: " . $tag_name . " could not be found", 110);
        else {
            $template_information = array(
                $template_category,
                $template_name,
                $tag_replacement_arr
            );
            return $template_information;
        }        
    }
    /**
     * Used to replace the given tag name with a tag array
     * 
     * It replaces the tag name with an array
     * Each element of the array can be a simple value
     * Or it can be an array which will contain a template name and template value pair		
     * 
     * @since 1.0.0
     * @param string $tag_name the name of the tag to replace
     * @param string $tag_value the value that will replace the tag name
     * 
     * @return string $tag_value the value to replace the tag name 
     */
    function ReplaceTagWithArray($tag_name, $tag_value)
    {        
        $replacement_value = "";
        for ($count = 0; $count < count($tag_value); $count++) {
            $item_value = $tag_value[$count];
            if (is_array($item_value)) {
                $template_name     = $item_value['template_name'];
                $template_category = $item_value['template_category'];
                $template_values   = $item_value['template_values'];
                /** If the template values is not an array then it is placed in an array
                 * The template will be replaced the same number of times as the length of the
                 * $template_values array
                 */
                if (!is_array($template_values))
                    $template_values = array(
                        $template_values
                    );
                $item_value = $this->RenderTemplateFile($template_name, $template_category, $template_values);
            }
            $replacement_value .= $item_value;
        }
        return $replacement_value;       
    }
    /**
     * Used to render the given template file with the given parameters
     * 
     * It reads the given template file from the given template category
     * It parses all the tags from the template file
     * It then replaces each tag with the correct value
     * From the $tag_replacement_arr parameter		  		 		 
     * 
     * @since 1.0.0
     * @param string $template_name short name of the template file
     * @param string $template_category name of the template category
     * @param array $tag_replacement_arr tag replacement values
     * 
     * @return string $final_tag_replacement_value the contents of the template file with all the tags replaced
     * The template file is replaced x number of times where x is the length of $tag_replacement_arr
     */
    public function RenderTemplateFile($template_name, $template_category, $tag_replacement_arr)
    {
        
        /** The default application configuration is fetched **/
        $default_configuration = ApplicationConfiguration::GetConfig("default_configuration");
        
        /** The final tag replacement value **/
        $final_tag_replacement_value = "";
        /** The tags in the template file are extracted **/
        list($template_contents, $template_tag_list) = $this->ExtractTemplateFileTags($template_category, $template_name);
        
        /** A template may be rendered multiple times. e.g table rows or table column templates **/
        for ($count1 = 0; $count1 < count($tag_replacement_arr); $count1++) {
            $temp_template_contents = $template_contents;
            $tag_replacement        = $tag_replacement_arr[$count1];
            /** For each extracted template tag the value for that tag is fetched **/
            for ($count2 = 0; $count2 < count($template_tag_list); $count2++) {
                /** First the tag value is checked in the tag replacement array **/
                $tag_name  = $template_tag_list[$count2];
                $tag_value = (isset($tag_replacement[$tag_name])) ? $tag_replacement[$tag_name] : false;
                /** If the tag value was not found then the default application configuration is checked for the tag name **/
                if ($tag_value === false || is_string($tag_replacement)) {
                    /** The default application configuration is checked for the tag name **/
                    if (isset($default_configuration[$tag_name]))
                        $tag_value = $default_configuration[$tag_name];
                    else
                        throw new \Exception("No template found for tag: " . $tag_name, 110);
                }
                /** If the tag value is an array then the array values are resolved to a string **/
                if (is_array($tag_value))
                    $tag_value = $this->ReplaceTagWithArray($tag_name, $tag_value);
                /** The tag name is replaced with the tag value **/
                $temp_template_contents = str_replace("{" . $tag_name . "}", $tag_value, $temp_template_contents);
            }
            /** The final template string is updated with the contents of the template **/
            $final_tag_replacement_value .= $temp_template_contents;
        }
        
        return $final_tag_replacement_value;        
    }
    /**
     * Used to render an application template
     * 
     * It displays the given template. It reads the template file from the given path		  
     * It then extracts all the tags from the template file. It then calls the object function defined in the template tag mapping		
     * This mapping is defined in application configuration file. The object function is then called		 
     * This function returns an array of tag replacement values. These values are used to replace the tags		 
     * A tag can be handled by a function defined in the template tag mapping		 
     * 
     * @since 1.0.0
     * @param string $tag_name template tag name. name of the tag that needs to be replaced with a template
     * 
     * @return string $template_contents the contents of the template file with all the tags replaced. suitable for diplaying in browser
     */
    public function RenderApplicationTemplate($tag_name)
    {
        
        /** The default application configuration is fetched **/
        $default_configuration = ApplicationConfiguration::GetConfig("default_configuration");
        
        /** The template handling function is called with given option and parameters **/
        list($template_category, $template_name, $tag_replacement_arr) = $this->GetTemplateTagsFromFunction($tag_name);
        
        /** The tags in the template file are extracted **/
        list($template_contents, $template_tag_list) = $this->ExtractTemplateFileTags($template_category, $template_name);
        
        /** For each extracted template tag the value for that tag is fetched **/
        for ($count = 0; $count < count($template_tag_list); $count++) {
            /** First the tag value is checked in the tag replacement array returned by the template handling function **/
            $tag_name  = $template_tag_list[$count];
            $tag_value = (isset($tag_replacement_arr[$tag_name])) ? $tag_replacement_arr[$tag_name] : false;
            /** If the tag value was not returned by the template handling function then the default application configuration is checked for the tag name **/
            if ($tag_value === false) {
                /** The default application configuration is checked for the tag name **/
                if (isset($default_configuration[$tag_name]))
                    $tag_value = $default_configuration[$tag_name];
                else {
                    /** Used to check if a template is defined for the given tag in application configuration **/
                    $tag_value = $this->RenderApplicationTemplate($tag_name);
                }
            }
            if (is_array($tag_value))
                $tag_value = $this->ReplaceTagWithArray($tag_name, $tag_value);
            
            $template_contents = str_replace("{" . $tag_name . "}", $tag_value, $template_contents);
        }
        
        return $template_contents;
        
    }
}