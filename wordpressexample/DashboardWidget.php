<?php

namespace IslamCompanion;

use \Framework\Configuration\Base as Base;

/** 
 * This class implements the functionality of the Holy Quran Dashboard Widget
 * 
 * It contains functions that are used to display the Holy Quran Dashboard Widget
 * 
 * @category   IslamCompanion
 * @package    IslamCompanion
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    2.0.0
 * @link       N.A
 */
final class HolyQuranDashboardWidget extends Base
{
    /**
     * Displays Quranic verses
     *
     * @since    2.0.0
     */
    public function DisplayDashboardWidget()
    {
    	try {
    		    /** The options id is fetched */
	            $options_id                        = $this->GetComponent("application")->GetOptionsId("options");
	            /** The current plugin options */
	            $plugin_options                    = $this->GetComponent("application")->GetPluginOptions($options_id);				
    			/** The state parameter is initialized */
    			$state                             = array("action"=>"current","sura"=>$plugin_options['sura'],"ruku"=>$plugin_options['ruku'],"division_number"=>$plugin_options['division_number']);    		
        	    /** The parameters for making the api request for getting the Holy Quran Navigator */
				$api_parameters                    = $this->GetHolyQuranDashboardParameters($state);
				/** The api response. The current url contents are fetched and assigned to api response */
		        $response                          = $this->GetComponent("application")->MakeAPIRequestToLocalModule($api_parameters);						
		        /** The response is displayed */
				echo $response['message']['html'];
		}
		catch(\Exception $e) {
			$this->GetComponent("errorhandler")->ExceptionHandler($e);
		}                
    }
    
	/**
     * The parameters for making the api request for getting the Holy Quran Dashboard
     *
     * @since 2.0.0
	 * @param array $state the current state of the navigator. it is an array with following keys:
	 * division_number => the division number
	 * sura => the selected sura
	 * ruku => the selected ruku
	 * 
	 * @return array $parameters the parameters used to made api call for fetching The Holy Quran Dashboard
     */
    public function GetHolyQuranDashboardParameters($state)
    {
    	try {
    			/** The options id is fetched */
	            $options_id                        = $this->GetComponent("application")->GetOptionsId("options");
	            /** The current plugin options */
	            $plugin_options                    = $this->GetComponent("application")->GetPluginOptions($options_id);				
    		    /** WordPress ajax nonce. prevents ajax requests from outside WordPress */
    		    $ajax_nonce                        = wp_create_nonce('islam-companion');		   
		        /** The parameters used to make the api request */
		        $parameters                        = array();
				/** The name of the local module to call */
				$parameters['module']              = "IslamCompanionApi";
				/** The name of the function to call. this function returns the frontend for the Holy Quran Dashboard widget */
				$parameters['option']              = "get_holy_quran_navigator";
				/** The language of the verses */
				$parameters['language']            = $plugin_options['language'];
				/** The narrator name */
				$parameters['narrator']            = $plugin_options['narrator'];
				/** The sura number */
				$parameters['sura']                = $state['sura'];
				/** The ruku number */
				$parameters['ruku']                = $state['ruku'];
				/** The division name */
				$parameters['division']            = strtolower($plugin_options['division']);
				/** The division number */
				$parameters['division_number']     = $state['division_number'];
				/** The start ayat */
				$parameters['ayat']                = $plugin_options['ayat'];
				/** Used to indicate if html of navigator should be in full hmtl page */
				$parameters['full_page']           = 0;
				/** Used to indicate that api response should not be encrypted */
				$parameters['encrypt_response']    = 0;
				/** Used to indicate the request type */
				$parameters['request_type']        = 'api';
				/** The action performed */
				$parameters['action']              = $state['action'];
				/** The custom script. it added a nonce value that allows secure ajax requests in WordPress */
				$parameters['custom_code']         = "IC_Holy_Quran_Dashboard_Widget.ic_ajax_nonce='".$ajax_nonce."';";
							
				/** The parameters are returned */				
				return $parameters;  	
		}
		catch(\Exception $e) {
			$this->GetComponent("errorhandler")->ExceptionHandler($e);	
		}			
    }

    /**
     * The dashboard widget settings are updated
     *
	 * The wordpress dashboard options for the Holy Quran Dashboard widget are updated
	 * using the information returned by module api call
	 * 
     * @since 2.0.0	 
	 * @param array $parameters the navigation settings returned by call to local api module
     */
    public function UpdateSettings($parameters)
    {
    	try {    		   
		    /** The options id is fetched */
	        $options_id                        = $this->GetComponent("application")->GetOptionsId("options");
	        /** The current plugin options */
	        $plugin_options                    = $this->GetComponent("application")->GetPluginOptions($options_id);
			/** The navigator state returned by api call is merged with current plugin options */
			$plugin_options                    = array_merge($plugin_options,$parameters);
			/** The plugin options are saved */			
			$this->GetComponent("application")->SavePluginOptions($plugin_options,$options_id);
		}
		catch(\Exception $e) {
			$this->GetComponent("errorhandler")->ExceptionHandler($e);	
		}			
    }
	
    /**
     * Function that is used to handle ajax request
     *
     * @since 2.0.0
     */
    public function HolyQuranDashboardWidgetAjax()
    {
    	try {    		
		    	/** The application parameters */
		    	$parameters                = $this->GetConfig("general","parameters");				
				/** The ajax referer value is checked for security */
		    	check_ajax_referer('islam-companion', 'security' );
				/** If verse data needs to be fetched */
		        if ($parameters['plugin_action'] == 'fetch_navigator_data') {
					/** The parameters for making the api request for getting the Holy Quran Dashboard Widget data */
					$api_parameters        = $this->GetHolyQuranDashboardParameters($parameters['state']);					
					/** The api response. The current url contents are fetched and assigned to api response */			
		        	$response              = $this->GetComponent("application")->MakeApiRequestToLocalModule($api_parameters);
					/** The dashboard widget settings are updated using response from api call to local module */
		        	$this->UpdateSettings($response['message']['state']);				 
		        	/** The response is displayed */
					echo json_encode(array("result"=>"success","text"=>$response['html']));
					/** The script is terminated so correct response can be sent to the browser */
					wp_die();
		        }
		}
		catch(\Exception $e) {
			$this->GetComponent("errorhandler")->ExceptionHandler($e);	
		}			
    }
}