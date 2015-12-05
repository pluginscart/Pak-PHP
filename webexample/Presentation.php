<?php

namespace WebExample;

use Framework\Configuration\Base as Base;

/**
 * This class implements the presentation class for the WebExample application
 * It implements functions that provide template parameters to the controller classes
 * 
 * It is used to provide template parameters
 * 
 * @category   WebExample
 * @package    Presentation
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0
 * @since      1.0.0
 */
class Presentation extends Base
{
    /** 
     * Used to provide template parameters for list_page template
	 * This template is used by index action
	 * 
	 * @since 1.0.0	 
     */
    public function GetListPageParametersForIndex()
    {
        $template_parameters = array(        	
            "table_html" => "",                                              
            "framework_template_url" => $this->GetConfig('path','framework_template_url')            
        );
        
        return $template_parameters;
    }    
	
	/** 
     * Used to provide template parameters for base_page template
	 * This template is used by index action     
	 * 
	 * @since 1.0.0	 
     */
    public function GetBasePageParametersForIndex()
    {
        /** The template object is fetched */
        $template_obj                  = $this->GetComponent('template');
        /** The default configuration parameters are fetched from application configuration */        
        $framework_template_url        = $this->GetConfig("path","framework_template_url");
        $web_vendor_path               = $this->GetConfig("path","web_vendor_path");
		$application_folder_path       = $this->GetConfig("path","application_folder_url");
        
        /** The javascript and css tag strings are generated using the basicsite template object */
        $css_files        = array(
            $framework_template_url . "/css/basicsite.css",
            $web_vendor_path . "/colorbox/example4/colorbox.css",
            $application_folder_path . "/css/custom.css"
        );
        $javascript_files = array(
            $web_vendor_path . "/jquery/jquery-2.1.4.min.js",
            $web_vendor_path . "/colorbox/jquery.colorbox-min.js",
            $web_vendor_path . "/jquery-ajax-loader-spinner/loadingoverlay.js",
            $framework_template_url . "/js/basicsite.js",
            $framework_template_url . "/js/utilities.js",
            $application_folder_path . "/js/custom.js"
        );
        /** The javascript and css tags are rendered using the parameters */
        $css_tags         = $template_obj->Render("css_js_tags", array(
            "file_type" => "css",
            "file_list" => $css_files
        ));
        $javascript_tags  = $template_obj->Render("css_js_tags", array(
            "file_type" => "javascript",
            "file_list" => $javascript_files
        ));
        
        $template_parameters = array(
            "css_tags" => $css_tags,
            "javascript_tags" => $javascript_tags
        );
        
        return $template_parameters;
    }    
}
