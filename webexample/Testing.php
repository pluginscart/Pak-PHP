<?php

namespace WebExample;

/**
 * This class implements the unit tests for the WebExample application
 * It extends the Testing class
 * 
 * It is used to test the WebExample application
 * It tests the framework utility libraries
 * 
 * @category   WebExample
 * @package    Testing
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0
 * @since      1.0.0
 */
class Testing extends \Framework\Testing\Testing
{
    /** 
     * Authentication function test
     * Used to test http digest authentication
	 * 
	 * @since 1.0.0
	 * @param array $test_data the test data 
     */
    public function TestAuthentication($test_data)
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
        $authentication           = $this->GetComponent("authentication");
        /** 
         * If the user presses the cancel button then the function returns false
         * If the user entered the wrong credentials then he will be asked to login again
         */
        $cancel_pressed=(!$authentication->AuthenticateUser($credentials, $authentication_box_title));            
        $this->AssertTrue(true, "Authentication test passed successfully");
    }
    
    /** 
     * Caching function test
     * Used to test function caching
	 * 
	 * @since 1.0.0
	 * @param array $test_data the test data 
     */
    public function TestCaching($test_data)
    {
        $database_obj        = $this->GetComponent("frameworkdatabase");
        $db_link             = $database_obj->df_get_id();
        /** The name of the table */
        $table_name          = "cached_data";
        /** The caching object is fetched with given parameters */
        $caching_parameters  = array(
            "db_link" => $database_obj->df_get_id(),
            "table_name" => $table_name
        );
        $caching_obj         = $this->GetComponent("caching");
		/** The db link is set so the data can be cached to database */
		$caching_obj->SetDbLink($db_link);
        /** The data is saved to cache */
        $caching_obj->SaveDataToCache("TestFunction", array(
            "parameter 1",
            "parameter 2"
        ), "test data");
        /** The data is fetched from cache */
        $cached_data = $caching_obj->GetCachedData("TestFunction", array(
            "parameter 1",
            "parameter 2"
        ),
		true);
		
        $this->AssertEqual($cached_data, "test data", "Checks if data fetched from cache is same as data saved to cache");
    }
    
    /**
     * Encryption testing
     * Used to test encryption and decryption of text
	 *
	 * @since 1.0.0
	 * @param array $test_data the test data 
     */
    public function TestEncryption($test_data)
    {
        /** The encryption object is fetched */
        $encryption_obj = $this->GetComponent("encryption");
        /** The text to be encrypted */
        $original_text  = "test encryption";
        /** The original text is encrypted */
        $encrypted_text = $encryption_obj->EncryptText($original_text);
        /** The encrypted text is decrypted */
        $decrypted_text = $encryption_obj->DecryptText($encrypted_text);
        /** If the original text matches the decrypted text then following message is shown */
        $this->AssertEqual($original_text, $decrypted_text, "Checks if the original text is equal to decrypted text");            
    }
    
    /**
     * Database testing
     * Used to test database abstraction class
	 * 
	 * @since 1.0.0
	 * @param array $test_data the test data 
     */
    public function TestDatabase($test_data)
    {
        $database_obj             = $this->GetComponent("frameworkdatabase");
        /** The $database_obj is initialized and cleared */
        $database_obj->df_initialize();
        /** The select query is built */
        $main_query               = array();
        $main_query[0]['field']   = "*";
        /** The where clause of the query is built */
        $where_clause             = array();
        $where_clause[0]['field'] = "function_name";
        $where_clause[0]['value'] = "TestFunction";
        $where_clause[0]['table'] = "cached_data";
        
        $query   = $database_obj->df_build_query($main_query, $where_clause, 's');
        $db_rows = $database_obj->df_all_rows($query);
		
        $this->AssertEqual($db_rows[0]['function_name'], "TestFunction", "Checks if data was correctly fetched from database");     
    }
    
    /**
     * Email testing
     * Used to test email function
     * It support html content and attachments
	 * 
	 * @since 1.0.0
	 * @param array $test_data the test data 
     */
    public function TestEmail($test_data)
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
        
        $email_obj = $this->GetComponent("email");
        $is_sent   = $email_obj->SendEmail(array(
            "test.xls"
        ), $from_email, $to_email, "Utilitiesframework Test", "<h3>testing html content</h3>");
		
        $this->AssertTrue($is_sent, "Checks if email was successfully sent");
    }
    
    /**
     * String function testing
     * Used to test relative to absolute conversion function
	 * 
	 * @since 1.0.0
	 * @param array $test_data the test data 
     */
    public function TestString($test_data)
    {
        /** The String class object is fetched **/
        $string_obj = $this->GetComponent("string");
        $main_url   = "https://pear.php.net/manual/en/";
        $rel_url    = "package.mail.mail.send.php";
        /** The relative link is converted to absolute link **/
        $abs_url    = $string_obj->ConvertRelUrlToAbsUrl($main_url, $rel_url);        
        $this->AssertEqual($abs_url, $main_url.$rel_url, "Checks if the absolute url was correctly generated");
    }

    /**
     * Template function testing
     * Used to test template rendering
	 * 
	 * @since 1.0.0
	 * @param array $test_data the test data 
     */
    public function TestTemplate($test_data)
    {
        /** The Template class object is fetched */
        $template_obj = $this->GetComponent("template_helper");
		$template_path = realpath(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."framework".DIRECTORY_SEPARATOR."utilities".DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR."example.html";  
		$tag_replacement_arr = array(array("title"=>"Page title","body"=>"Body title"));
		/** The example template file is rendered */
        $template_file_contents    = $template_obj->RenderTemplateFile($template_path, $tag_replacement_arr);
        $this->AssertTrue(strpos($template_file_contents, "Page title")!==false, "Checks if the 'Page title' text is present in the example template file");
    }
}