<?php

namespace Framework\FrontController;

/**
 * This class implements the base Template class 
 * 
 * It contains functions that help in constructing the template widgets
 * The class is abstract and must be inherited by the template classes
 * 
 * @category   Framework
 * @package    FrontController
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    Release: 1.0.0
 * @link       N.A
 */
abstract class Template
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
     * @return array $template_information an array with 2 elements     
     * first is template file name
     * second is $tag_replacement_arr. this is a list of tags values that will replace the tags in the given template file 
     */
    function GetTemplateTagsFromFunction($tag_name)
    {        
        /** Configuration values are fetched from application configuration */
        $component_list      = array(
            "presentation"
        );
        $configuration_names = array(
            "general"
        );
        
        list($components, $configuration) = Configuration::GetComponentsAndConfiguration($component_list, $configuration_names);		
        /** If no url mapping is defined for the current url option then an exception is thrown */
        if (!isset($configuration['general']['application_url_mappings'][$configuration['general']['option']]))
            throw new \Exception("Invalid url request sent to application");
        /** The list of templates defined for the given url option */
        $url_templates = $configuration['general']['application_url_mappings'][$configuration['general']['option']]["templates"];
        
        $tag_values_found    = false;
        $tag_replacement_arr = array();
        for ($count = 0; $count < count($url_templates); $count++) {
            if ($url_templates[$count]["tag_name"] == $tag_name) {
                $ui_object_name      = $url_templates[$count]['object_name'];
                $ui_object           = Configuration::GetComponent($ui_object_name);
                $function_name       = $url_templates[$count]['function_name'];
                $template_file_name  = $url_templates[$count]['template_file_name'];                
                $template_parameters = $components['presentation']->GetTemplateParameters($configuration['general']['option'], $template_file_name);
                
                /** The template callback function is defined */
                $template_callback = array(
                    $ui_object,
                    $function_name
                );
				
                /** If the function is callable then it is called */
                if (is_callable($template_callback))
                    $tag_replacement_arr = call_user_func_array($template_callback, array(
                        $template_file_name,
                        $template_parameters
                    ));
                /** If it is not callable then an exception is thrown */
                else
                    throw new \Exception("Template function : " . $function_name . " was not found");
                
                $tag_values_found = true;
                break;
            }
        }
        
        if (!$tag_values_found)
            throw new \Exception("Template information for the tag: " . $tag_name . " could not be found");
        else {
            $template_information = array(               
                $template_file_name,
                $tag_replacement_arr
            );
            return $template_information;
        }        
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
        /** The path to the application template folder is fetched */
        $template_folder_path = Configuration::GetConfig("path","template_path");
        /** The template handling function is called with given option and parameters */
        list($template_file_name, $tag_replacement_arr) = $this->GetTemplateTagsFromFunction($tag_name);
        
        /** The tags in the template file are extracted */
        list($template_contents, $template_tag_list) = \Framework\Utilities\UtilitiesFramework::Factory("template")->ExtractTemplateFileTags($template_folder_path. DIRECTORY_SEPARATOR . $template_file_name);
        
        /** For each extracted template tag the value for that tag is fetched */
        for ($count = 0; $count < count($template_tag_list); $count++) {
            /** First the tag value is checked in the tag replacement array returned by the template handling function */
            $tag_name  = $template_tag_list[$count];
            $tag_value = (isset($tag_replacement_arr[$tag_name])) ? $tag_replacement_arr[$tag_name] : '!not found!';
			/** If the tag value is an array then the array is processed. This array can contain further template tags */          
            if (is_array($tag_value))
                $tag_value = \Framework\Utilities\UtilitiesFramework::Factory("template")->ReplaceTagWithArray($tag_name, $tag_value);
			/** If the tag value was not found then the function is called recursively */
			else if ($tag_value=='!not found!')$tag_value = $this->RenderApplicationTemplate($tag_name);
			/** If the tag value was not found then an exception is thrown */
			if ($tag_value=='!not found!')throw new \Exception("Tag value for tag: ".$tag_name." could not be found");
			/** The tag name is replaced with the tag contents */
            $template_contents = str_replace("{" . $tag_name . "}", $tag_value, $template_contents);
        }
        
        return $template_contents;
        
    }
}