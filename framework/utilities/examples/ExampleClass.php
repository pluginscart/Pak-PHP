<?php

namespace Framework\Utilities;

final class ExampleClass
{
    
    /** 
     * Authentication function test
     * Used to test http digest authentication		 
     */
    public function AuthenticationTest()
    {
        /** List of valid user credentials. used to test the http digest authentication */
        $credentials              = array(
            array(
                "user_name" => "admin",
                "password" => "admin"
            ),
            array(
                "user_name" => "manager",
                "password" => "manager"
            )
        );
        /** The custom text to use in the authentication box that shows in the browser */
        $authentication_box_title = "Protected Area!";
        /** The authentication object is fetched */
        $authentication           = UtilitiesFramework::Factory("authentication");
        /** 
         * If the user presses the cancel button then following message is shown
         * If the user entered the wrong credentials then he will be asked to login again
         */
        if (!$authentication->AuthenticateUser($credentials, $authentication_box_title))
            echo "You pressed the cancel button!.";
        /** If the user entered the correct login information then the following message is shown */
        else
            echo "You entered the correct login information!.";
    }
    
    /** 
     * Caching function test
     * Used to test function caching
     */
    public function CachingTest()
    {
        /** The database object is fetched */
        $database_parameters = array(
            "host" => "localhost",
            "user" => "nadir",
            "password" => "kcbW5eFSCbPXbJGLHvUGG8T8",
            "database" => "dev_pakphp",
            "debug" => "1"
        );
        $database_obj        = UtilitiesFramework::Factory("database", $database_parameters);
        $db_link             = $database_obj->df_get_id();
        /** The prefix of the table. e.g if prefix name is example_ then table name is example_cached_data */
        $table_prefix        = "example_";
        /** The caching object is fetched with given parameters */
        $caching_parameters  = array(
            "db_link" => $database_obj->df_get_id(),
            "table_prefix" => $table_prefix
        );
        $caching_obj         = UtilitiesFramework::Factory("caching", $caching_parameters);
        /** The data is saved to cache */
        $caching_obj->SaveDataToCache("TestFunction", array(
            "parameter 1",
            "parameter 2"
        ), "test data");
        /** The data is fetched from cache */
        $cached_data = $caching_obj->GetCachedData("TestFunction", array(
            "parameter 1",
            "parameter 2"
		), true);
        print_R($cached_data);
    }
    
    /**
     * Encryption testing
     * Used to test encryption and decryption of text
     */
    public function EncryptionTest()
    {
        /** The encryption object is fetched */
        $encryption_obj = UtilitiesFramework::Factory("encryption");
        /** The text to be encrypted */
        $original_text  = "test encryption";
        /** The original text is encrypted */
        $encrypted_text = $encryption_obj->EncryptText($original_text);
        /** The encrypted text is decrypted */
        $decrypted_text = $encryption_obj->DecryptText($encrypted_text);
        /** If the original text matches the decrypted text then following message is shown */
        if ($original_text == $decrypted_text)
            echo "Text sucessfully decrypted";
        else
            echo "Text could not be decrypted";
    }
    
    /**
     * Database testing
     * Used to test database abstraction class
     */
    public function DatabaseTest()
    {
        /** The database object is fetched */
        $database_parameters = array(
            "host" => "localhost",
            "user" => "nadir",
            "password" => "kcbW5eFSCbPXbJGLHvUGG8T8",
            "database" => "dev_pakphp",
            "debug" => "1"
        );
        $database_obj        = UtilitiesFramework::Factory("database", $database_parameters);
        /** The $database_obj is initialized and cleared */
        $database_obj->df_initialize();
        /** The select query is built */
        $main_query               = array();
        $main_query[0]['field']   = "*";
        /** The where clause of the query is built */
        $where_clause             = array();
        $where_clause[0]['field'] = "function_name";
        $where_clause[0]['value'] = "TestFunction";
        $where_clause[0]['table'] = "example_cached_data";
        
        $query   = $database_obj->df_build_query($main_query, $where_clause, 's');
        $db_rows = $database_obj->df_all_rows($query);
        print_R($db_rows);
    }
    
    /**
     * Excel file testing
     * Used to test excel utility object functions
     */
    public function ExcelTest()
    {
        /** The PhpExcel library is included */
        $excel_file_path = "!Enter the path to the PHPExcel IOFactory.php file!";
		if(!is_file($excel_file_path)) {
			echo "Please enter the path to the PHPExcel IOFactory.php file!";
			return;
		}
        include_once($excel_file_path);
        /** The Excel class object is fetched */
        $excel_obj = UtilitiesFramework::Factory("excel");
        $data_arr  = $excel_obj->ReadExcelFile("test.xls", "A2", "C13");
        print_R($data_arr);
    }
    
    /**
     * Email testing
     * Used to test email function
     * It support html content and attachments
     */
    public function EmailTest()
    {
        /** 
         * The Email class object is fetched 
         * The Email class requires Mail and Mail_Mime pear package
         * Change the from and to emails to your email address
         */
        include_once("Mail.php");
        include_once("Mail/mime.php");
        
        $from_email = "nadir@pakjiddat.com";
        $to_email   = "nadir@pakjiddat.com";
        
        $email_obj = UtilitiesFramework::Factory("email");
        $is_sent   = $email_obj->SendEmail(array(
            "test.xls"
        ), $from_email, $to_email, "Utilitiesframework Test", "<h3>testing html content</h3>");
        if ($is_sent)
            echo "Email was successfully sent";
        else
            echo "Email could not be sent";
    }
    
    /**
     * Error handling test
     * Used to test error handling
     */
    public function ErrorHandlingTest()
    {
        /** The parameters from ErrorHandler object */
        $parameters                           = array();
        /** Custom shutdown function. It is automatically called just before script exits */
        $parameters['shutdown_function']      = array(
            $this,
            "CustomShutdown"
        );
		/** Used to indicate that application is being run from browser */
		$parameters['is_browser_application'] = true;
        /** Used to indicate if the error message should be displayed */
        $parameters['development_mode']       = true;
        /** The email address that will get the error message email */
        $parameters['email']                  = array("email_address"=>"nadir@pakjiddat.com","email_header"=>"From: admin@pakjiddat.com\r\nSubject: Test email");
        /** The error log file name */
        $parameters['log_file_name']          = "";
        /** Custom error handling function */
        $parameters['custom_error_handler']   = array(
            $this,
            "CustomErrorHandler"
        );
        
        /** The ErrorHandling class object is fetched */
        $errorhandling_obj                    = UtilitiesFramework::Factory("errorhandler", $parameters);
        /** Throw an exception for testing the error handling */
        throw new \Exception("Test exception!", 10);
    }
    
    /**
     * FileSystem testing
     * Used to test file system functions
     * It validates given html string using the W3C validator
     * It sends certain parameters as http post to the validator
     */
    public function FileSystemTest()
    {
        $is_browser_application = true;
        $html_content           = '<!DOCTYPE html>
								<html>
								<head>
								<meta charset="UTF-8">
								<title>Title of the document</title>
								</head>
								
								<body>
								Content of the document......
								</body>
							</html>';
        
        $html_content = str_replace("\r", "", $html_content);
        $html_content = str_replace("\n", "", $html_content);
        
        $validator_url = "https://html5.validator.nu/";
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
        
        $errorhandling_obj = UtilitiesFramework::Factory("filesystem");
        
        $validation_results = $errorhandling_obj->GetFileContent($validator_url, "POST", $content, $headers);
        if ($is_browser_application)
            $validation_results = str_replace("style.css", $validator_url . "style.css", $validation_results);
        
        print_R($validation_results);
    }
    
    /**
     * String function testing
     * Used to test relative to absolute conversion function
     */
    public function StringTest()
    {
        /** The String class object is fetched */
        $string_obj = UtilitiesFramework::Factory("string");
        $main_url   = "https://pear.php.net/manual/en/";
        $rel_url    = "package.mail.mail.send.php";
        $abs_url    = $string_obj->ConvertRelUrlToAbsUrl($main_url, $rel_url);
        print_R($abs_url);
    }
    
    /**
     * Custom error handling function
     * Its automatically called when there is an uncaught error or exception
     * 
     * @param $log_message the error or exception message formatted by the ErrorHandling class. it contains the entire error stack trace including function parameters
     * @param $error_parameters the error parameters. they can be used to create custom error message.
     * It is an array with following keys: "error_level","error_message","error_file","error_line",
     * "error_context","error_type".
     */
    public function CustomErrorHandler($log_message, $error_parameters)
    {
        echo "Custom error log message: <br/><br/>" . $log_message;
        echo "<br/><br/>Error parameters: <br/><br/>" . var_export($error_parameters, true);
    }
    
    /**
     * Custom shutdown function
     * Its automatically called when the script exits
     */
    public function CustomShutdown()
    {
        echo "Custom shudown function. Script has ended!";
    }
    
	/**
     * Template function testing
     * Used to test template class
     */
    public function TemplateTest()
    {
        /** The Template class object is fetched */
        $template_obj = UtilitiesFramework::Factory("template");
		$template_path = "templates".DIRECTORY_SEPARATOR."example.html";  
		$tag_replacement_arr = array(array("title"=>"Page title","body"=>"Body title"));
		/** The example template file is rendered */
        $template_file_contents    = $template_obj->RenderTemplateFile($template_path, $tag_replacement_arr);
        print_R($template_file_contents);
    }
}
?>