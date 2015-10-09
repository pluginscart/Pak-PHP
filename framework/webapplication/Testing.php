<?php

namespace Framework\WebApplication;

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
abstract class Testing
{
    /**
     * The single static instance
     */
    protected static $instance;
    
    /**
     * The number of valid assert statements in the test class
     */
    private $valid_assert_count = 0;
    
    /**
     * The number of invalid assert statements in the test class
     */
    private $invalid_assert_count = 0;
    
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
     * @return ApplicationTesting static::$instance name the instance of the correct child class is returned 
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
        $filesystem_obj          = \Framework\Utilities\UtilitiesFramework::Factory("filesystem");
        $is_browser_application  = Configuration::GetConfig("general","is_browser_application");
        $test_parameters         = Configuration::GetConfig("testing");
        
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
     * It saves the results of testing to the given file
     * 
     * @since 1.0.0		 
     * @param string $test_results results of testing		 		 
     */
    private function SaveTestResults($test_results)
    {
        /** The absolute path of the test results file */
        $test_configuration = Configuration::GetConfig("testing");
        $test_file_name     = $test_configuration['test_results_file'];
        /** The html is removed from the test results. The <br/> is replaced with new line */
        $test_results      = str_replace("<br/>", "\n", $test_results);
        $test_results      = strip_tags($test_results);
        /** The application test results are written to test file */
        Configuration::GetComponent("filesystem")->WriteLocalFile($test_results, $test_file_name);
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
    public function SaveTestData()
    {
        /** The application custom options */
        $custom_options          = Configuration::GetConfig('custom');
        /** The test parameters are fetched from application configuration */
        $configuration           = Configuration::GetConfig('testing');		
        /** The test data file path */
        /** If the test data folder path is not defined then an exception is thrown */
        if (!is_dir($configuration["test_data_folder"]))
            throw new \Exception("Invalid test data folder path");
        $test_data_file_path     = $configuration['test_data_folder'] . DIRECTORY_SEPARATOR . Configuration::GetConfig('general','option') . ".json";
        /** The application parameters are json encoded */
        $test_data               = Configuration::GetConfig('general');
		$test_data               = array("custom"=>$custom_options,"option"=>$test_data['option'],"parameters"=>$test_data['parameters'],"uploads"=>$test_data['uploads']);
		$test_data               = json_encode($test_data);		
        /** The application parameters are written to test file */
        Configuration::GetComponent("filesystem")->WriteLocalFile($test_data, $test_data_file_path);
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
    public function AssertTrue($expression)
    {
        if ($expression)
            $this->valid_assert_count++;
        else
            $this->invalid_assert_count++;
        
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
    public function AssertEqual($parameter1, $parameter2)
    {
        $is_valid = ($parameter1 == $parameter2);
        if ($is_valid)
            $this->valid_assert_count++;
        else
            $this->invalid_assert_count++;
        
        return $is_valid;
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
    public function RunUnitTests()
    {
        /** Utility object and configuration values are fetched from application configuration */
        $object_names        = array(
            "filesystem",
            "testing"
        );
        $configuration_names = array(
            "testing",
            "required_frameworks",
            "general"
        );
        list($components, $configuration) = Configuration::GetComponentsAndConfiguration($object_names, $configuration_names);
        /** The number of unit tests run */
        $test_count     = 0;
        /** The results of testing all functions*/
        $test_results   = "";
        /** The result of testing single function */
        $test_result    = "";
        /** The classes to be unit tested */
        $test_classes   = $configuration["testing"]['test_classes'];
        /** Start time for the unit tests */
        $start_time     = time();
        /** The function count is set */
        $function_count = 0;
        /** For each class all functions that start with Test are called */
        for ($count = 0; $count < count($test_classes); $count++) {
            $object_name   = $test_classes[$count];
            $class_name    = $configuration['required_frameworks'][$object_name]['class_name'];
            /** The class methods are fetched */
            $class_methods = get_class_methods($class_name);
            /** The class object is fetched from application configuration */
            $test_object   = Configuration::GetComponent($object_name);
            /** Each object function that starts with "Test" is called */
            for ($count = 0; $count < count($class_methods); $count++) {
                $class_function = $class_methods[$count];
                if (strpos($class_function, "Test") === 0) {
                    /** The testing callback function is defined */
                    $testing_callback = array(
                        $test_object,
                        $class_function
                    );
                    try {
                        $function_count++;
                        $current_assert_count = $this->valid_assert_count;
                        if (is_callable($testing_callback))
                            call_user_func($testing_callback);
                        else
                            throw new \Exception("Test function: " . $class_function . " does not exist in class: " . $class_name);
                        
                        if ($this->invalid_assert_count > 0)
                            throw new \Exception("Assert failed in function: " . $class_function);
                        
                        $test_results .= ($count + 1) . ") Testing function: " . $class_name . "::" . $class_function . ". result: passed. number of asserts: " . ($this->valid_assert_count - $current_assert_count) . $configuration['general']['line_break'];
                    }
                    catch (Exception $e) {
                        $errorhandler_obj = Configuration::GetComponent("errorhandler");
                        $errorhandler_obj->ExceptionHandler($e);
                    }
                }
            }
        }
        
        /** End time for the unit tests */
        $end_time = time();
        
        $test_results .= $configuration['general']['line_break'] . $configuration['general']['line_break'];
        $test_results .= "Result of unit testing: ";
        $test_results .= $configuration['general']['line_break'] . $configuration['general']['line_break'];
        $test_results .= "Number of functions tested: " . $function_count . $configuration['general']['line_break'];
        $test_results .= "Number of asserts: " . $this->valid_assert_count . $configuration['general']['line_break'];
        $test_results .= "Number of failed asserts: " . $this->invalid_assert_count . $configuration['general']['line_break'];
        $test_results .= "Time taken: " . ($end_time - $start_time) . " sec";
        
        echo $test_results;
        
        /** The results of testing are saved to file */
        /** If the test data folder path is not defined then an exception is thrown */
        if ($configuration['testing']["save_test_data"])
            $this->SaveTestResults($test_results);
    }
    
    /**
     * Used to test all the application urls
     * 
     * It tests each application url
     * For each url it calls the template functions or controller function for the url
     * The result of the testing		 
     * 
     * @since 1.0.0		 
     * @throws Exception an object of type Exception if an exception occured         
     */
    public function RunFunctionalTests()
    {
        /** Utility object and configuration values are fetched from application configuration */
        $object_names        = array(
            "filesystem",
            "testing"
        );
        $configuration_names = array(
            "testing",
            "general"            
        );
        list($components, $configuration) = Configuration::GetComponentsAndConfiguration($object_names, $configuration_names);
        
        /** The number of functional tests run */
        $test_count   = 0;
        /** The results of testing */
        $test_results = "";
        /** The result of testing single function */
        $test_result  = "";
        /** Start time for the funtional tests */
        $start_time   = time();
        foreach ($configuration['general']['application_url_mappings'] as $option => $option_data) {
            /** The full path to the test data file */
            $test_file_name = $option . ".json";
            /** If the test data folder path is not defined then an exception is thrown */
            if (!isset($configuration['testing']["test_data_folder"]))
                throw new \Exception("Invalid test data folder path");
            $test_data_file_path    = $configuration['testing']["test_data_folder"] . DIRECTORY_SEPARATOR . $test_file_name;
			/** If the test data file does not exist then an exception is thrown */
			if (!is_file($test_data_file_path))
                throw new \Exception("Invalid test data file path");
            /** The contents of the test data file are read */            
            $application_parameters = $components['filesystem']->ReadLocalFile($test_data_file_path);
            /** The test data is json decoded */
            $application_parameters = json_decode($application_parameters, true);					
            /** The test data is saved to application configuration */
            $updated_general_config = array_replace_recursive(Configuration::GetConfig("general"), $application_parameters);
            Configuration::SetConfig("general", "", $updated_general_config);
			          
			/** The test parameters are saved to application configuration */
			if (isset($application_parameters['parameters']))
                Configuration::SetConfig("general", "parameters", $application_parameters['parameters']);
            /** If a testing function is defined for the url then it is called before the function is tested */
            if (isset($option_data['testing'])) {
                /** If skip_testing configuration is set to true then the url is not tested */
                if ($option_data['testing']['skip_testing'])
                    continue;
                /** The testing function is run if the test object name and function name are not empty */
                else if ($option_data['testing']['object_name'] != "" && $option_data['testing']['function_name'] != "") {
                    /** The testing object is fetched from application configuration */
                    $testing_object = Configuration::GetComponent($option_data['testing']['object_name']);
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
            /** If a controller is defined for the current url option then it is called */
            if (isset($option_data['controller'])) {
                /** The controller function is run */
                $response    = Configuration::GetComponent("application")->RunControllerFunction($option);
                $test_result = $this->ValidateOutput("array", $response);
            }
            /** If no controller is defined for the current url option and a template is defined then the template is rendered and then displayed in the browser */
            else if (isset($option_data['templates'])) {
                /** The application template is rendered */
                $template_contents = Configuration::GetComponent("application")->RenderApplicationTemplate($option);				
                $test_result       = $this->ValidateOutput("html", $template_contents);				
            }
            /** If no controller and no template is defined for the current url option then an exception is thrown */
            else
                throw new \Exception("No controller or template defined for the current url.");
            
            if (!is_array($test_result) || (is_array($test_result) && $test_result['result']!='success'))
                throw new \Exception("Functional test for url: " . $option . " returned invalid response. Details: ".$test_result['message']);

			if ($this->invalid_assert_count > 0)
                throw new \Exception("Assert failed in function: " . $class_function);
                        
       		$test_results .= ($test_count + 1) . ") Testing url: " . $option . " result: passed" . $configuration['general']['line_break'];
            $test_count++;
        }
        
        /** End time for the unit tests */
        $end_time = time();
        
        $test_results .= $configuration['general']['line_break'];
        $test_results .= "Result of functional testing: ";
        $test_results .= $configuration['general']['line_break'] . $configuration['general']['line_break'];
        $test_results .= "Number of functions tested: " . $test_count . $configuration['general']['line_break'];
        $test_results .= "Number of asserts: " . $this->valid_assert_count . $configuration['general']['line_break'];
        $test_results .= "Number of failed asserts: " . $this->invalid_assert_count . $configuration['general']['line_break'];
        $test_results .= "Time taken: " . ($end_time - $start_time) . " sec";
        
        echo $test_results;
        /** The results of testing are saved to file */
        if ($configuration['testing']["save_test_results"])
            $this->SaveTestResults($test_results);
    }
}