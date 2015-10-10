<?php

namespace Framework\WebApplication;

/**
 * This class implements the base BrowserApplication class 
 * 
 * It contains functions that help in constructing the user interface of browser based applications
 * The class is abstract and must be inherited by the application user interface class
 * 
 * @category   Framework
 * @package    WebApplication
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    Release: 1.0.0
 * @link       N.A
 */
abstract class Application
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
     * @return BrowserApplication static::$instance name the instance of the correct child class is returned 
     */
    public static function GetInstance()
    {        
        if (static::$instance == null) {
            static::$instance = new static();
        }
        return static::$instance;       
    }    
    /**
     * Used to handle the application request
     * 
     * It checks the url mappings in the application configuration
     * And calls the defined objects function
     * 
     * @since 1.0.0
     * @throws Exception an object of type Exception is thrown if a url request is sent for which no mapping 
     * is defined in the application configuration class
     */
    public function HandleRequest()
    {        
        /** Utility object and configuration values are fetched from application configuration */
        $object_names        = array(
            "testing",
            "application",
        );
        $configuration_names = array(
            "general",
            "testing"
        );
        
        list($components, $configuration) = Configuration::GetComponentsAndConfiguration($object_names, $configuration_names);
		        
        /** If no url mapping is defined for the current url option then a url mapping is auto generated */
        if (!isset($configuration['general']['application_url_mappings'][$configuration['general']['option']]))
			{
				/** The string object is fetched from application configuration */
				$string_obj           = \Framework\Utilities\UtilitiesFramework::Factory("string");
				$application_obj      = $components['application'];
				$class_function       = $string_obj->Concatenate("Handle",$string_obj->CamelCase($configuration['general']['option']));
				$application_callback = array(
                        $application_obj,
                        $class_function
           		);
				if (is_callable($application_callback))
					{
						$configuration['general']['application_url_mappings'][$configuration['general']['option']]=array(
			                "controller" => array(
			                    "object_name" => "application",
			                    "function_name" => $class_function
			                )
			            );
						Configuration::SetConfig("general", "application_url_mappings", $configuration['general']['application_url_mappings']);
					}
				else throw new \Exception("Invalid url request sent to application");
			}            
     
            /** If the application is not in test mode then the url request is handled */
            if (!$configuration['testing']['test_mode']) {
                /** If the save_test_data option is set to true then the page parameters are saved to test_data folder */
                if ($configuration['testing']['save_test_data']) {
                    /** The application test class object is fetched */
                    $application_test_class_obj = $components['testing'];
                    /** The application parameters are saved as test parameters */
                    $application_test_class_obj->SaveTestData();
                }
                /** If a controller is defined for the current url option then it is called */
                if (isset($configuration['general']['application_url_mappings'][$configuration['general']['option']]['controller'])) {
                    /** The controller function is run */
                    $response = $this->RunControllerFunction($configuration['general']['option']);
                    /** The response data is converted to json string and returned to browser */
                    $this->DisplayJsonResponse($response);
                }
                /** If no controller is defined for the current url option and a template is defined then the template is rendered and then displayed in the browser */
                else if (isset($configuration['general']['application_url_mappings'][$configuration['general']['option']]['templates'])) {
                    /** The application template is rendered with parameters generated by presentation class function */
                    $template_contents = $this->RenderApplicationTemplate($configuration['general']['option']);
                    /** The template contents are displayed */
                    $this->DisplayTemplateContents($template_contents);
                }
                /** If no controller and no template is defined for the current url option then an exception is thrown */
                else
                    throw new \Exception("No controller or template defined for the current url.");
            }
            /** If the application is in test mode then the testing function is called for the current test parameters */
            else {
                /** The application test class object is fetched */
                $application_test_class_obj = $components['testing'];
                
                /** If the application needs to be functional tested then all application urls are tested */
                if ($configuration['testing']['test_type'] == "functional")
                    $application_test_class_obj->RunFunctionalTests();
                /** If the application needs to be unit tested then only given test class with be tested with test parameters */
                else if ($configuration['testing']['test_type'] == "unit")
                    $application_test_class_obj->RunUnitTests();
                /** If some other test type is given then an exception is thrown */
                else
                    throw new \Exception("Invalid test type given in application configuration");
            }
           
    }    
    /**
     * Used to render the application template
     * 
     * It fetches the template object from application configuration
     * It then calls the Render function of the object
     * This function is defined in the base template object class
     * And may be overriden in the child template class
     * 
     * @since 1.0.0
     * @param string $option the url option		 
     * 
     * @return string $template_contents the contents of the template html		 
     */
    public function RenderApplicationTemplate($option)
    {        
        $template_contents = Configuration::GetComponent("template")->Render("root", array());
        return $template_contents;       
    }    
    /**
     * Used to call the url handling function of a controller class
     * 
     * It fetches the controller object information in application configuration
     * It then calls the function of the controller object and returns the response
     * 
     * @since 1.0.0
     * @param string $option the url option
     * 
     * @return array $response		 
     */
    public function RunControllerFunction($option)
    {        
        /** The application url mappings are fetched from application configuration */
        $application_url_mappings = Configuration::GetConfig('general','application_url_mappings');
        $controller_object_name   = $application_url_mappings[$option]['controller']['object_name'];
        $controller_object        = Configuration::GetComponent($controller_object_name);
        $function_name            = $application_url_mappings[$option]['controller']['function_name'];
        $response                 = $controller_object->$function_name();
        return $response;        
    }    
    /**
     * Used to create an encoded url for the application
     * 
     * It creates a url that includes small parameters that can be passed in the url
     * The parameters are first json encoded and then base64 encoded and then url encoded
     * 
     * @since 1.0.0
     * @param string $option the url option
     * @param array $parameters the list of url parameters. it is an associative array. if set to false then the parameters are not used
     * @return string $encoded_url the encoded url
     * @return boolean $is_link used to indicate if url will be used in link. if it will be used in link then url & will be
     * encoded so it is compatible with html5 validator
     * @throws Exception an object of type Exception is thrown if the encoded parameters size is larger than 100 characters
     */
    public function GetEncodedUrl($option, $parameters, $is_link)
    {
    	/** If the parameters are set then they are encoded */
    	if($parameters) {
    		 /** The url parameters are first json encoded */
             $encoded_parameters = json_encode($parameters);
             /** Then the parameters are base64 encoded */
             $encoded_parameters = base64_encode($encoded_parameters);
             /** Then the parameters are urlencoded */
             $encoded_parameters = urlencode($encoded_parameters);
			                
             /** An exception is thrown if encoded parameter length is larger then 150 characters */
             if (strlen($encoded_parameters) > 150)
                 throw new \Exception("The size of the encoded parameters must be less than 100 characters");
    	}
        
        /** The web application base url is fetched from application configuration */
        $web_application_url = Configuration::GetConfig("path","framework_url");
		/** The application name is fetched from the application configuration */   
        $module_name         = Configuration::GetConfig("general","module");
		/** The url parameters */
		$url_parameters      = array("option"=>$option,"module"=>$module_name);
		/** If the parameters were given then they are added to the url */
		if ($parameters)
		    $url_parameters['parameters'] = $encoded_parameters;
		
        /** The encoded url is created and returned */
        /** If the url will be used in an a tag link then the parameters are separate by &amp; */ 
        if ($is_link)
		    $separator = "&amp;";
		else
            $separator = "&";
	
		$encoded_url = $web_application_url . "?";
		foreach ($url_parameters as $key=>$value) {
			$encoded_url .= $key."=".$value.$separator;
		}
             
        $encoded_url = trim($encoded_url, $separator);            
        
        return $encoded_url;        
    }    
    /**
     * Used to decode url parameters
     * 
     * It decodes the url parameters
     * The parameters are first url decoded then base64 decoded and then json decoded
     * The resulting parameters are then stripped of html tags for security purpose
     * 
     * @since 1.0.0
     * @param $encoded_parameters $encoded_parameters the list of url parameters	 
     * 		 
     * @return array $decoded_parameters the list of decoded parameters
     */
    public function GetUrlParameters($encoded_parameters)
    {
        /** The encoded parameters are first url decoded */
        $encoded_parameters = urldecode($encoded_parameters);
        /** The parameters are then base64 decoded */
        $encoded_parameters = base64_decode($encoded_parameters);
        /** The parameters are then json decoded */
        $decoded_parameters = json_decode($encoded_parameters, true);
        
        $temp_decoded_parameters = array();
        
        /** The html tags are stripped from each url parameter */
        if (is_array($decoded_parameters)) {		
            foreach ($decoded_parameters as $key => $value) {
                $temp_decoded_parameters[strip_tags($key)] = $value;
            }
		}
		
        $decoded_parameters = $temp_decoded_parameters;
        
        /** The parameters are then returned */
        return $decoded_parameters;        
    }  
	  
    /**
     * Used to display the given template contents to the browser		 
     * 
     * It echoes the template contents		 
     * 
     * @since 1.0.0
     * @param string $template_contents the contents of the template file to be displayed
     */
    protected function DisplayTemplateContents($template_contents)
    {        
        echo $template_contents;       
    }    
	
    /**
     * Used to display response to json request
     * 
     * It echoes a json encoded response	 
     * 
     * @since 1.0.0
     * @param array $response the data to display as json		 
     */
    protected function DisplayJsonResponse($response)
    {        
        echo json_encode($response);       
    }
	    
	/**
     * Used to return the json response containing the given text
     * 
     * It returns json encoded string containing the given text
	 * The json string can be used as response to an ajax request 
     * 
     * @since 1.0.0
     * @param string $text the text that needs to be json encoded
	 * 
	 * @return string $response the json encoded response
     */
    protected function GetJsonResponse($text)
    {
        /** The response array */ 
        $response    = array("result"=>"success","text"=>$text);
		/** The response array is json encoded */
		$response    = json_encode($response);
		
		return $response;     
    }  
}