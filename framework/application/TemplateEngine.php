<?php

namespace Framework\Application;

use \Framework\Configuration\Base as Base;

/**
 * This class implements the base Template class 
 * 
 * It contains functions that help in constructing the template widgets
 * The class is abstract and must be inherited by the template classes
 * 
 * @category   Framework
 * @package    Application
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    Release: 1.0.0
 * @link       N.A
 */
abstract class TemplateEngine extends Base
{   
	/**
     * Used to call a function of the presentation class that generates the template parameters
     * For the given url and tag name
     * 
     * It fetches the presentation object from application configuration
     * It then calls the function of the presentation object and returns the response
     * The name of the function is generated by removing the file extension
     * And replacing '_' with space and then changing first letter of each word to capital
     * After that the spaces are removed
     * 
     * @since 1.0.
     * @param string $option the url option
     * @param string $tag_name the name of the tag for which the parameters need to be generated
     * 
     * @return array $template_parameters it is an array that contains template parameters	 
     */
    final public function GetTemplateParameters($option, $tag_name)
    {        
        $template_parameters            = array();
        
        /** The presentation object is fetched from application configuration */
        $presentation_object            = $this->GetComponent('presentation');
        /** Url option is converted to camelcase */
        $function_name_suffix           = $this->GetComponent("string")->CamelCase($option);                
        
        $function_name                  = substr($tag_name, 0, strrpos($tag_name, "."));
		/** Functiona name is converted to camelcase */
        $function_name                  = $this->GetComponent("string")->CamelCase($function_name);
        $function_name                  = "Get" . $function_name . "ParametersFor" . $function_name_suffix;		
        /** The template parameters callback is defined */
        $template_parameters_callback   = array(
            $presentation_object,
            $function_name
        );
        /** If the callback exists then it is called. Otherwise the template parameters are set to empty array */
        if (is_callable($template_parameters_callback))
            $template_parameters        = call_user_func($template_parameters_callback);
        else
            $template_parameters        = array();
        
        return $template_parameters;       
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
    final public function GetTemplateTagsFromFunction($tag_name)
    {
		/** The application url mapping configuration */
		$application_url_mappings         = $this->GetConfig("general","application_url_mappings");  
		/** The application option */
		$application_option               = $this->GetConfig("general","option");       		
        /** If no url mapping is defined for the current url option then an exception is thrown */
        if (!isset($application_url_mappings[$application_option]))
            throw new \Exception("Invalid url request sent to application");
        /** The list of templates defined for the given url option */
        $url_templates = $application_url_mappings[$application_option]["templates"];
        
        $tag_values_found    = false;
        $tag_replacement_arr = array();
        for ($count = 0; $count < count($url_templates); $count++) {
            if ($url_templates[$count]["tag_name"] == $tag_name) {
                $ui_object_name      = $url_templates[$count]['object_name'];
                $ui_object           = $this->GetComponent($ui_object_name);
                $function_name       = $url_templates[$count]['function_name'];
                $template_file_name  = $url_templates[$count]['template_file_name'];
				/** Used to indicate that function for generating template parameters should be automatically called */                
                $template_parameters = $this->GetTemplateParameters($application_option, $template_file_name);
                
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
    final public function RenderApplicationTemplate($tag_name)
    {    			
        /** The template handling function is called with given option and parameters */
        list($template_file_name, $tag_replacement_arr)   = $this->GetTemplateTagsFromFunction($tag_name);
        
        /** The path to the framework template folder is fetched */
        $template_folder_path                             = $this->GetConfig("path","template_path");
		/** The path to the application template folder is fetched */
        $application_template_folder_path                 = $this->GetConfig("path","application_template_path");
		/** The template file path */
		$template_file_path                               = $template_folder_path. DIRECTORY_SEPARATOR . $template_file_name;
		if (!is_file($template_file_path)) {
			$template_file_path                           = $application_template_folder_path. DIRECTORY_SEPARATOR . $template_file_name;
		    if (!is_file($template_file_path)) throw new \Exception("Template file: ".$template_file_name." could not be found");	
		}

        /** The tags in the template file are extracted */
        list($template_contents, $template_tag_list)      = $this->GetComponent("template_helper")->ExtractTemplateFileTags($template_file_path);
        
        /** For each extracted template tag the value for that tag is fetched */
        for ($count = 0; $count < count($template_tag_list); $count++) {
            /** First the tag value is checked in the tag replacement array returned by the template handling function */
            $tag_name                                     = $template_tag_list[$count];
            $tag_value                                    = (isset($tag_replacement_arr[$tag_name])) ? $tag_replacement_arr[$tag_name] : '!not found!';
			/** If the tag value is an array then the array is processed. This array can contain further template tags */          
            if (is_array($tag_value))
                $tag_value                                = $this->GetComponent("template_helper")->ReplaceTagWithArray($tag_name, $tag_value);
			/** If the tag value was not found then the function is called recursively */
			else if ($tag_value=='!not found!')$tag_value = $this->RenderApplicationTemplate($tag_name);
			/** If the tag value was not found then an exception is thrown */
			if ($tag_value=='!not found!')throw new \Exception("Tag value for tag: ".$tag_name." could not be found");
			/** The tag name is replaced with the tag contents */
            $template_contents                            = str_replace("{" . $tag_name . "}", $tag_value, $template_contents);
        }
        
        return $template_contents;
        
    }
}