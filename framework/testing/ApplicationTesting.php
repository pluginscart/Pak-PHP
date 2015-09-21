<?php

namespace Framework\Testing;
use Framework\ApplicationConfiguration\ApplicationConfiguration as ApplicationConfiguration;
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
abstract class ApplicationTesting
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
     * The output is e.g html or json encoded string
     * The child class that implements this function should check the type of the output
     * And then call the relavant validation function
     * 
     * @since 1.0.0
     * @param string $output_type the type of output. e.g json or html		 
     * @param string $output_string the output of the application
     * @return boolean $is_valid used to indicate if the output is valid or not 
     */
    abstract function ValidateOutput($output_type, $output_string);
    
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
        /** The absolute path of the test results file **/
        $test_configuration = ApplicationConfiguration::GetConfig("testing");
        $test_file_name     = $test_configuration['test_results_file'];
        
        /** The application test results are written to test file **/
        ApplicationConfiguration::GetComponent("filesystem")->WriteLocalFile($test_results, $test_file_name);
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
        /** The application option **/
        $option          = ApplicationConfiguration::GetConfig('option');
        /** The test parameters are fetched from application configuration **/
        $test_parameters = ApplicationConfiguration::GetConfig('testing');
        /** The test data file path **/
        /** If the test data folder path is not defined then an exception is thrown **/
        if (!isset($configuration['testing']["test_data_folder"]))
            throw new \Exception("Invalid test data folder path");
        $test_data_file_path = $test_parameters['test_data_folder'] . DIRECTORY_SEPARATOR . $option . ".json";
        /** The application parameters are json encoded **/
        $test_data           = json_encode(ApplicationConfiguration::GetConfig('parameters'));
        /** The application parameters are written to test file **/
        ApplicationConfiguration::GetComponent("filesystem")->WriteLocalFile($test_data, $test_data_file_path);
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
        /** Utility object and configuration values are fetched from application configuration **/
        $object_names        = array(
            "filesystem",
            "testing"
        );
        $configuration_names = array(
            "testing",
            "required_frameworks",
            "line_break"
        );
        list($components, $configuration) = ApplicationConfiguration::GetComponentsAndConfiguration($object_names, $configuration_names);
        /** The number of unit tests run **/
        $test_count     = 0;
        /** The results of testing all functions**/
        $test_results   = "";
        /** The result of testing single function **/
        $test_result    = "";
        /** The classes to be unit tested **/
        $test_classes   = $configuration["testing"]['test_classes'];
        /** Start time for the unit tests **/
        $start_time     = time();
        /** The function count is set **/
        $function_count = 0;
        /** For each class all functions that start with Test are called **/
        for ($count = 0; $count < count($test_classes); $count++) {
            $object_name   = $test_classes[$count];
            $class_name    = $configuration['required_frameworks'][$object_name]['class_name'];
            /** The class methods are fetched **/
            $class_methods = get_class_methods($class_name);
            /** The class object is fetched from application configuration **/
            $test_object   = ApplicationConfiguration::GetComponent($object_name);
            /** Each object function that starts with "Test" is called **/
            for ($count = 0; $count < count($class_methods); $count++) {
                $class_function = $class_methods[$count];
                if (strpos($class_function, "Test") === 0) {
                    /** The testing callback function is defined **/
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
                        
                        $test_results .= ($count + 1) . ") Testing function: " . $class_name . "::" . $class_function . ". result: passed. number of asserts: " . ($this->valid_assert_count - $current_assert_count) . $configuration['line_break'];
                    }
                    catch (Exception $e) {
                        $errorhandler_obj = ApplicationConfiguration::GetComponent("errorhandler");
                        $errorhandler_obj->ExceptionHandler($e);
                    }
                }
            }
        }
        
        /** End time for the unit tests **/
        $end_time = time();
        
        $test_results .= $configuration['line_break'] . $configuration['line_break'];
        $test_results .= "Result of unit testing: ";
        $test_results .= $configuration['line_break'] . $configuration['line_break'];
        $test_results .= "Number of functions tested: " . $function_count . $configuration['line_break'];
        $test_results .= "Number of asserts: " . $this->valid_assert_count . $configuration['line_break'];
        $test_results .= "Number of failed asserts: " . $this->invalid_assert_count . $configuration['line_break'];
        $test_results .= "Time taken: " . ($end_time - $start_time) . " sec";
        
        echo $test_results;
        
        /** The results of testing are saved to file **/
        /** If the test data folder path is not defined then an exception is thrown **/
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
        /** Utility object and configuration values are fetched from application configuration **/
        $object_names        = array(
            "filesystem",
            "testing"
        );
        $configuration_names = array(
            "testing",
            "line_break",
            "application_url_mappings"
        );
        list($components, $configuration) = ApplicationConfiguration::GetComponentsAndConfiguration($object_names, $configuration_names);
        
        /** The number of functional tests run **/
        $test_count   = 0;
        /** The results of testing **/
        $test_results = "";
        /** The result of testing single function **/
        $test_result  = "";
        /** Start time for the unit tests **/
        $start_time   = time();
        foreach ($configuration['application_url_mappings'] as $option => $option_data) {
            /** The test data is saved to application configuration so it can be used by the application **/
            /** The full path to the test data file **/
            $test_file_name = $option . ".json";
            /** If the test data folder path is not defined then an exception is thrown **/
            if (!isset($configuration['testing']["test_data_folder"]))
                throw new \Exception("Invalid test data folder path");
            $test_data_file_path    = $configuration['testing']["test_data_folder"] . DIRECTORY_SEPARATOR . $test_file_name;
            /** The contents of the test data file are read **/
            $application_parameters = $components['filesystem']->ReadLocalFile($test_data_file_path);
            /** The test data is json decoded **/
            $application_parameters = json_decode($application_parameters, true);
            /** The test option is saved to application configuration **/
            ApplicationConfiguration::SetConfig("option", $option);
            /** The test parameters are saved to application configuration **/
            ApplicationConfiguration::SetConfig("parameters", $application_parameters);
            /** If the url option specifies files that need to be included then the files are included **/
            if (isset($option_data['include_files'])) {
                /** All files that need to be included are included **/
                for ($count = 0; $count < count($option_data['include_files']); $count++) {
                    $file_name = $option_data['include_files'][$count];
                    if (is_file($file_name))
                        require_once($file_name);
                    else
                        throw new \Exception("Invalid include file name: " . $file_name . " given for page option: " . $option, 60);
                }
            }
            /** If a testing function is defined for the url then it is called before the function is tested **/
            if (isset($option_data['testing'])) {
                /** If skip_testing configuration is set to true then the url is not tested **/
                if ($option_data['testing']['skip_testing'])
                    continue;
                /** The testing function is run if the test object name and function name are not empty **/
                else if ($option_data['testing']['object_name'] != "" && $option_data['testing']['function_name'] != "") {
                    /** The testing object is fetched from application configuration **/
                    $testing_object = ApplicationConfiguration::GetComponent($option_data['testing']['object_name']);
                    $function_name  = $option_data['testing']['function_name'];
                    
                    /** The testing callback function is defined **/
                    $testing_callback = array(
                        $testing_object,
                        $function_name
                    );
                    
                    try {
                        $current_assert_count = $this->valid_assert_count;
                        if (is_callable($testing_callback))
                            call_user_func($testing_callback);
                        else
                            throw new \Exception("Testing function : " . $function_name . " was not found for test object: " . $option_data['testing']['object_name']);
                        
                        if ($this->invalid_assert_count > 0)
                            throw new \Exception("Assert failed in function: " . $class_function);
                        
                        $test_result .= ($count + 1) . ") Testing function: " . $option_data['testing']['object_name'] . "::" . $class_function . ". result: passed. " . ($this->valid_assert_count - $current_assert_count) . " valid asserts" . $configuration['line_break'];
                        echo $test_result;
                        flush();
                        $test_results .= $test_result;
                    }
                    catch (Exception $e) {
                        $errorhandler_obj = ApplicationConfiguration::GetComponent("errorhandler");
                        $errorhandler_obj->ExceptionHandler($e);
                    }
                }
            }
            /** If a controller is defined for the current url option then it is called **/
            if (isset($option_data['controller'])) {
                /** The controller function is run **/
                $response    = ApplicationConfiguration::GetComponent("application")->RunControllerFunction($option);
                $test_result = $this->ValidateOutput("array", $response);
            }
            /** If no controller is defined for the current url option and a template is defined then the template is rendered and then displayed in the browser **/
            else if (isset($option_data['templates'])) {
                /** The application template is rendered **/
                $template_contents = ApplicationConfiguration::GetComponent("application")->RenderApplicationTemplate($option);
                $test_result       = $this->ValidateOutput("html", $template_contents);
            }
            /** If no controller and no template is defined for the current url option then an exception is thrown **/
            else
                throw new \Exception("No controller or template defined for the current url.", 60);
            
            if (!is_array($test_result))
                throw new \Exception("Functional test for url: " . $option . " returned invalid response", 60);
            /** If the result of testing a function is error then the function displays an error message **/
            if ($test_result['result'] == 'error')
                $test_result = "URL: " . $option . ". -> Error. Details: " . $test_result['message'] . $configuration['line_break'];
            /** Otherwise the function displays not error message **/
            else
                $test_result = "URL: " . $option . " -> No Errors!" . $configuration['line_break'];
        }
        
        /** End time for the unit tests **/
        $end_time = time();
        
        $test_results .= $configuration['line_break'] . $configuration['line_break'];
        $test_results .= "Result of unit testing: ";
        $test_results .= $configuration['line_break'] . $configuration['line_break'];
        $test_results .= "Number of functions tested: " . $function_count . $configuration['line_break'];
        $test_results .= "Number of asserts: " . $this->valid_assert_count . $configuration['line_break'];
        $test_results .= "Number of failed asserts: " . $this->invalid_assert_count . $configuration['line_break'];
        $test_results .= "Time taken: " . ($end_time - $start_time) . " sec";
        
        echo $test_results;
        /** The results of testing are saved to file **/
        if ($configuration['testing']["save_test_data"])
            $this->SaveTestResults($test_results);
    }
}