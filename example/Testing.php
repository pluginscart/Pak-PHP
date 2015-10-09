<?php

namespace Example;

/**
 * This class implements the unit tests for the Example application
 * It extends the BrowserApplicationTest class
 * 
 * It is used to test the Example application
 * It tests the framework utility libraries
 * 
 * @category   WebApplication
 * @package    Example
 * @author     Nadir Latif <nadir@pakjiddat.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2
 * @version    1.0.0
 * @link       N.A
 */
class Testing extends \Framework\WebApplication\Testing
{
    /** 
     * Authentication function test
     * Used to test http digest authentication		 
     */
    public function TestAuthentication()
    {
        /** List of valid user credentials. used to test the http digest authentication **/
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
        /** The custom text to use in the authentication box that shows in the browser **/
        $authentication_box_title = "Protected Area!";
        /** The authentication object is fetched **/
        $authentication           = Configuration::GetComponent("authentication");
        /** 
         * If the user presses the cancel button then the function returns false
         * If the user entered the wrong credentials then he will be asked to login again
         */
        $cancel_pressed=(!$authentication->AuthenticateUser($credentials, $authentication_box_title));            
        $this->AssertTrue(true);
    }
    
    /** 
     * Caching function test
     * Used to test function caching
     */
    public function TestCaching()
    {
        $database_obj        = Configuration::GetComponent("database");
        $db_link             = $database_obj->df_get_id();
        /** The prefix of the table. e.g if prefix name is example_ then table name is example_cached_data **/
        $table_prefix        = "example_";
        /** The caching object is fetched with given parameters **/
        $caching_parameters  = array(
            "db_link" => $database_obj->df_get_id(),
            "table_prefix" => $table_prefix
        );
        $caching_obj         = Configuration::GetComponent("caching");
		/** The db link is set so the data can be cached to database **/
		$caching_obj->SetDbLink($db_link);
        /** The data is saved to cache **/
        $caching_obj->SaveDataToCache("TestFunction", array(
            "parameter 1",
            "parameter 2"
        ), "test data");
        /** The data is fetched from cache **/
        $cached_data = $caching_obj->GetCachedData("TestFunction", array(
            "parameter 1",
            "parameter 2"
        ),
		true);
		
        $this->AssertEqual($cached_data,"test data");
    }
    
    /**
     * Encryption testing
     * Used to test encryption and decryption of text
     */
    public function TestEncryption()
    {
        /** The encryption object is fetched **/
        $encryption_obj = Configuration::GetComponent("encryption");
        /** The text to be encrypted **/
        $original_text  = "test encryption";
        /** The original text is encrypted **/
        $encrypted_text = $encryption_obj->EncryptText($original_text);
        /** The encrypted text is decrypted **/
        $decrypted_text = $encryption_obj->DecryptText($encrypted_text);
        /** If the original text matches the decrypted text then following message is shown **/
        $this->AssertEqual($original_text,$decrypted_text);            
    }
    
    /**
     * Database testing
     * Used to test database abstraction class
     */
    public function TestDatabase()
    {

        $database_obj        = Configuration::GetComponent("database");
        /** The $database_obj is initialized and cleared **/
        $database_obj->df_initialize();
        /** The select query is built **/
        $main_query               = array();
        $main_query[0]['field']   = "*";
        /** The where clause of the query is built **/
        $where_clause             = array();
        $where_clause[0]['field'] = "function_name";
        $where_clause[0]['value'] = "TestFunction";
        $where_clause[0]['table'] = "example_cached_data";
        
        $query   = $database_obj->df_build_query($main_query, $where_clause, 's');
        $db_rows = $database_obj->df_all_rows($query);
		
        $this->AssertEqual($db_rows[0]['function_name'],"TestFunction");     
    }
    
    /**
     * Email testing
     * Used to test email function
     * It support html content and attachments
     */
    public function TestEmail()
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
        
        $email_obj = Configuration::GetComponent("email");
        $is_sent   = $email_obj->SendEmail(array(
            "test.xls"
        ), $from_email, $to_email, "Utilitiesframework Test", "<h3>testing html content</h3>");
		
        $this->AssertTrue($is_sent);
    }
    
    /**
     * String function testing
     * Used to test relative to absolute conversion function
     */
    public function TestString()
    {
        /** The String class object is fetched **/
        $string_obj = Configuration::GetComponent("string");
        $main_url   = "https://pear.php.net/manual/en/";
        $rel_url    = "package.mail.mail.send.php";
        /** The relative link is converted to absolute link **/
        $abs_url    = $string_obj->ConvertRelUrlToAbsUrl($main_url, $rel_url);        
        $this->AssertEqual($abs_url,$main_url.$rel_url);
    }

    /**
     * Template function testing
     * Used to test template rendering
     */
    public function TestTemplate()
    {
        /** The Template class object is fetched */
        $template_obj = Configuration::GetComponent("template");
		$template_path = realpath(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."framework".DIRECTORY_SEPARATOR."utilities".DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR."example.html";  
		$tag_replacement_arr = array(array("title"=>"Page title","body"=>"Body title"));
		/** The example template file is rendered */
        $template_file_contents    = $template_obj->RenderTemplateFile($template_path, $tag_replacement_arr);
        $this->AssertTrue(strpos($template_file_contents,"Page title")!==false);
    }
}