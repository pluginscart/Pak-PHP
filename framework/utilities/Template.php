<?php

namespace Framework\Utilities;

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
class Template
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
     * Used to get tags within given template file
     * 
     * It reads a template file and extracts all the tags in the template file
     * 
     * @since 1.0.0     
	 * @param string $template_path absolute path to the template html file
     * 
     * @return array $template_information an array with 2 elements.
     * first one is the template contents string.
     * the second one is the template tag replacement string
     */
    function ExtractTemplateFileTags($template_path)
    {                
        $template_contents = FileSystem::GetInstance(array())->ReadLocalFile($template_path);
        
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
                $template_path     = $item_value['template_path'];
                $template_values   = $item_value['template_values'];
                /** If the template values is not an array then it is placed in an array
                 * The template will be replaced the same number of times as the length of the
                 * $template_values array
                 */
                if (!is_array($template_values))
                    $template_values = array(
                        $template_values
                    );
                $item_value = $this->RenderTemplateFile($template_path);
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
	 * @param string $template_path absolute path to the template html file
     * @param array $tag_replacement_arr tag replacement values
     * 
     * @return string $final_tag_replacement_value the contents of the template file with all the tags replaced
     * The template file is replaced x number of times where x is the length of $tag_replacement_arr
     */
    public function RenderTemplateFile($template_path, $tag_replacement_arr)
    {
        /** The final tag replacement value */
        $final_tag_replacement_value = "";
        /** The tags in the template file are extracted */
        list($template_contents, $template_tag_list) = $this->ExtractTemplateFileTags($template_path);
        
        /** A template may be rendered multiple times. e.g table rows or table column templates */
        for ($count1 = 0; $count1 < count($tag_replacement_arr); $count1++) {
            $temp_template_contents = $template_contents;
            $tag_replacement        = $tag_replacement_arr[$count1];
            /** For each extracted template tag the value for that tag is fetched */
            for ($count2 = 0; $count2 < count($template_tag_list); $count2++) {
                /** First the tag value is checked in the tag replacement array */
                $tag_name  = $template_tag_list[$count2];
                $tag_value = (isset($tag_replacement[$tag_name])) ? $tag_replacement[$tag_name] : '!NOT SET!';                
                /** If the tag value is an array then the array values are resolved to a string */
                if (is_array($tag_value))
                    $tag_value = $this->ReplaceTagWithArray($tag_name, $tag_value);
				else if ($tag_value=="!NOT SET!") throw new \Exception("Tag replacement value was not given for the tag: ".$tag_name);
                /** The tag name is replaced with the tag value */
                $temp_template_contents = str_replace("{" . $tag_name . "}", $tag_value, $temp_template_contents);
            }
            /** The final template string is updated with the contents of the template */
            $final_tag_replacement_value .= $temp_template_contents;
        }
        
        return $final_tag_replacement_value;        
    }  
}