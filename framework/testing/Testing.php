<?php

namespace Framework\Testing;

use \Framework\Configuration\Base as Base;

/**
 * This class implements the base ApplicationTest class 
 * 
 * It contains functions that help in testing applications
 * The class is abstract and must be inherited by the application test class
 * 
 * @category   Framework
 * @package    Testing
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    Release: 1.0.0
 * @link       N.A
 */
class Testing extends Base
{    
    /**
     * The number of valid assert statements in the test class
     */
    private $valid_assert_count = 0;
    
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
    final function ValidateOutput($output_type, $output)
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
    final function ValidateArray($output)
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
    final function ValidateJson($output_string)
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
     * It uses the validation service given in application configuration file
     * 
     * @since 1.0.0		 
     * @param $string $html_content the html string to be validated
     * 
     * @return $array $validation_results the results of validation. the array contains 2 keys. result=> success or error
     * and message=> response returned by the validation service
     */
    final private function ValidateHTML($html_content)
    {
        $filesystem_obj         = \Framework\Utilities\UtilitiesFramework::Factory("filesystem");
        $is_browser_application = $this->GetConfig("general", "is_browser_application");
        $test_parameters        = $this->GetConfig("testing");
        
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
        
        $validation_results = $filesystem_obj->GetFileContent($validator_url, "POST", $content, $headers);
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
    
    /**
     * Used to save the test results to test folder
     * 
     * It saves the results of testing to file given in application configuration
	 * If the test results file does not exist
	 * Then the function returns without saving the test results
     * 
     * @since 1.0.0		 
     * @param string $test_results results of testing		 		 
     */
    final private function SaveTestResults($test_results)
    {
        /** The absolute path of the test results file */
        $test_configuration = $this->GetConfig("testing");
        $test_file_name     = $test_configuration['test_results_file'];
		/** If the test results file does not exist */
		if (!is_file($test_file_name)) return;
        /** The html is removed from the test results. The <br/> is replaced with new line */
        $test_results       = str_replace("<br/>", "\n", $test_results);
        $test_results       = strip_tags($test_results);
        /** The application test results are written to test file */
        $this->GetComponent("filesystem")->WriteLocalFile($test_results, $test_file_name);
    }
    
    /**
     * Used to save the test data to test folder
     * 
     * It fetches the application parameters from application configuration
     * It saves the application parameters to the test data folder defined
     * In application configuration
     * 
     * @since 1.0.0
     */
    final public function SaveTestData()
    {
        /** The test data file path */
        /** If the test data folder path is not defined then an exception is thrown */
        if (!is_dir($this->GetConfig("testing", "test_data_folder")))
            throw new \Exception("Invalid test data folder path");
        $test_data_file_path = $this->GetConfig("testing", "test_data_folder") . DIRECTORY_SEPARATOR . $this->GetConfig('general', 'option') . "_test_data.json";
        /** The application parameters are fetched */
        $test_data           = $this->GetConfig('general', 'parameters');
        $test_data           = array(
            $test_data
        );
        /** The test data is read from local file */
        $current_test_data   = $this->GetComponent("filesystem")->ReadLocalFile($test_data_file_path);
        /** Used to indicate if the test data exists in the test data file */
        $test_data_exists    = false;
        /**
         * If the current test data file has contents then it is json decoded
         * If the current test data file does not have the application parameters
         * Then it is appended to current application parameters
         */
        if ($current_test_data != "") {
            $current_test_data = json_decode($current_test_data, true);
            /** Each of the test data elements are checked */
            for ($count = 0; $count < count($current_test_data); $count++) {
                /** Used to indicate that test data exists */
                $test_data_exists  = true;
                /** Test data element */
                $test_data_element = $current_test_data[$count];
                /** Each item in the test data element is checked */
                foreach ($test_data_element as $key => $value) {
                    /** If the test data item key does not exist or it exists but the value is not equal to the value of the current test data */
                    if ((!isset($test_data[0][$key])) || (isset($test_data[0][$key]) && $test_data[0][$key] != $value)) {
                        $test_data_exists = false;
                        break;
                    }
                }
                if ($test_data_exists)
                    break;
            }
            if (!$test_data_exists) {
                $test_data = array_merge($current_test_data, $test_data);
            }
        }
        /**
         * The application parameters are json encoded		 
         */
        $test_data = json_encode($test_data);
        /** If the test data is not empty then the application parameters are written to test data file */
        if ($test_data != "") {
            $this->GetComponent("filesystem")->WriteLocalFile($test_data, $test_data_file_path);
        }
    }
    
    /**
     * Used to check if given expression is true
     * 
     * If the function parameter is true then the valid assert count is increased
     * Otherwise the invalid assert count is increased
     * 
     * @since 1.0.0
     * @param boolean $expression an expression that is either true or false
     */
    final public function AssertTrue($expression)
    {
        if ($expression)
            $this->valid_assert_count++;
        else
            throw new \Exception("Failed to assert that false is true");
        
        return $expression;
    }
    
    /**
     * Used to check if given parameters are equal
     * 
     * It compares value of given function parameters
     * 
     * @since 1.0.0
     * @param boolean $is_valid indicates if the parameters are equal
     */
    final public function AssertEqual($parameter1, $parameter2)
    {
        $is_valid = ($parameter1 == $parameter2);
        if ($is_valid)
            $this->valid_assert_count++;
        else
            throw new \Exception("Failed to assert that " . $parameter1 . " is equal to " . $parameter2);
        
        return $is_valid;
    }
    
    /**
     * Used to call the given script
     * 
     * It calls the relevant function of the given script
     * The script name is given in application configuration
     * 
     * @since 1.0.0     
     */
    final public function CallScript()
    {
        /** The number of unit tests run */
        $test_count        = 0;
        /** The results of testing all functions*/
        $test_results      = $this->GetConfig('general', 'line_break') . $this->GetConfig('general', 'line_break');
        /** The result of testing single function */
        $test_result       = "";
        /** The script to be called */
        $script_class_name = $this->GetConfig('testing', 'test_classes', '0');
        /** Start time for the script */
        $start_time        = time();
        /** The class object is fetched from application configuration */
        $script_object     = $this->GetComponent($script_class_name);
        /** The script option given in application configuration */
        $script_option     = $this->GetConfig("general", "parameters", "option");
        /** The script function to call */
        $class_function    = $this->GetComponent("string")->CamelCase($script_option);
        /** The script callback function is defined */
        $script_callback   = array(
            $script_object,
            $class_function
        );
        
        try {
            $current_assert_count = $this->valid_assert_count;
            if (is_callable($script_callback))
                call_user_func($script_callback);
            else
                throw new \Exception("Script function: " . $class_function . " does not exist in class: " . $script_object);
        }
        catch (Exception $e) {
            $errorhandler_obj = $this->GetComponent("errorhandler");
            $errorhandler_obj->ExceptionHandler($e);
        }
        
        /** End time for the script execution */
        $end_time = time();
        
        $test_results .= $this->GetConfig('general', 'line_break') . $this->GetConfig('general', 'line_break');
        $test_results .= "Result of script execution: ";
        $test_results .= $this->GetConfig('general', 'line_break') . $this->GetConfig('general', 'line_break');
        $test_results .= "Time taken: " . ($end_time - $start_time) . " sec" . $this->GetConfig('general', 'line_break') . $this->GetConfig('general', 'line_break');
        
        echo $test_results;
        
        /** The results of testing are saved to file */
        /** If the test data folder path is not defined then an exception is thrown */
        if ($this->GetConfig('testing', 'save_test_results'))
            $this->SaveTestResults($test_results);
    }
    
    /**
     * Used to test functions in test classes
     * 
     * It tests all the classes given in appliation configuration
     * Only functions that start with "Test" will be run
     * Each function that is tested should return an array with 2 keys:
     * result=> the result of the test. i.e success or error
     * message=> the message from the function. e.g error message
     * 
     * @since 1.0.0		 
     * @throws Exception an object of type Exception if an exception occured
     */
    final public function RunUnitTests()
    {
        /** The number of unit tests run */
        $test_count   = 0;
        /** The results of testing all functions*/
        $test_results = $this->GetConfig('general', 'line_break');
        /** The result of testing single function */
        $test_result  = "";
        /** The classes to be unit tested */
        $test_classes = $this->GetConfig('testing', 'test_classes');
        /** Start time for the unit tests */
        $start_time   = time();
        /** For each class all functions that start with Test are called */
        for ($count = 0; $count < count($test_classes); $count++) {
            $object_name        = $test_classes[$count];
            /** The required frameworks configuration */
            $require_frameworks = $this->GetConfig('required_frameworks');
            $class_name         = $require_frameworks[$object_name]['class_name'];
            /** The class methods are fetched */
            $class_methods      = get_class_methods($class_name);
            /** The class object is fetched from application configuration */
            $test_object        = $this->GetComponent($object_name);
            /** Each object function that starts with "Test" is called */
            for ($count1 = 0; $count1 < count($class_methods); $count1++) {
                $class_function = $class_methods[$count1];
                if (strpos($class_function, "Test") === 0) {
                    /** The testing callback function is defined */
                    $testing_callback = array(
                        $test_object,
                        $class_function
                    );
                    try {
                        /** The test count is increased by 1 */
                        $test_count++;
                        /** The current assert count */
                        $current_assert_count = $this->valid_assert_count;
                        /** If the callback function is callable then it is called with parameters in test data file */
                        if (is_callable($testing_callback)) {
                            /** The test data */
                            $test_data           = $this->LoadTestData($class_function);
                            /** The number of test cases of the test function */
                            $test_cases          = 0;
                            /** The test function is called for each parameter in test data file */
                            for ($count2 = 0; $count2 < count($test_data); $count2++) {
                                call_user_func_array($testing_callback, $test_data);
                                $test_cases++;
                            }
                        } else
                            throw new \Exception("Test function: " . $class_function . " does not exist in class: " . $class_name);
                        /** The test results are updated */
                        $test_results .= ($test_count) . ") Testing function: " . $class_name . "::" . $class_function .
                        $this->GetConfig('general', 'line_break')."Result: passed".
                        $this->GetConfig('general', 'line_break')."Number of test cases: ".$test_cases.
                        $this->GetConfig('general', 'line_break')."Number of asserts: " . ($this->valid_assert_count - $current_assert_count).
                        $this->GetConfig('general', 'line_break').
                        $this->GetConfig('general', 'line_break');
                    }
                    catch (Exception $e) {
                        $errorhandler_obj = $this->GetComponent("errorhandler");
                        $errorhandler_obj->ExceptionHandler($e);
                    }
                }
            }
        }
        
        /** End time for the unit tests */
        $end_time = time();
        
        $test_results .= $this->GetConfig('general', 'line_break') . $this->GetConfig('general', 'line_break');
        $test_results .= "Result of unit testing: ";
        $test_results .= $this->GetConfig('general', 'line_break') . $this->GetConfig('general', 'line_break');
        $test_results .= "Number of functions tested: " . $test_count . $this->GetConfig('general', 'line_break');
        $test_results .= "Number of asserts: " . $this->valid_assert_count . $this->GetConfig('general', 'line_break');
        $test_results .= "Time taken: " . ($end_time - $start_time) . " sec" . $this->GetConfig('general', 'line_break') . $this->GetConfig('general', 'line_break');
        
        echo $test_results;
        
        /** The results of testing are saved to file */
        /** If the test data folder path is not defined then an exception is thrown */
        if ($this->GetConfig('testing', 'save_test_results'))
            $this->SaveTestResults($test_results);
    }
    
    /**
     * Used to test all the application urls
     * 
     * It tests each application url
     * For each url it calls the template functions or controller function for the url   
     * 
     * @since 1.0.0		 
     * @throws Exception an object of type Exception if an exception occured         
     */
    final public function RunFunctionalTests()
    {
        /** The number of functional tests run */
        $test_count   = 0;
        /** The results of testing */
        $test_results = "";
        /** The result of testing single function */
        $test_result  = "";
        /** Start time for the funtional tests */
        $start_time   = time();
        foreach ($this->GetConfig('general', 'application_url_mappings') as $option => $option_data) {
            /** If a testing function is defined for the url then it is called before the function is tested */
            if (isset($option_data['testing'])) {
                /** If skip_testing configuration is set to true then the url is not tested */
                if ($option_data['testing']['skip_testing'])
                    continue;
                /** The testing function is run if the test object name and function name are not empty */
                else if ($option_data['testing']['object_name'] != "" && $option_data['testing']['function_name'] != "") {
                    /** The testing object is fetched from application configuration */
                    $testing_object = $this->GetComponent($option_data['testing']['object_name']);
                    $function_name  = $option_data['testing']['function_name'];
                    
                    /** The testing callback function is defined */
                    $testing_callback = array(
                        $testing_object,
                        $function_name
                    );
                    
                    $current_assert_count = $this->valid_assert_count;
                    if (is_callable($testing_callback))
                        call_user_func($testing_callback);
                    else
                        throw new \Exception("Testing function : " . $function_name . " was not found for test object: " . $option_data['testing']['object_name']);
                    
                }
            }
            /** The full path to the test data file */
            $test_file_name = $option . ".json";
            /** the testing configuration parameters */
            $testing        = $this->GetConfig('testing');
            /** If the test data folder path is not defined then an exception is thrown */
            if (!isset($testing['test_data_folder']))
                throw new \Exception("Invalid test data folder path");
            $test_data_file_path = $this->GetConfig('testing', 'test_data_folder') . DIRECTORY_SEPARATOR . $test_file_name;
            /** If the test data file does not exist then an exception is thrown */
            if (!is_file($test_data_file_path))
                throw new \Exception("Invalid test data file path");
            /** The contents of the test data file are read */
            $application_parameters = $this->GetComponent('filesystem')->ReadLocalFile($test_data_file_path);
            /** The test data is json decoded */
            $application_parameters = json_decode($application_parameters, true);
            /** The test data is saved to application configuration */
            $updated_general_config = array_replace_recursive($this->GetConfig("general"), $application_parameters);
            $this->SetConfig("general", "", $updated_general_config);
            
            /** If a controller is defined for the current url option then it is called */
            if (isset($option_data['controller'])) {
                /** The controller function is run */
                $response    = $this->GetComponent("application")->RunControllerFunction($option);
                $test_result = $this->ValidateOutput("array", $response);
            }
            /** If no controller is defined for the current url option and a template is defined then the template is rendered and then displayed in the browser */
            else if (isset($option_data['templates'])) {
                /** The application template is rendered */
                $template_contents = $this->GetComponent("application")->RenderApplicationTemplate($option);
                $test_result       = $this->ValidateOutput("html", $template_contents);
            }
            /** If no controller and no template is defined for the current url option then an exception is thrown */
            else
                throw new \Exception("No controller or template defined for the current url.");
            
            if (!is_array($test_result) || (is_array($test_result) && $test_result['result'] != 'success'))
                throw new \Exception("Functional test for url: " . $option . " returned invalid response. Details: " . $test_result['message']);
            
            $test_results .= ($test_count + 1) . ") Testing url: " . $option . " result: passed" . $this->GetConfig('general', 'line_break');
            $test_count++;
        }
        
        /** End time for the unit tests */
        $end_time = time();
        
        $test_results .= $this->GetConfig('general', 'line_break');
        $test_results .= "Result of functional testing: ";
        $test_results .= $this->GetConfig('general', 'line_break') . $this->GetConfig('general', 'line_break');
        $test_results .= "Number of functions tested: " . $test_count . $this->GetConfig('general', 'line_break');
        $test_results .= "Number of asserts: " . $this->valid_assert_count . $this->GetConfig('general', 'line_break');
        $test_results .= "Time taken: " . ($end_time - $start_time) . " sec" . $this->GetConfig('general', 'line_break') . $this->GetConfig('general', 'line_break');
        
        echo $test_results;
        /** The results of testing are saved to file */
        if ($this->GetConfig('testing', 'save_test_results'))
            $this->SaveTestResults($test_results);
    }
    
    /**     
     * This function provides test data for testing the given function
	 * It may be overriden by child classes
	 * It reads test data from the function's test data file
	 * 
     * It reads the test data file. contents of file should be in json format
     * It decodes the json text
     * The decoded text is returned
     * 
     * @since    1.0.0
     * @param string $function_name the name of the function to be tested
     * @throws object \Exception an exception is thrown if the given test data file does not exist
	 *  
     * @return $test_data the test data contents
     */
    protected function LoadTestData($function_name)
    {
    	/** The test data */
    	$test_data                          = array();
    	/** The test data folder */
    	$test_data_folder                   = $this->GetConfig("testing", "test_data_folder");
		/** If the test data folder is not defined then empty test data is returned */
		if (!is_dir($test_data_folder))
		    return $test_data;
		
    	/** The list of all test data files */
		$test_file_list                     = scandir($test_data_folder);
		/** Current test file name */
		$current_test_file_name             = str_replace("Test", "", $function_name);
		/** For each test data file the function name is compared with test file name */
		for ($count = 0; $count < count($test_file_list); $count++) {
			$test_file_name                 = $test_file_list[$count];
			$temp_test_file_name            = str_replace("_test_data.json","",$test_file_name);
			if ($temp_test_file_name == $current_test_file_name) break;
		}		
        /** The test file is read */
        $test_file_name                     = $this->GetConfig("testing", "test_data_folder") . DIRECTORY_SEPARATOR . $test_file_name;
		/** If the test data file does not exist then an exception is thrown */
		if (!is_file($test_file_name))
		    throw new \Exception("The test data file: ".$test_file_name." does not exist");
		
        $test_data                          = $this->GetComponent("filesystem")->ReadLocalFile($test_file_name);
        /** The test data is json decoded */
        $test_data                          = json_decode($test_data, true);
        /** The test data is returned */
        return $test_data;
    }
    
    /**
     * Used to insert the given html into a html5 template
     * 
     * The given html is added to the html5 template
     * The output of this function should be valid html5
     * This function can be used to validate html5 content
     * 
     * @since 1.0.0
     * @param string $html the html to be added to the html5 template
     * @param string $template_name the name of the template object to use. it must support the base_page option
     * 
     * @return string $page_html the html string of the complete page
     */
    final public function InsertHtmlToTemplate($html, $template_object_name)
    {
        /** The parameters for the test page */
        $parameters = array(
            "title" => "Base Page",
            "css" => array(),
            "javascript" => array(),
            "body" => $html
        );
        /** The test page is rendered with the given parameters */
        $page_html  = $this->GetComponent($template_object_name)->Render("base_page", $parameters);
        
        return $page_html;
    }
}
