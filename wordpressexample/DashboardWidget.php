<?php

namespace WordPressExample;

use \Framework\Configuration\Base as Base;

/** 
 * This class implements the functionality of the Dashboard Widget
 * 
 * It contains functions that are used to display the Dashboard Widget
 * 
 * @category   WordPressExample
 * @package    Base
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0
 * @since      1.0.0
 */
final class DashboardWidget extends Base
{
    /**
     * Displays the dashboard widget
     *
     * @since    1.0.0
     */
    public function DisplayDashboardWidget()
    {
    	try {
    		    /** The options id is fetched */
	            $options_id                        = $this->GetComponent("application")->GetOptionsId("options");
	            /** The current plugin options */
	            $plugin_options                    = $this->GetComponent("application")->GetPluginOptions($options_id);
				/** The dashboard text */
				$dashboard_text                    = $plugin_options['text'];
				/** The path to the plugin template folder */
                $plugin_template_path              = $this->GetConfig("wordpress", "plugin_template_path") . DIRECTORY_SEPARATOR;
				/** The ajax nonce */
				$ajax_nonce                        = wp_create_nonce("wordpress-example");		        
		        /** The tag replacement array is built */
		        $tag_replacement_arr               = array(
											            array("text" => $dashboard_text,"ajax_nonce"=>$ajax_nonce)
											        );
		        /** The dashboard template is rendered */
		        $dashboard_html                    = $this->GetComponent("template")->RenderTemplateFile($plugin_template_path . "dashboard.html", $tag_replacement_arr);
		        /** The text field is displayed */
		        $this->GetComponent("application")->DisplayOutput($dashboard_html);    			
		}
		catch(\Exception $e) {
			$this->GetComponent("errorhandler")->ExceptionHandler($e);
		}                
    }
	
    /**
     * Function that is used to handle ajax request
     *
     * @since 1.0.0
     */
    public function DashboardWidgetAjax()
    {
    	try {    		
		    	/** The ajax referer value is checked for security */
		    	check_ajax_referer('wordpress-example', 'security' );
		    	/** Success response is returned */
				echo json_encode(array("result"=>"success"));
				die();			
		}
		catch(\Exception $e) {
			$this->GetComponent("errorhandler")->ExceptionHandler($e);	
		}			
    }
}