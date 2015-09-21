<?php

namespace TestingFramework;

use \ApplicationConfigurationFramework\ApplicationConfiguration as ApplicationConfiguration;
use \TestingFramework\ApplicationTesting as ApplicationTesting;

/**
 * This class implements the base BrowserApplicationTest class 
 * 
 * It contains functions that help in testing browser based applications
 * The class is abstract and must be inherited by the application test class
 * 
 * @category   ApplicationTesting
 * @package    TestingFramework
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    Release: 1.0.0
 * @link       N.A
 */
abstract class BrowserApplicationTesting extends ApplicationTesting
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
     * @return BrowserApplicationTesting static::$instance name the instance of the correct child class is returned 
     */
    public static function GetInstance()
    {
        if (static::$instance == null) {
            static::$instance = new static();
        }
        return static::$instance;
    }
    
    /**
     * Used to validate the given url output 
     * 
     * It validates the given output produced by the application for a given url
     * The output is e.g html,array or json encoded string
     * It checks the type of the output and then call the relavant validation function
     * 
     * @since 1.0.0
     * @param string $output_type the type of output. e.g json or html		 
     * @param string $output the output of the application
     * 
     * @return $array $validation_results the results of validation. the array contains 2 keys. result=> success or error
     * and message=> response returned by validation function
     */
    function ValidateOutput($output_type, $output)
    {
        if ($output_type == "html")
            $validation_results = $this->ValidateHTML($output);
        else if ($output_type == "json")
            $validation_results = $this->ValidateJson($output);
        else if ($output_type == "array")
            $validation_results = $this->ValidateArray($output);
        
        return $validation_results;
    }
    
    /**
     * Used to validate the given array
     * 
     * It checks if given array has valid data
     * The array has valid data if it has a key called result which is equal to success		 
     * 
     * @since 1.0.0
     * @param array $output the array to validate
     * 
     * @return $array $validation_results the results of validation. the array contains 2 keys. error=> success or error
     * and message=> error message contained in json string message property
     */
    function ValidateArray($output)
    {
        if (isset($output['result']) && $output['result'] == 'success')
            $validation_results = array(
                "result" => "success",
                "message" => ""
            );
        else
            $validation_results = array(
                "result" => "error",
                "message" => $output['message']
            );
        
        return $validation_results;
    }
    
    /**
     * Used to validate the given json string
     * 
     * It checks if given string is valid json
     * It also validates json object
     * Json object is valid if it has a key called result which is equal to success		 
     * 
     * @since 1.0.0
     * @param string $output_string the json string to validate
     * 
     * @return $array $validation_results the results of validation. the array contains 2 keys. result=> success or error
     * and message=> error message contained in json string message property
     */
    function ValidateJson($output_string)
    {
        $output_arr = json_decode($output_string, true);
        if (isset($output_arr['result']) && $output_arr['result'] == 'success')
            $validation_results = array(
                "result" => "success",
                "message" => ""
            );
        else
            $validation_results = array(
                "result" => "error",
                "message" => $output_arr['message']
            );
        
        return $validation_results;
    }
    
    /**
     * Used to validate the html of a component using the w3c validation service
     * 
     * It validates the given html string and returns the response from the validation service	 
     * 
     * @since 1.0.0		 
     * @param $string $html_content the html string to be validated
     * 
     * @return $array $validation_results the results of validation. the array contains 2 keys. result=> success or error
     * and message=> response returned by w3c validation service
     */
    private function ValidateHTML($html_content)
    {
        $utilities_obj          = ApplicationConfiguration::GetComponent("utilities");
        $is_browser_application = ApplicationConfiguration::GetConfig("is_browser_application");
        $test_parameters        = ApplicationConfiguration::GetConfig("testing");
        
        $html_content = str_replace("\r", "", $html_content);
        $html_content = str_replace("\n", "", $html_content);
        
        $validator_url = $test_parameters['validator_url'];
        $output_format = ($is_browser_application) ? "html" : "text";
        $show_source   = ($is_browser_application) ? "yes" : "no";
        
        $content = array(
            "parser" => "html5",
            "out" => $output_format,
            "showsource" => $show_source,
            "asciiquotes" => "yes",
            "content" => $html_content
        );
        
        $headers = array(
            "Content-type: multipart/form-data; boundary=---------------------------" . strlen($html_content)
        );
        
        $validation_results = $utilities_obj->GetFileContent($validator_url, "POST", $content, $headers);
        if ($is_browser_application)
            $validation_results = str_replace("style.css", $validator_url . "style.css", $validation_results);
        
        if (strpos($validation_results, "There were errors.") !== false)
            $result = 'error';
        else
            $result = 'success';
        
        $validation_results = array(
            "result" => $result,
            "message" => $validation_results
        );
        
        return $validation_results;
    }
}